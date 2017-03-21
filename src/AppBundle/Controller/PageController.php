<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Menu;

class PageController extends Controller
{
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
     * Render menu by name.
     *
     * @param  Menu|string  $name
     *
     * @Template()
     */
    public function menuAction($name)
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
