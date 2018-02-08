<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends PageController
{
    /**
     * @Route("/{prefix}search", name="search_ajax",
     *     requirements={"prefix": "amp/|"},
     *     defaults={"prefix": ""},
     * )
     */
    public function indexAction(Request $request, $prefix)
    {
        if (!$request->isMethod('POST') && empty($prefix)) {
            throw $this->createNotFoundException();
        }

        $articles_all_count = $stores_all_count = $articles = $stores = $json_res = null;
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
            'needle' => $needle,
            'articles_count' => $articles_all_count,
            'stores_count' => $stores_all_count,
        ];

        if (empty($prefix)) {
            $parameters['articles'] = $articles;
            $parameters['stores'] = $stores;
            return $this->render('AppBundle:Page:search.html.twig', $parameters);
        }

        return new Response($this->getJsonRes($articles, $stores, $needle, $articles_all_count, $stores_all_count, $prefix));
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

        return empty($prefix) ? $this->render('AppBundle:Page:list.html.twig', $parameters) : $this->render('AppBundle:amp/Page:list.html.twig', $parameters);
    }

    /**
     * Convert list of $articles and $stores into JSON-string for live-search of amp-version.
     *
     * @param array $articles
     * @param array $stores
     * @return string
     * @author Michael Strohyi
     **/
    private function getJsonRes($articles, $stores, $needle, $articles_count, $stores_count, $prefix = null)
    {
        if (empty($articles) && empty($stores)) {
            return '{"items":[]}';
        }

        $items = [];
        if (!empty($articles)) {
            $items[] = [
                'url' => '/',
                'name' => 'Results from Articles', 'class' => 'search-result-type disabled',
            ];
            foreach ($articles as $article) {
                $items[] = [
                    'url' => '/' . $prefix . ltrim($article->getUrl(), '/'),
                    'name' => $article->getHeader(),
                    'class' => 'result',
                ];
            }
            if ($articles_count > count($articles)) {
               $items[] = [
                   'url' => $this->generateUrl('search_page', ['slug' => 'article', 'prefix' => $prefix, 'q' => $needle]),
                   'name' => "... more results for '" . $needle . "'",
                   'class' => 'search-result-more',
               ]; 
            }
        }

        if (!empty($stores)) {
            $items[] = [
                'url' => '/',
                'name' => 'Results from Stores', 
                'class' => 'search-result-type disabled',
            ];
            foreach ($stores as $store) {
                $items[] = [
                    'url' => '/' . trim($prefix, '/') . $store->getUrl(),
                    'name' => $store->getName(),
                    'class' => 'result',
                ];
            }

            if ($stores_count > count($stores)) {
               $items[] = [
                   'url' => $this->generateUrl('search_page', ['slug' => 'store', 'prefix' => $prefix, 'q' => $needle]),
                   'name' => "... more results for '" . $needle . "'",
                   'class' => 'search-result-more'
               ]; 
            }
        }

        return json_encode(["items" => $items]);
    }
}
