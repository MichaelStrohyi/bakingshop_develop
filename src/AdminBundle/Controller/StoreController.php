<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Coupon;
use AppBundle\Entity\Store;
use AppBundle\Entity\StoreCoupon;
use AdminBundle\Form\StoreType;

/**
 * @Route("/store")
 */
class StoreController extends PageController
{
    const COUPONS_LIMIT = 200;

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
     *     requirements={"type": "all|new|job"},
     *     defaults={"type": "new"},
     * ))
     * @Template()
     */
    public function autoupdateAction($type)
    {
        ini_set('MAX_EXECUTION_TIME', 540);
        if ($type == "job") {
            $date = new \DateTimeImmutable();
            $hours = $date->format('H');
            if ($hours % 12 == 0) {
                $type = "all";
            } else {
                $type = "new";
            }
        }

        $url = $this->getParameter('feeds_url');
        $coupon_repo = $this->getDoctrine()->getRepository("AppBundle:Coupon");
        switch ($type) {
            case 'all':
                $feed_data = file_get_contents($url);
                $this->fetchCouponsFromFeed($feed_data);
                break;
            case 'new':
                $url .= "&incremental=1";
                $feed_data = file_get_contents($url);
                $this->fetchCouponsFromFeed($feed_data, true);
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
     * Fetch coupons from the given JSON-string and save them into db
     *
     * @param string $feed_data
     * @param boolean $incr_mode
     *
     * @return void
     * @author Michael Strohyi
     **/
    private function fetchCouponsFromFeed($feed_data, $incr_mode = false)
    {
        $data = json_decode($feed_data);
        if (empty($data)) {
            return;
        }

        $feed_coupons = [];
        $i = 0;
        foreach ($data as $key => $value) {
            if (!is_object($value)) {
                continue;
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
            $feed_coupons[$coupon['nMerchantID']][] = $cur_coupon;
        }

       if (empty($feed_coupons)) {
            return;
        }

        $doctrine = $this->getDoctrine();
        $em = $doctrine->getEntityManager();
        $coupon_repo = $doctrine->getRepository("AppBundle:Coupon");
        $operator_repo = $doctrine->getRepository("AppBundle:Operator");
        $operators =  $operator_repo->getAllOperators();
        $stores_list = [];
        foreach ($feed_coupons as $feed_store_id => $feed_store_coupons) {
            $store = $doctrine->getRepository("AppBundle:Store")->getStoreByFeedId($feed_store_id);
            if (empty($store)) {
                continue;
            }

            $stores_list[] = $store->getId();
            $coupons_updated = false;
            $coupons_list = [];
            $last_code_pos = $store->getLastCodePosition();
            $last_coupon_offset = count($store->getCoupons()) - 1 - $last_code_pos;
            $coupons_count = 0;
            foreach ($feed_store_coupons as $feed_coupon) {
                if (++$coupons_count > self::COUPONS_LIMIT) {
                    break;
                }
                $store_coupon = $store->findCouponByFeedId($feed_coupon['id']);
                if (strtolower($feed_coupon['status']) != "active") {
                    if (!empty($store_coupon)) {
                        $store->removeCoupon($store_coupon);
                        $coupons_updated = true;
                        $em->remove($store_coupon);
                    }

                    continue;
                }

                $code_exists = !empty($feed_coupon['code']) ? $store->findCouponByCode($feed_coupon['code'], $feed_coupon['id']) : null;
                if (!empty($code_exists)) {
                    if (!empty($store_coupon)) {
                        $store->removeCoupon($store_coupon);
                        $coupons_updated = true;
                        $em->remove($store_coupon);
                    }
                    continue;
                }

                if (empty($store_coupon)) {
                    $store_coupon = new StoreCoupon();
                    $store_coupon
                        ->setFeedId($feed_coupon['id'])
                        ->setStore($store)
                        ->setAddedBy($operator_repo->getRandomItem($operators))
                    ;
                }

                $store_coupon
                    ->setLabel($feed_coupon['label'])
                    ->setCode($feed_coupon['code'])
//                    ->setLink($feed_coupon['link'])
                    ->setLink($store->getLink())
                    ->setStartDate($this->convertDateFromFeed($feed_coupon['starts']))
                    ->setExpireDate($this->convertDateFromFeed($feed_coupon['expires']))
                    ->setJustVerified()
                    ->setMaxDiscount()
                ;

                if (empty($store_coupon->getId())) {
                    $cur_pos = empty($feed_coupon['code']) ? ++$last_coupon_offset + $last_code_pos : ++$last_code_pos;
                    $store->insertCouponOnPosition($store_coupon, $cur_pos);
                }

                $coupons_updated = true;
                $coupons_list[] = $feed_coupon['id'];
            }

            $coupons_updated = !$incr_mode && $store->removeFeedCoupons($coupons_list) !== false ? true : $coupons_updated; //!!!
            if ($coupons_updated) {
                $store->actualiseCouponsPosition(self::COUPONS_LIMIT);
                $em->persist($store);
                $em->flush();
            }
        }

        if (!$incr_mode) {
            $coupon_repo->removeFeedCoupons($stores_list);
        }
    }

    /**
     * Convert given $date string into DateTime object to store in db
     *
     * @param string $date
     *
     * @return DateTime|null
     * @author Michael Strohyi
     **/
    private function convertDateFromFeed($date)
    {
        try {
            $date = new \DateTime($date);
            $cur_date = new \DateTimeImmutable();

            return $date->format("Y") > $cur_date->format("Y") + 3 || $date->format("Y") < $cur_date->format("Y") - 3 ? null : $date;
        } catch (\Exception $e) {
            return null;
        }
    }
}
