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
        $amp_flag = $request->query->get('a');

        $parameters = [
            'store' => $store,
            'crosslink' => $this->generateUrl('homepage', [], true)  . $this->getDoctrine()->getRepository("USPCPageBundle:Page")->createCrossLink($prefix, $this->container->getParameter('amp_prefix'), $request->getPathInfo()),
            'menus' => $this->getDoctrine()->getRepository('AppBundle:Menu')->findAllbyName(),
        ];

        # if prefix is not set render html page
        if (empty($prefix)) {
            return $this->render('AppBundle:Store:coupons.html.twig', $parameters);
        }

        # if amp_flag is set render amplified html page otherwise render amp-html page
        return ($amp_flag == 'a') ? $this->render('AppBundle:amplified/Store:coupons.html.twig', $parameters) : $this->render('AppBundle:amp/Store:coupons.html.twig', $parameters);
    }

    /**
     * @Route("/{prefix}stores", name="store_list_page",
     *     requirements={"prefix": "amp/|"},
     *     defaults={"prefix": ""},
     * )
     */
    public function listAction($prefix = null, Request $request)
    {
        $parameters = [
            'stores' => $this->getDoctrine()->getRepository('AppBundle:Store')->findAllByName(),
            'type' => 'store',
            'type_title' => 'Stores',
            'crosslink' => $this->generateUrl('homepage', [], true)  . $this->getDoctrine()->getRepository("USPCPageBundle:Page")->createCrossLink($prefix, $this->container->getParameter('amp_prefix'), $request->getPathInfo()),
            'menus' => $this->getDoctrine()->getRepository('AppBundle:Menu')->findAllByName(),
            ];

        return empty($prefix) ? $this->render('AppBundle:Page:list.html.twig', $parameters) : $this->render('AppBundle:amp/Page:list.html.twig', $parameters);
    }

    /**
     * @Route("/go/{store_name},{type},{id}", name="out_link",
     *     defaults={"coupon_id": null},
     * )
     */
    public function goAction($type, $id)
    {
        switch ($type) {
            case 'cp':
                 $res = $this->getDoctrine()->getRepository('AppBundle:Coupon')->findLinkById($id);
                break;
            case 'st':
                 $res = $this->getDoctrine()->getRepository('AppBundle:Store')->findLinkById($id);
                break;
            default:
                $res = null;
                break;
        }
        if (empty($res)) {
            throw $this->createNotFoundException();
        }

        return  $this->redirect($res);
    }
}
