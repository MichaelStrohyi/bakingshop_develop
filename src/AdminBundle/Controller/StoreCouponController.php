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
}
