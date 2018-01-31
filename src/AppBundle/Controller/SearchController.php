<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends PageController
{
    /**
     * @Route("/search", name="search_ajax")
     */
    public function indexAction(Request $request)
    {
        if (!$request->isMethod('POST')) {
            throw $this->createNotFoundException();
        }

        $articles_all_count = $stores_all_count = $articles = $stores = null;
        $needle= trim(strip_tags(stripcslashes(htmlspecialchars($request->get('search-ajax')))));

        if (!empty($needle)) {
            $limit = 10;
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

                $articles = array_slice($articles, 0, $articles_count);
                $stores = array_slice($stores, 0, $stores_count);
            }
        }

        $parameters = [
            'articles' => $articles,
            'stores' => $stores,
            'needle' => $needle,
            'articles_count' => $articles_all_count,
            'stores_count' => $stores_all_count,
        ];

        return $this->render('AppBundle:Page:search.html.twig', $parameters);
    }

    /**
     * @Route("/{prefix}search/{slug}", name="search_page",
     *     requirements={"slug": "article|store|all", "prefix": "amp/|"},
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
                break;

            case 'store':
                $parameters['stores'] = $this->getDoctrine()->getRepository("AppBundle:Store")->findBySubname($needle);
                break;

            case 'all':
                $parameters['articles'] = $this->getDoctrine()->getRepository("AppBundle:Article")->findBySubname($needle);
                $parameters['stores'] = $this->getDoctrine()->getRepository("AppBundle:Store")->findBySubname($needle);
                break;
        }

        return $this->render('AppBundle:Page:list.html.twig', $parameters);
    }
}
