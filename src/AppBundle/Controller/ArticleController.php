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
    public function pageAction(Article $article, Request $request, $prefix = '')
    {
        $route_params = $request->attributes->get('_route_params');
        $prefix = $route_params['prefix'];
        $amp_prefix = $this->container->getParameter('amp_prefix');
        $path = $request->getPathInfo();
        if  (!empty($prefix)) {
            $path = substr($path, strlen($prefix));
            $link_prefix = '/' . rtrim($prefix, '/');

            $crosslink = $this->generateUrl('homepage', [], true) . ltrim($path, '/');
        } else {
            $crosslink = $this->generateUrl('homepage', [], true) . $amp_prefix . ltrim($path, '/');
            $link_prefix = '';
        }

        $parameters = [
            'article' => $article,
            'link_prefix' => $link_prefix,
            'crosslink' =>$crosslink
        ];

        return empty($prefix) ? $this->render('AppBundle:Article:page.html.twig', $parameters) : $this->render('AppBundle:amp/Article:page.html.twig', $parameters);
    }
}
