<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use AppBundle\Entity\Article;

class ArticleController extends Controller
{
    /**
     * @Route("/{prefix}article/{id}", name="article_page",
     *     requirements={"id": "\d+", "prefix": "amp/|"},
     *     defaults={"prefix": ""},
     * )
     * @ParamConverter("article", class="AppBundle:Article")
     * @Template()
     */
    public function pageAction(Article $article, Request $request)
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
            'article' => $article,
            'crosslink' => $crosslink,
            'menus' => $menus,
        ];

        # if prefix is not set render html page else render amp-html page
        return empty($prefix) ? $this->render('AppBundle:Article:page.html.twig', $parameters) : $this->render('AppBundle:amp/Article:page.html.twig', $parameters);
    }
}
