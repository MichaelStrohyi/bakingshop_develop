<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Menu;
use AppBundle\Entity\Article;

class PageController extends Controller
{
    /**
     * @Route("/{prefix}", name="homepage",
     *     requirements={"prefix": "amp/|"},
     *     defaults={"prefix": ""},
     * )
     */
    public function homepageAction($prefix, Request $request)
    {
        $article_repo = $this->getDoctrine()->getRepository('AppBundle:Article');

        $article = $article_repo->getHomepage();
        if (!$article) {
            throw $this->createNotFoundException();
        }

        $parameters = [
            'request' => $request,
            'article' => $article,
            'type' => $article->getType(),
            'type_title' => $article->getTypeTitle($article->getType()),
            'prefix' => $prefix,
        ];

        return $this->forward('AppBundle:Article:page', $parameters);
    }

    /**
     * @Template()
     */
    public function sidebarAction()
    {
        $menus = $this->getDoctrine()->getRepository('AppBundle:Menu')->findAll();

        return [
            'menus' => $menus,
        ];
    }

    /**
     * @Template()
     */
    public function headerSlideshowAction()
    {
        return [];
    }

    /**
     * Render menu by name and type.
     *
     * @param Menu|string  $name
     * @param string $prefix
     * @param string $type
     *
     */
    public function menuAction($name, $prefix = null)
    {
        if (is_string($name)) {
            $menu = $this->getDoctrine()->getRepository('AppBundle:Menu')->findOneByName($name);
        } elseif ($name instanceof Menu) {
            $menu = $name;
        } else {
            $menu = null;
        }
        $parameters['menu'] = $menu;

        # if prefix is not set render menu for html page
        if (empty($prefix)) {
            return $this->render('AppBundle:Page:menu.html.twig', $parameters);
        }

        # if prefix is set render menu for amp-html page
        $parameters['prefix'] = $prefix;
        return $this->render('AppBundle:amp/Page:menu.html.twig', $parameters);
    }

    /**
     * @Route("/{prefix}{slug}/list", name="list_page",
     *     requirements={"slug": ".+", "prefix": "amp/|"},
     *     defaults={"prefix": ""},
     * )
     *
     * @Template()
     */
    public function listAction($slug, $prefix = null, Request $request)
    {
        if (!in_array($slug, Article::getTypes())) {
            throw $this->createNotFoundException();
        }

        $parameters = [
            'articles' => $this->getDoctrine()->getRepository('AppBundle:Article')->findAllByType($slug),
            'type' => $slug,
            'type_title' => Article::getTypeTitle($slug),
            'crosslink' => $this->generateUrl('homepage', [], true)  . $this->getDoctrine()->getRepository("USPCPageBundle:Page")->createCrossLink($prefix, $this->container->getParameter('amp_prefix'), $request->getPathInfo()),
            'menus' => $this->getDoctrine()->getRepository('AppBundle:Menu')->findAllByName(),
            ];

        return empty($prefix) ? $this->render('AppBundle:Page:list.html.twig', $parameters) : $this->render('AppBundle:amp/Page:list.html.twig', $parameters);
    }
}
