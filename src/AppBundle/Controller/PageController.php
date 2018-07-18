<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Menu;
use AppBundle\Entity\Article;
use AppBundle\Entity\Store;

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
        #g find article with is_homepage flag
        $article_repo = $this->getDoctrine()->getRepository('AppBundle:Article');
        $article = $article_repo->getHomepage();
        # return error 404 if article is not fond
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
    public function sidebarAction($pathInfo = null)
    {
        $menus = $this->getDoctrine()->getRepository('AppBundle:Menu')->findAllbyPosition();

        return [
            'menus' => $menus,
            'pathInfo' => $pathInfo,
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
    public function menuAction($name, $prefix = null, $pathInfo = null)
    {
        if (is_string($name)) {
            $menu = $this->getDoctrine()->getRepository('AppBundle:Menu')->findOneByName($name);
        } elseif ($name instanceof Menu) {
            $menu = $name;
        } else {
            return;
        }
        
        $parameters['menu'] = $menu;
        $parameters['pathInfo'] = $pathInfo;

        # if prefix is not set render menu for html page
        if (empty($prefix)) {
            return $this->render('AppBundle:Page:menu.html.twig', $parameters);
        }

        # if prefix is set render menu for amp-html page
        $parameters['prefix'] = $prefix;
        return $this->render('AppBundle:amp/Page:menu.html.twig', $parameters);
    }

    /**
     * @Route("/{prefix}{slug}/list/{page}", name="list_page",
     *     requirements={"slug": ".+", "prefix": "amp/|", "page": "\d+|all"},
     *     defaults={"prefix": "", "page": 1},
     * )
     *
     * @Template()
     */
    public function listAction($slug, $prefix = null, $page, Request $request)
    {
        if ($page == "all") {
            $page = 0;
        }

        if ($page == 0 && $slug == Article::PAGE_SUBTYPE_ARTICLE) {
            return $this->redirectToRoute("sitemap_page", ["prefix" => $prefix]);
        }

        if (!in_array($slug, Article::getTypes()) && $slug != Store::PAGE_TYPE) {
            throw $this->createNotFoundException();
        }

        $parameters = $this->getData($slug, $prefix , $page, $request);

        return empty($prefix) ? $this->render('AppBundle:Page:list.html.twig', $parameters) : $this->render('AppBundle:amp/Page:list.html.twig', $parameters);
    }

    /**
     * @Route("/{prefix}sitemap", name="sitemap_page",
     *     requirements={"prefix": "amp/|"},
     *     defaults={"prefix": ""},
     * )
     *
     * @Template()
     */
    public function sitemapAction($prefix = null, $parameters = [], Request $request)
    {
        $page = 0;
        $slug = Article::PAGE_SUBTYPE_ARTICLE;
        $parameters = $this->getData($slug, $prefix , $page, $request);
        $parameters['type_title'] = 'Site Map';

        return empty($prefix) ? $this->render('AppBundle:Page:list.html.twig', $parameters) : $this->render('AppBundle:amp/Page:list.html.twig', $parameters);
    }

    /**
     * Get data for twig template for list/sitemap page according to given parameters
     *
     * @return array
     * @author Michael Strohyi
     **/
    private function getData($slug, $prefix = null, $page, Request $request)
    {
        if (!in_array($slug, Article::getTypes()) && $slug != Store::PAGE_TYPE) {
            throw $this->createNotFoundException();
        }

        $page_repo = $this->getDoctrine()->getRepository("USPCPageBundle:Page");
        $parameters = [
            'crosslink' => $this->generateUrl('homepage', [], true)  . $this->getDoctrine()->getRepository("USPCPageBundle:Page")->createCrossLink($prefix, $this->container->getParameter('amp_prefix'), $request->getPathInfo()),
            'menus' => $this->getDoctrine()->getRepository('AppBundle:Menu')->findAllByPosition(),
            'page' => $page,
            'type' => $slug,
            ];

        # get pagination links and articles or stores list according to type
        if ($slug == Store::PAGE_TYPE) {
            list($parameters['stores'], $parameters['navigation']) = $page_repo->getResultsForPage($this->getDoctrine()->getRepository('AppBundle:Store')->findAllByName(), $page);
            $parameters['type_title'] = 'Stores';
        }
        else {
            list($parameters['articles'], $parameters['navigation']) = $page_repo->getResultsForPage($this->getDoctrine()->getRepository("AppBundle:Article")->findAllByType($slug), $page);
            $parameters['type_title'] = Article::getTypeTitle($slug);
        }

        return $parameters;
    }

}
