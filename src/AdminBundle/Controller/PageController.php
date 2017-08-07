<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use USPC\PageBundle\Entity\Page;

class PageController extends Controller
{
    /**
     * Update page url in database
     *
     * @param string $type page type
     * @param object $obj object for save url
     * 
     * @return void
     * 
     * @author Mykola Martynov
     **/
    protected function updatePageUrls($type, $obj)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $page_repo = $em->getRepository('USPCPageBundle:Page');

        # set all previous urls as alias
        $page_repo->makeAliasUrl($type, $obj);

        # add/update current url
        $page = $page_repo->findObject($type, $obj);
        if (!$page) {
            $page = new Page();
            $page->setType($type);
            $page->setUrl($obj->getUrl());
            $page->setObjectId($obj->getId());

            $em->persist($page);
        }

        $page->setIsAlias(false);
        $em->flush();
    }

    /**
     * Remove unused urls
     *
     * @param string $type page type
     * @param object $obj object for save url
     * 
     * @return void
     *
     * @author Mykola Martynov
     **/
    protected function deletePageUrls($type, $obj)
    {
        $this->getDoctrine()->getRepository('USPCPageBundle:Page')
            ->deletePageUrls($type, $obj);
    }
}
