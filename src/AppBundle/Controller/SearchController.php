<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Article;
use AppBundle\Entity\Store;

class SearchController extends Controller
{
    const RESULTS_LIMIT = 10;

    /**
     * @Route("/{prefix}search", name="search_ajax",
     *     requirements={"prefix": "amp/|"},
     *     defaults={"prefix": ""},
     * )
     */
    public function indexAction(Request $request, $prefix)
    {
        # exit if prefix is not set and methos is not POST (GET method is only allowed for pages with prefix)
        if (!$request->isMethod('POST') && empty($prefix)) {
            throw $this->createNotFoundException();
        }

        $articles_all_count = $stores_all_count = $articles = $stores = $json_res = null;
        # get search-string from parameters
        $needle = trim(strip_tags(stripcslashes($request->get('search-ajax'))));
        $amplified = $request->get('a') == 'a' ? true : false;
        # if search-string is not empty
        if (!empty($needle)) {
            #set max count of search results wich will be shown for user
            $limit = self::RESULTS_LIMIT;
            # get articles wich match search-string
            $articles = $this->getDoctrine()->getRepository("AppBundle:Article")->findBySubname($needle, $limit + 1);
            # get stores wich match search-string
            $stores = $this->getDoctrine()->getRepository("AppBundle:Store")->findBySubname($needle, $limit + 1);
            # get results from stores and articles limited by RESULT_LIMIT
            $items = $this->getDoctrine()->getRepository("USPCPageBundle:Page")->getMixedResultsForPage($articles, $stores, $limit);
            $articles = $items['articles'];
            $stores = $items['stores'];
            $articles_all_count = $items['articles_count'];
            $stores_all_count = $items['stores_count'];
        }
        
        # if prefix is not set render search results as html
        if (empty($prefix)) {
            # set parameters for twig template
            $parameters = [
                'needle' => $needle,
                'articles_count' => $articles_all_count,
                'stores_count' => $stores_all_count,
                'articles'=> $articles,
                'stores' => $stores,
                'amplified' => $amplified,
            ];

            return $this->render('AppBundle:Page:search.html.twig', $parameters);
        }
        # if prefix is set return search results as json
        return new Response($this->getJsonRes($articles, $stores, $needle, $articles_all_count, $stores_all_count, $request->getBaseUrl(), $prefix));
    }

    /**
     * @Route("/{prefix}search/{slug}/{page}", name="search_page",
     *     requirements={"slug": "article|store|all", "prefix": "amp/|", "page": "\d+|all"},
     *     defaults={"prefix": "", "page": 1},
     * )
     */
    public function listAction($slug, $page, $prefix = null, Request $request)
    {
        if ($page == "all") {
            $page = 0;
        }

        # get search-string from parameters
        $needle = trim(strip_tags(stripcslashes($request->get('q'))));
        #set parameters for twig template
        $parameters = [
            'type' => 'search',
            'type_title' => 'Search',
            'crosslink' => $this->generateUrl('homepage', [], true)  . $this->getDoctrine()->getRepository("USPCPageBundle:Page")->createCrossLink($prefix, $this->container->getParameter('amp_prefix'), $request->getPathInfo()),
            'menus' => $this->getDoctrine()->getRepository('AppBundle:Menu')->findAllByPosition(),
            'needle' => $needle,
            'stores' => null,
            'articles' => null,
            'page' => $page,
            'slug' => $slug,

        ];
        $articles_count = $stores_count = 0;
        $page_repo = $this->getDoctrine()->getRepository("USPCPageBundle:Page");
        # get articles and stores wich match search-string according to search type and pagination links
        switch ($slug) {
            case Article::PAGE_TYPE:
                list($parameters['articles'], $parameters['navigation']) = $page_repo->getResultsForPage($this->getDoctrine()->getRepository("AppBundle:Article")->findBySubname($needle), $page);
                break;

            case Store::PAGE_TYPE:
                list($parameters['stores'], $parameters['navigation']) = $page_repo->getResultsForPage($this->getDoctrine()->getRepository("AppBundle:Store")->findBySubname($needle), $page);
                break;

            case 'all':
                $articles = $this->getDoctrine()->getRepository("AppBundle:Article")->findBySubname($needle);
                $stores = $this->getDoctrine()->getRepository("AppBundle:Store")->findBySubname($needle);
                $items = $this->getDoctrine()->getRepository("USPCPageBundle:Page")->getMixedResultsForPage($articles, $stores); 
                $parameters['articles'] = $items['articles'];
                $parameters['stores'] = $items['stores'];
                $parameters['articles_count'] = $items['articles_count'];
                $parameters['stores_count'] = $items['stores_count'];
                break;
        }
        # get articles and stores count
        $articles_count = array_key_exists('articles', $parameters) && !empty($parameters['articles']) ? count($parameters['articles']) : 0;
        $stores_count = array_key_exists('stores', $parameters) && !empty($parameters['stores']) ? count($parameters['stores']) : 0;
        # if there is only one result of search make redirect to page of this result
        if ($articles_count + $stores_count == 1 && (!array_key_exists('navigation', $parameters) || empty($parameters['navigation']))) {
            return $articles_count == 0 ? $this->redirect($this->generatePathForObj($parameters['stores'][0], ['baseUrl' => $request->getBaseUrl(), 'prefix' => $prefix]), 301) : $this->redirect($this->generatePathForObj($parameters['articles'][0], ['baseUrl' => $request->getBaseUrl(), 'prefix' => $prefix]), 301);
        }
        # render result of search
        return empty($prefix) ? $this->render('AppBundle:Page:list.html.twig', $parameters) : $this->render('AppBundle:amp/Page:list.html.twig', $parameters);
    }

