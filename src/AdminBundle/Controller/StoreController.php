<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Coupon;
use AppBundle\Entity\Store;
use AdminBundle\Form\StoreType;

/**
 * @Route("/store")
 */
class StoreController extends PageController
{
    /**
     * @Route("/", name="admin_store_index")
     * @Template()
     */
    public function indexAction()
    {
        $store_list = $this->getDoctrine()->getRepository("AppBundle:Store")->findAllByName();

        return [
            'store_list' => $store_list,
        ];
    }

    /**
     * Create new store
     *
     * @param  Request  $request
     * 
     * @return Template
     * @author Michael Strohyi
     *
     * @Route("/new", name="admin_store_create")
     * @Template()
     **/
    public function createAction(Request $request)
    {
        $store = new Store;
        $form = $this->createStoreForm($store, $request);

        if ($form->isValid()) {
            $this->persistStore($store);

            return $this->redirectToRoute("admin_store_index");
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Edit store
     *
     * @param  Store  $store
     * @param  Request  $request
     * 
     * @return Template
     * @author Michael Strohyi
     *
     * @Route("/{id}/edit", name="admin_store_edit", requirements={"id": "\d+"})
     * @ParamConverter("store", class="AppBundle:Store")
     * @Template()
     **/
    public function editAction(Store $store, Request $request)
    {
        $form = $this->createStoreForm($store, $request);

        if ($form->isValid()) {
            $this->handleLogo($store, $request);
            $this->persistStore($store);

            return $this->redirectToRoute("admin_store_index");
        }

        return [
            'store' => $store,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete given store
     *
     * @param  Store  $store
     * @param  Request  $request
     * 
     * @return Template
     * @author Michael Strohyi
     *
     * @Route("/{id}/delete", name="admin_store_delete")
     * @ParamConverter("store", class="AppBundle:Store")
     * @Template()
     **/
    public function deleteAction(Store $store, Request $request)
    {
        $form = $this->createFormBuilder([])->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity_manager = $this->getDoctrine()->getEntityManager();
            $store_id = $store->getId();
            # delete store
            $entity_manager->remove($store);
            $entity_manager->flush();
            # delete urls for current store from db and from menu-items
            $this->deleteFromMenus(Store::PAGE_TYPE, $store, $store_id);
            $this->deletePageUrls(Store::PAGE_TYPE, $store, $store_id);

            return $this->redirectToRoute("admin_store_index");
        }

        return [
            'store' => $store,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/autoupdate/{type}", name="admin_store_autoupdate",
     *     requirements={"type": "all|new"},
     *     defaults={"type": "new"},
     * ))
     * @Template()
     */
    public function autoupdateAction($type)
    {
        ini_set('MAX_EXECUTION_TIME', 3000);
        $url = $this->getParameter('feeds_url');
        $coupon_repo = $this->getDoctrine()->getRepository("AppBundle:Coupon");
//        $url = "c:\appserv\www\api\api_json.txt";
        switch ($type) {
            case 'all':
                $api_data = file_get_contents($url);
                $coupon_repo->removeAutoupdatedCoupons();
                $coupon_repo->insertCouponsFromFeed($this->grabCouponsFromApi($api_data));
                break;
            case 'new':
                $url .= "&incremental=1";
                $api_data = file_get_contents($url);
                $coupon_repo->updateCouponsFromFeed($this->grabCouponsFromApi($api_data));
                break;
        }

        return [
            'type' => $type,
        ];
    }

    /**
     * Save given store into database
     *
     * @param  Store  $store
     * 
     * @return void
     * @author Michael Strohyi
     **/
    private function persistStore(Store $store)
    {
        $entity_manager = $this->getDoctrine()->getEntityManager();
        # get old store url from db
        $old_url = $entity_manager->getRepository('AppBundle:Store')->getUrlFromDB($store);
        # save store into db
        $entity_manager->persist($store);
        $entity_manager->flush();
        # add/update store url in database
        $this->updatePageUrls(Store::PAGE_TYPE, $store);

        # update store url in menus
        $entity_manager->getRepository('AppBundle:MenuItem')->updateUrls($old_url, $store->getUrl());
    }

     /**
     * Return form for create/edit store
     *
     * @param Store $store
     * @param Request $request
     *
     * @return Form
     * @author Michael Strohyi
     **/
    private function createStoreForm(Store $store, Request $request)
    {
        $form = $this->createForm(new StoreType(), $store);
        $form->handleRequest($request);

        return $form;
    }

    /**
     * Remove store logo if crrent logo was deleted and new logo was not selected
     *
     * @param Store $store
     * @param Request $request
     *
     * @return void
     * @author Michael Strohyi
     **/
    private function handleLogo(Store $store, Request $request)
    {
        if (null === $request->request->get('current_logo') && (null === $store->getLogo() || null === $store->getLogo()->getImageFile())) {
            $store->removeLogo();
        }
    }
    /**
     * Convert string in JSON-format into array of coupons
     *
     * @param string $api_data
     *
     * @return array
     * @author Michael Strohyi
     **/
    private function grabCouponsFromApi($api_data)
    {
        $data = json_decode($api_data);
        if (empty($data)) {
            return [];
        }

        $coupons = [];
        $i = 0;
        foreach ($data as $key => $value) {
            if ($i > 40000) {
                break;
            }

            $coupon = get_object_vars($value);
            $i++;
            $cur_coupon = [
                'id' => $coupon['nCouponID'],
                'label' => $coupon['cLabel'],
                'code' => $coupon['cCode'],
                'link' => $coupon['cAffiliateURL'],
                'starts' => $coupon['dtStartDate'],
                'expires' => $coupon['dtEndDate'],
                'discount' => Coupon::findMaxDiscount($coupon['cLabel']),
                'status' => $coupon['cStatus'],
            ];
            $coupons[$coupon['nMerchantID']][] = $cur_coupon;
        }

        return $coupons;
    }
}
