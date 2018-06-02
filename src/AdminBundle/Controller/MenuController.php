<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Menu;
use AdminBundle\Form\MenuType;
use AdminBundle\Form\MenusType;

/**
 * @Route("/menu")
 */
class MenuController extends Controller
{
    /**
     * @Route("/", name="admin_menu_index")
     * @Template()
     */
    public function indexAction()
    {
        $menu_list = $this->getDoctrine()->getRepository("AppBundle:Menu")->findAllByPosition();

        return [
            'menu_list' => $menu_list,
            'menu_types' => Menu::getTypes(),
        ];
    }

    /**
     * Create new menu
     *
     * @param  Request  $request
     * 
     * @return Template
     * 
     * @author Mykola Martynov
     *
     * @Route("/new", name="admin_menu_create")
     * @Template()
     **/
    public function createAction(Request $request)
    {
        $menu = new Menu;
        $form = $this->createMenuForm($menu, $request);

        if ($form->isValid()) {
            # set position for new menu at the end of the same type menus list
            $menu->setPosition($this->getDoctrine()->getRepository('AppBundle:Menu')->getNewPosition($menu->getType()));
            # save menu
            $this->persistMenu($menu);

            return $this->redirectToRoute("admin_menu_items", ["id" => $menu->getId()]);
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Update menu
     *
     * @param  Menu  $menu
     * @param  Request  $request
     * 
     * @return Template
     * 
     * @author Mykola Martynov
     *
     * @Route("/{id}/edit", name="admin_menu_edit", requirements={"id": "\d+"})
     * @ParamConverter("menu", class="AppBundle:Menu")
     * @Template()
     **/
    public function editAction(Menu $menu, Request $request)
    {
        $form = $this->createMenuForm($menu, $request);

        if ($form->isValid()) {
            $this->persistMenu($menu);

            return $this->redirectToRoute("admin_menu_show", ["id" => $menu->getId()]);
        }
        return [
            'menu' => $menu,
            'form' => $form->createView(),
        ];
    }

    /**
     * Display information about given menu.
     * 
     * @param  Menu   $menu
     * 
     * @return Template
     * 
     * @Route("/{id}", name="admin_menu_show", requirements={"id": "\d+"})
     * @ParamConverter("menu", class="AppBundle:Menu")
     * @Template()
     */
    public function showAction(Menu $menu)
    {
        return [
            'menu' => $menu,
        ];
    }

    /**
     * Delete givem menu
     *
     * @param  Menu  $menu
     * @param  Request  $request
     * 
     * @return Template
     * 
     * @author Mykola Martynov
     *
     * @Route("/{id}/delete", name="admin_menu_delete")
     * @ParamConverter("menu", class="AppBundle:Menu")
     * @Template()
     **/
    public function deleteAction(Menu $menu, Request $request)
    {
        $form = $this->createFormBuilder([])->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity_manager = $this->getDoctrine()->getEntityManager();

            $entity_manager->remove($menu);
            $entity_manager->flush();

            return $this->redirectToRoute("admin_menu_index");
        }

        return [
            'menu' => $menu,
            'form' => $form->createView(),
        ];
    }

    /**
     * Display all menu with given type
     *
     * @param string $type
     * @param  Request $request
     *
     * @return Template
     *
     * @author Michael Strohyi
     *
     * @Route("/{type}/reorder", name="admin_menu_reorder")
     * @Template()
     **/
    public function reorderAction(Request $request, $type)
    {
        # get all menus with given type
        $menu_list = $this->getDoctrine()->getRepository('AppBundle:Menu')->findByType($type);
        # create form
        $form = $this->createMenusForm($menu_list, $request);
        # check if form has been submitted and is valid
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            # save all menus
            foreach ($form->getData()['items'] as $menu) {
                $em->persist($menu);
            }

            $em->flush();

            return $this->redirectToRoute("admin_menu_index");
        }

        return [
            'form' => $form->createView(),
        ];
    }


    /**
     * Save given menu into database
     *
     * @param  Menu  $menu
     * 
     * @return void
     * 
     * @author Mykola Martynov
     **/
    private function persistMenu(Menu $menu)
    {
        $entity_manager = $this->getDoctrine()->getEntityManager();

        $entity_manager->persist($menu);
        $entity_manager->flush();
    }

    /**
     * Return form for create/edit menu
     *
     * @param  Menu  $menu
     * @param  Request  $request
     * 
     * @return FormBuilder
     * 
     * @author Mykola Martynov
     **/
    private function createMenuForm(Menu $menu, Request $request)
    {
        $form = $this->createForm(new MenuType, $menu);
        $form->handleRequest($request);

        return $form;
    }

    /**
     * Return form for reorder menu
     *
     * @param  array  $menus
     * @param  Request  $request
     *
     * @return FormBuilder
     *
     * @author Michael Strohy
     **/
    private function createMenusForm($menus, Request $request)
    {
        $form = $this->createForm(new MenusType, ['items' => $menus]);
        $form->handleRequest($request);

        return $form;
    }
}
