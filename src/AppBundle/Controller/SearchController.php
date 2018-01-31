<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends PageController
{
    /**
     * @Route("/{prefix}search", name="search_page_index",
     *     requirements={"prefix": "amp/|"},
     *     defaults={"prefix": ""},
     * )
     */
    public function indexAction($prefix, Request $request)
    {
        $method = 'get';
        $limit = $needle = $articles_all_count = $stores_all_count = null;
        $articles = $stores = [];
        if ($request->isMethod('POST')) {
            $method = 'post';
            $needle = trim(strip_tags(stripcslashes(htmlspecialchars($request->get('search-string')))));
            $limit = 2;
            $articles = $this->getDoctrine()->getRepository("AppBundle:Article")->findBySubname($needle, $limit);
            $stores = $this->getDoctrine()->getRepository("AppBundle:Store")->findBySubname($needle, $limit);
            $articles_count = $articles_all_count = count($articles);
            $stores_count = $stores_all_count = count($stores);
            if ($articles_count + $stores_count > $limit) {
                if ($stores_count < $limit / 2) {
                    $articles_count = $limit - $stores_count;
                } elseif ($articles_count < $limit / 2) {
                    $stores_count = $limit - $articles_count;
                } else {
                    $articles_count = $stores_count = $limit / 2;
                }
            }

            $articles = array_slice($articles, 0, $articles_count);
            $stores = array_slice($stores, 0, $stores_count);
        }

        $parameters = [
            'type' => 'search',
            'type_title' => 'Search',
            'prefix' => $prefix,
            'method' => $method,
            'articles' => $articles,
            'stores' => $stores,
            'articles_count' => $articles_all_count,
            'stores_count' => $stores_all_count,
            'needle' => $needle,
        ];

        return $this->render('AppBundle:Page:search.html.twig', $parameters);
    }

    /**
     * @Route("/{prefix}search/{slug}", name="search_page_full",
     *     requirements={"slug": "article|store", "prefix": "amp/|"},
     *     defaults={"prefix": ""},
     * )
     */
    public function listAction($slug, $prefix = null, Request $request)
    {
        $needle = trim(strip_tags(stripcslashes(htmlspecialchars($request->get('q')))));
        $parameters = [
            'type' => 'search',
            'type_title' => 'Search',
            'prefix' => $prefix,
            'crosslink' => $this->generateUrl('homepage', [], true)  . $this->getDoctrine()->getRepository("USPCPageBundle:Page")->createCrossLink($prefix, $this->container->getParameter('amp_prefix'), $request->getPathInfo()),
            'menus' => $this->getDoctrine()->getRepository('AppBundle:Menu')->findAllByName(),
            'needle' => $needle,
        ];
        switch ($slug) {
            case 'article':
                $parameters['articles'] = $this->getDoctrine()->getRepository("AppBundle:Article")->findBySubname($needle);
                return $this->render('AppBundle:Page:list.html.twig', $parameters);

            case 'store':
                $parameters['stores'] = $this->getDoctrine()->getRepository("AppBundle:Store")->findBySubname($needle);
                return $this->render('AppBundle:Page:list.html.twig', $parameters);  
        }
        
        throw $this->createNotFoundException();
    }
}
