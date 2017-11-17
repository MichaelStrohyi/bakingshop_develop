<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Store;
use AdminBundle\Form\StoreCouponsType;

/**
 * @Route("/store/{id}/coupons", requirements={"id": "\d+"})
 * @ParamConverter("store", class="AppBundle:Store")
 */
class StoreCouponController extends Controller
{ 
    /**
     * Display all coupons in the given store
     *
     * @param  Store  $store
     * @param  Request  $request
     * 
     * @return Template
     * @author Michael Strohyi
     *
     * @Route("/", name="admin_store_coupons")
     * @Template()
     */
    public function indexAction(Store $store, Request $request)
    {
        $form = $this->createCouponsForm($store, $request);

        if ($form->isValid()) {
            $this->handleLogo($store, $request);
            $this->persistItems($store);

            return $this->redirectToRoute("admin_store_index");
        }

        return [
            'store' => $store,
            'form' => $form->createView(),
        ];
    }

    /**
     * Create form for manipulating store coupons
     *
     * @param  Store  $store
     * @param  Request  $request
     * 
     * @return Form
     *
     * @author Michael Strohyi
     **/
    private function createCouponsForm(Store $store, Request $request)
    {
        $form = $this->createForm(new StoreCouponsType, $store);
        $form->handleRequest($request);

        return $form;
    }

    /**
     * Persist all store coupons
     *
     * @param  Store  $store
     * 
     * @return void
     * 
     * @author Michael Strohyi
     **/
    private function persistItems(Store $store)
    {
        $entity_manager = $this->getDoctrine()->getEntityManager();

        $entity_manager->persist($store);
        $entity_manager->flush();
    }

    /**
     * Remove logo for each coupon if crrent logo was deleted and new logo was not selected
     *
     * @param Store $store
     * @param Request $request
     *
     * @return void
     * @author Michael Strohyi
     **/
    private function handleLogo(Store $store, Request $request)
    {
        $current_logo = $request->request->get('current_logo');

        foreach ($store->getCoupons() as $value) {
            if ((empty($current_logo) || !array_key_exists($value->getId(), $current_logo)) && (null === $value->getLogo() || null === $value->getLogo()->getImageFile())) {
                    $value->removeLogo();
            }
        }
    }
}
