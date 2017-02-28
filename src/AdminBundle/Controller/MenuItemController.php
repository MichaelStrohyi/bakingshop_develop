<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Menu;
use AdminBundle\Form\MenuItemsType;

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
     * @param  Request  $request
     * 
     * @return Template
     * 
     * @author Mykola Martynov
     *
     * @Route("/", name="admin_menu_items")
     * @Template()
     **/
    public function indexAction(Menu $menu, Request $request)
    {
        $form = $this->createMenuForm($menu, $request);
        $this->addItemsInMenu($menu);

        if ($form->isValid()) {
            $this->persistItems($menu);

            return $this->redirectToRoute("admin_menu_index");
        }

        return [
            'menu' => $menu,
            'form' => $form->createView(),
        ];
    }

    /**
     * Set menu parent for each element
     *
     * @param  Menu  $menu
     * 
     * @return void
     * 
     * @author Mykola Martynov
     **/
    private function addItemsInMenu(Menu $menu)
    {
        foreach ($menu->getItems() as $item) {
            $item->setMenu($menu);
        }
    }

    /**
     * Create form for manipulating menu items
     *
     * @param  Menu  $menu
     * @param  Request  $request
     * 
     * @return Form
     *
     * @author Mykola Martynov
     **/
    private function createMenuForm(Menu $menu, Request $request)
    {
        $form = $this->createForm(new MenuItemsType, $menu);
        $form->handleRequest($request);

        return $form;
    }

    /**
     * Persist all menu items and change position for each item.
     *
     * @param  Menu  $menu
     * 
     * @return void
     * 
     * @author Mykola Martynov
     **/
    private function persistItems(Menu $menu)
    {
        $entity_manager = $this->getDoctrine()->getEntityManager();

        $entity_manager->persist($menu);
        $entity_manager->flush();
    }
}
