<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Menu;

class PageController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepageAction()
    {
        $article_repo = $this->getDoctrine()->getRepository('AppBundle:Article');

        $article = $article_repo->getHomepage();
        if (!$article) {
            throw $this->createNotFoundException();
        }

        return $this->forward('AppBundle:Article:page', ['article' => $article]);
    }

    /**
     * @Route("/amp/", name="homepage_amp")
     */
    public function homepageAmpAction()
    {
        $article_repo = $this->getDoctrine()->getRepository('AppBundle:Article');

        $article = $article_repo->getHomepage();
        if (!$article) {
            throw $this->createNotFoundException();
        }

        return $this->forward('AppBundle:Article:pageAmp', ['article' => $article]);
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
     * Render menu by name.
     *
     * @param  Menu|string  $name
     *
     * @Template()
     */
    public function menuAction($name)
    {
        return $this->getMenuByName($name);
    }

    /**
     * Render menu for amp-page by name and type.
     *
     * @param  Menu|string  $name
     *
     * @Template()
     */
    public function menuAmpAction($name, $type = null)
    {
        $menu = $this->getMenuByName($name);
        $menu['type'] = $type;
        return $menu;
    }

    /**
     * Return menu by name
     *
     * @param  string  $name
     * @return array
     * @author Michael Strohyi
     **/
    function getMenuByName($name)
    {
        if (is_string($name)) {
            $menu = $this->getDoctrine()->getRepository('AppBundle:Menu')->findOneByName($name);
        } elseif ($name instanceof Menu) {
            $menu = $name;
        } else {
            $menu = null;
        }

        return [
           'menu' => $menu,
        ];
    }
}
