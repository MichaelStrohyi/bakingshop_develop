<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use AppBundle\Entity\Store;

class StoreController extends Controller
{
    /**
     * @Route("/{prefix}store/{id}/coupons", name="store_coupons_page",
     *     requirements={"id": "\d+", "prefix": "amp/|"},
     *     defaults={"prefix": ""},
     * )
     * @ParamConverter("store", class="AppBundle:Store")
     */
    public function couponsAction(Store $store, Request $request, $prefix = null)
    {
        $parameters = [
            'store' => $store,
            'crosslink' => $this->generateUrl('homepage', [], true)  . $this->getDoctrine()->getRepository("USPCPageBundle:Page")->createCrossLink($prefix, $this->container->getParameter('amp_prefix'), $request->getPathInfo()),
            'menus' => $this->getDoctrine()->getRepository('AppBundle:Menu')->findAllbyName(),
        ];

        # if prefix is not set render html page else render amp-html page
        return empty($prefix) ? $this->render('AppBundle:Store:coupons.html.twig', $parameters) : $this->render('AppBundle:amp/Store:coupons.html.twig', $parameters);
    }
}
