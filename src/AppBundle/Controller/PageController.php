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
    public function homepageAction(Request $request)
    {
        $article_repo = $this->getDoctrine()->getRepository('AppBundle:Article');

        $article = $article_repo->getHomepage();
        if (!$article) {
            throw $this->createNotFoundException();
        }

        $parameters['request'] = $request;
        $parameters['article'] = $article;

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
    public function menuAction($name, $prefix = null, $type = null)
    {
        if (is_string($name)) {
            $menu = $this->getDoctrine()->getRepository('AppBundle:Menu')->findOneByName($name);
        } elseif ($name instanceof Menu) {
            $menu = $name;
        } else {
            $menu = null;
        }
        $parameters['menu'] = $menu;
        $parameters['menu_type'] = $type;

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
    public function listAction($slug, Request $request)
    {
        if (!in_array($slug, Article::getTypes())) {
            throw $this->createNotFoundException();
        }

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

        switch ($slug) {
            case Article::PAGE_SUBTYPE_ARTICLE:
                $type = $slug;
                $type_title = 'Articles';
                break;
            case Article::PAGE_SUBTYPE_RECIPE:
                $type = $slug;
                $type_title = 'Recipies';
                break;
            case Article::PAGE_SUBTYPE_INFO:
                $type = $slug;
                $type_title = 'Information';
                break;
            default:
                $type = '';
                $type_title = '';
                break;
        }

        $parameters['articles'] = $this->getDoctrine()->getRepository('AppBundle:Article')->findAllByType($slug);
        $parameters['type'] = $type;
        $parameters['type_title'] = $type_title;
        $parameters['crosslink'] = $crosslink;
        $parameters['menus'] = $this->getDoctrine()->getRepository('AppBundle:Menu')->findAll();

        return empty($prefix) ? $this->render('AppBundle:Page:list.html.twig', $parameters) : $this->render('AppBundle:amp/Page:list.html.twig', $parameters);
    }
}
