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
    public function couponsAction(Store $store, Request $request)
    {
        $route_params = $request->attributes->get('_route_params');
        $prefix = $route_params['prefix'];
        $amp_prefix = $this->container->getParameter('amp_prefix');
        $path = $request->getPathInfo();
        
        # create crosslink to link apm-html page with html page
        if  (!empty($prefix)) {
            $path = substr($path, strlen($prefix));
            $crosslink = $this->generateUrl('homepage', [], true) . ltrim($path, '/');
        } else {
            $crosslink = $this->generateUrl('homepage', [], true) . trim($amp_prefix, '/') . $path;
        }

        $menus = $this->getDoctrine()->getRepository('AppBundle:Menu')->findAllbyName();
        $parameters = [
            'store' => $store,
            'crosslink' =>$crosslink,
            'menus' => $menus,
        ];

        # if prefix is not set render html page else render amp-html page
        return empty($prefix) ? $this->render('AppBundle:Store:coupons.html.twig', $parameters) : $this->render('AppBundle:amp/Store:coupons.html.twig', $parameters);
    }
}
