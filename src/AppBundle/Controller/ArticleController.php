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
    public function pageAction(Article $article, Request $request, $prefix = null)
    {
        $parameters = [
            'article' => $article,
            'crosslink' => $this->generateUrl('homepage', [], true)  . $this->getDoctrine()->getRepository("USPCPageBundle:Page")->createCrossLink($prefix, $this->container->getParameter('amp_prefix'), $request->getPathInfo()),
            'menus' => $this->getDoctrine()->getRepository('AppBundle:Menu')->findAllbyName(),
        ];

        # if prefix is not set render html page else render amp-html page
        return empty($prefix) ? $this->render('AppBundle:Article:page.html.twig', $parameters) : $this->render('AppBundle:amp/Article:page.html.twig', $parameters);
    }
}
