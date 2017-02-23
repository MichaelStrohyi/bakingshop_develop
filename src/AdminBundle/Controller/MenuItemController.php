<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Menu;

/**
 * @Route("/menu/{id}/items", requirements={"id": "\d+"})
 * @ParamConverter("menu", class="AppBundle:Menu")
 */
class MenuItemController extends Controller
{
    /**
     * Display all items in given menu.
     *
     * @param  Menu  $menu
     * 
     * @return Template
     * 
     * @author Mykola Martynov
     *
     * @Route("/", name="admin_menu_items")
     * @Template()
     **/
    public function indexAction(Menu $menu)
    {
        return [
            'menu' => $menu,
        ];
    }
}
