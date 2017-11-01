<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Store;
use AdminBundle\Form\StoreType;

/**
 * @Route("/store")
 */
class StoreController extends Controller
{
    /**
     * @Route("/", name="admin_store_index")
     * @Template()
     */
    public function indexAction()
    {
        $store_list = $this->getDoctrine()->getRepository("AppBundle:Store")->findAllByName();

        return [
            'store_list' => $store_list,
        ];
    }

    /**
     * Create new store
     *
     * @param  Request  $request
     * 
     * @return Template
     * @author Michael Strohyi
     *
     * @Route("/new", name="admin_store_create")
     * @Template()
     **/
    public function createAction(Request $request)
    {
        $store = new Store;
        $form = $this->createStoreForm($store, $request);

        if ($form->isValid()) {
            $this->persiststore($store);

            return $this->redirectToRoute("admin_store_index");
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Edit store
     *
     * @param  Store  $store
     * @param  Request  $request
     * 
     * @return Template
     * @author Michael Strohyi
     *
     * @Route("/{id}/edit", name="admin_store_edit", requirements={"id": "\d+"})
     * @ParamConverter("store", class="AppBundle:Store")
     * @Template()
     **/
    public function editAction(Store $store, Request $request)
    {
        $form = $this->createStoreForm($store, $request);

        if ($form->isValid()) {
            $this->handleLogo($store, $request);
            $this->persistStore($store);

            return $this->redirectToRoute("admin_store_index");
        }

        return [
            'store' => $store,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete given store
     *
     * @param  Store  $store
     * @param  Request  $request
     * 
     * @return Template
     * @author Michael Strohyi
     *
     * @Route("/{id}/delete", name="admin_store_delete")
     * @ParamConverter("store", class="AppBundle:Store")
     * @Template()
     **/
    public function deleteAction(Store $store, Request $request)
    {
        $form = $this->createFormBuilder([])->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity_manager = $this->getDoctrine()->getEntityManager();

            $entity_manager->remove($store);
            $entity_manager->flush();

            return $this->redirectToRoute("admin_store_index");
        }

        return [
            'store' => $store,
            'form' => $form->createView(),
        ];
    }

    /**
     * Save given store into database
     *
     * @param  Store  $store
     * 
     * @return void
     * @author Michael Strohyi
     **/
    private function persistStore(Store $store)
    {
        $entity_manager = $this->getDoctrine()->getEntityManager();

        $entity_manager->persist($store);
        $entity_manager->flush();
    }

     /**
     * Return form for create/edit store
     *
     * @param Store $store
     * @param Request $request
     *
     * @return Form
     * @author Michael Strohyi
     **/
    private function createStoreForm(Store $store, Request $request)
    {
            $form = $this->createForm(new StoreType(), $store);
            $form->handleRequest($request);

            return $form;
    }

    /**
     * Remove store logo if crrent logo was deleted and new logo was not selected
     *
     * @param Store $store
     * @param Request $request
     *
     * @return void
     * @author Michael Strohyi
     **/
    private function handleLogo(Store $store, Request $request)
    {
        if (null === $request->request->get('current_logo') && (null === $store->getLogo() || null === $store->getLogo()->getImageFile())) {
            $store->removeLogo();
        }
    }

}
