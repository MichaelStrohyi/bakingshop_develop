<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Store;

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
        return [
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
        return [
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
        return [
        ];
    }

}