    /**
     * Convert list of $articles and $stores into JSON-string for live-search of amp-version.
     *
     * @param array $articles
     * @param array $stores
     * @param string $needle
     * @param int $articles_count
     * @param int $stores_count
     * @param string $baseUrl
     * @param string $prefix
     * @return string
     * @author Michael Strohyi
     **/
    private function getJsonRes($articles, $stores, $needle, $articles_count, $stores_count, $baseUrl, $prefix = null)
    {
        $items = [];
        # if stores and articles are empty return message "no results found"
        if (empty($articles) && empty($stores)) {
            if (strlen($needle) > 1) {
                $items[] = [
                    'url' => $this->generateUrl('homepage', ['prefix' => $prefix]),
                    'name' => "No results found for \"" . $needle . "\"",
                    'class' => 'search-result-type disabled',
                ];
            }

            return json_encode(["items" => $items]);
        }
        # if stores are not empty
        if (!empty($stores)) {
            $items = array_merge($items, $this->prepareForJson($stores, $stores_count, $needle, $baseUrl, $prefix, empty($articles)));
        }
        # if articles are not empty
        if (!empty($articles)) {
            $items = array_merge($items, $this->prepareForJson($articles, $articles_count, $needle, $baseUrl, $prefix, empty($stores)));
        }
        #return result as json
        return json_encode(["items" => $items]);
    }
    /**
     * Generate path to given $obj with given $parameters
     *
     * @param  object $obj
     * @param  array $parameters
     * @return string|null
     * @author Michael Strohyi
     **/
    private function generatePathForObj($obj, $parameters)
    {
         if (is_object($obj) && method_exists($obj, 'getUrl')) {
            return $this->objectUrl($obj, $parameters);
        }
    }

    /**
     * Return url for the given $obj
     *
     * ASSUMPTION: object url has no query parameters and starts with forwards slash
     *
     * @param  object $obj
     * @param  array $parameters
     * @return string|null
     * @author Michael Strohyi
     **/
    private function objectUrl($obj, $parameters = [])
    {
        # get baseUrl from parameters
        $baseUrl = '';
        if (array_key_exists('baseUrl', $parameters)) {
            $baseUrl = $parameters['baseUrl'];
            # delete baseUrl from parameters list
            unset($parameters['baseUrl']);
        }
        # get prefix from parameters
        $prefix = '';
        if (array_key_exists('prefix', $parameters)) {
            if (!empty($parameters['prefix'])) {
                $prefix = '/' . trim($parameters['prefix'], '/');
            }
            # delete prefix from parameters list
            unset($parameters['prefix']);
        }
        #create url
        $url = $baseUrl . $prefix . $obj->getUrl();

        # add parameters
        if (!empty($url) && !empty($parameters)) {
            $url .= '?' . http_build_query($parameters);
        }

        return $url;
    }
    /**
     * Get data from given items and convert this data to array, which can be converted into JSON search-respond
     *
     * @param array $items
     * @param int $items_count
     * @param string $needle
     * @param string $baseUrl
     * @param string $prefix
     * @param boolean $single_type
     * @return array
     * @author Michael Strohyi
     **/
    private function prepareForJson($items, $items_count, $needle, $baseUrl, $prefix = null, $single_type = true)
    {
        $res = [];
        if (empty($items)) {
            return $res;
        }
        # analize type of items and set necessary variables according to item's type
        switch (get_class($items[0])) {
            case 'AppBundle\Entity\Article':
                $method = 'getHeader';
                $type = Article::PAGE_TYPE;
                break;

            case 'AppBundle\Entity\Store':
                $method = 'getName';
                $type = Store::PAGE_TYPE;
                break;

            default:
                # return if type of items is unknown
                return $res;
        }
        # if items of another type exist (single_Type == false) add header with type of search result for current items
        if (!$single_type) {
            $res[] = [
                'url' => $this->generateUrl('homepage', ['prefix' => $prefix]),
                'name' => 'Results from ' . ucfirst($type) . 's',
                'class' => 'search-result-type disabled',
            ];
        }
        # add each item into result
        foreach ($items as $item) {
            $logo = [];
            $discount = null;
            if ($type == 'Store') {
                $item_logo = $item->getLogo();
                if (!empty($item_logo) && !empty($item_logo->getFilename())) {
                    $logo = [
                        'filename' => $item_logo->getFilename(),
                        'width' => $item_logo->getWidth(),
                        'height' => $item_logo->getHeight(),
                    ];
                }

                $discount = $item->getMainDiscount();
            }

            $res[] = [
                'url' => $this->generatePathForObj($item, ['baseUrl' => $baseUrl, 'prefix' => $prefix]),
                'name' => $item->$method(),
                'class' => 'result',
                'logo' => $logo,
                'discount' => $discount,
            ];
        }
        # add link to see all items if some item was removed from result by limit
        if ($items_count > count($items)) {
           $res[] = [
               'url' => $this->generateUrl('search_page', ['slug' => $type, 'prefix' => $prefix, 'q' => $needle, 'page' => 1]),
               'name' => "... more results for '" . $needle . "'",
               'class' => 'search-result-more',
           ];
        }

        return $res;
    }
}
