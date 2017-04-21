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

    public function dispatcherAction($slug, Request $request)
    {
        # load dispatchers
        $this->dispatchers = $this->container->getParameter('router.dispatchers');

        # add forward slash at the beginning
        $url = '/' . $slug;

        # find page with the given url
        $page = $this->page = $this->getDoctrine()->getRepository('USPCPageBundle:Page')->findOneByUrl($url);

        # get page object
        $page_object = $this->page_object = $this->getPageObject();

        # display error if page not found
        if (empty($page) || empty($page_object)) {
            throw $this->createNotFoundException();
        }

        # redirect to new url
        if ($page->isAlias()) {
            return $this->redirect($request->getBaseUrl() . $this->page_object->getUrl(), 301);
        }

        list($controller, $parameters) = $this->getPageController();

        # add request into parameters
        $parameters['request'] = $request;

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
}
