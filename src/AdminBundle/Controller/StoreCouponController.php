<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Store;

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
        return [
        ];
    }
}
