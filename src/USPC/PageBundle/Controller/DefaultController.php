<?php

namespace USPC\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use USPC\PageBundle\Entity\Page;

class DefaultController extends Controller
{
    /**
     * @var array
     */
    private $dispatchers;

    /**
     * Page entity
     *
     * @var Page
     **/
    private $page;

    /**
     * Page object
     *
     * @var object
     **/
    private $page_object;

    public function dispatcherAction($slug, $prefix = null, Request $request)
    {
        # load dispatchers
        $this->dispatchers = $this->container->getParameter('router.dispatchers');

        # add forward slash at the beginning
        $url = '/' . $slug;

        # find page with the given url
        $page = $this->page = $this->getDoctrine()->getRepository('USPCPageBundle:Page')->findOneByUrl($url);

        # get page object
        $page_object = $this->page_object = $this->getPageObject();

        # if page not found
        if (empty($page) || empty($page_object)) {
            # try to find given url in redirects
            $new_url = $this->findRedirect($url);
            # if url not found display error page
            if (empty($new_url)) {
                throw $this->createNotFoundException();
            }
            # make redirect to new url
            return $this->makeRedirect($new_url, $prefix, $request->getBaseUrl());
        }
        # if page is alias redirect to new url
        if ($page->isAlias()) {
            return $this->makeRedirect($this->page_object->getUrl(), $prefix, $request->getBaseUrl());
        }

        list($controller, $parameters) = $this->getPageController();

        # add request and prefix into parameters
        $parameters['request'] = $request;
        $parameters['prefix'] = $prefix;
        $response = $this->forward($controller, $parameters, $request->query->all());

        return $response;
    }

    /**
     * Return dispatcher if exists, otherwise return null.
     *
     * @return mixed
     * @author Mykola Martynov
     **/
    private function getDispatcher()
    {
        if (empty($this->page)) {
            return null;
        }

        $type = $this->page->getType();
        if (!array_key_exists($type, $this->dispatchers)) {
            return null;
        }

        return $this->dispatchers[$type];
    }

    /**
     * Return list of two items, first is a controller method, and a second
     * is it's parameters.
     *
     * @return array
     * @author Mykola Martynov
     **/
    private function getPageController()
    {
        $dispatcher = $this->getDispatcher();
        if (empty($dispatcher)) {
            return null;
        }

        $controller = $dispatcher['controller'];
        $parameters = [$this->page->getType() => $this->page_object];

        return [$controller, $parameters];
    }

    /**
     * Return object associated with current page
     *
     * @return object
     * @author Mykola Martynov
     **/
    private function getPageObject()
    {
        $dispatcher = $this->getDispatcher();
        if (empty($dispatcher)) {
            return null;
        }

        $entity = $dispatcher['entity'];
        $object = $this->getDoctrine()->getRepository($entity)->find($this->page->getObjectId());

        return $object;
    }

    /**
     * Create new url and make redirect to it
     *
     * @param string $url
     * @param string $prefix
     * @param string $base_url
     *
     * @return RedirectResponse
     * @author Michael Strohyi
     **/
    private function makeRedirect($url, $prefix = null, $base_url = null)
    {
        $new_url = $base_url;
        if (!empty($prefix)) {
            $new_url .=  '/' . rtrim($prefix, '/');
        }

        $new_url .= $url;

        return $this->redirect($new_url, 301);
    }

    /**
     * Search given url in redirects table and return it's prod url for redirect or null, if no redirect was found
     *
     * @param string $url
     *
     * @return string|null
     * @author Michael Strohyi
     **/
    private function findRedirect($url)
    {
        $redirects = $this->getDoctrine()->getRepository('AppBundle:Redirect')->findAll();
        foreach ($redirects as $redirect) {
            $redirect_url = $redirect->getProdUrl();
            if (strpos($url, $redirect_url) === 0) {
                $url = str_replace($redirect_url, $redirect->getUrl(), $url);
                return $url;
            }
        }
    }
}
