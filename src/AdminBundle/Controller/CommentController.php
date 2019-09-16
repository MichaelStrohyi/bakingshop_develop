<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AdminBundle\Form\CommentsType;
use AppBundle\Entity\Store;

class CommentController extends Controller
{ 
    /**
     * @Route("/comment", name="admin_comment_index")
     * @Template()
     */
    public function indexAction()
    {
        $store_list = $this->getDoctrine()->getRepository("AppBundle:Store")->findAllWithUnverifiedComments(true);

        return [
            'store_list' => $store_list,
        ];
    }

    /**
     * Display all stores with comments, which need verification
     *
     * @param Store $store
     * @param Request $request
     * 
     * @return Template
     * @author Michael Strohyi
     *
     * @Route("/store/{id}/comments", name="admin_store_comments", requirements={"id": "\d+"})
     * @ParamConverter("store", class="AppBundle:Store")
     * @Template()
     */
    public function storeCommentsAction(Store $store, Request $request)
    {
        $form = $this->createCommentsForm($store, $request);

        if ($form->isValid()) {
            $this->persistComments($store);

            return $this->redirectToRoute("admin_comment_index");
        }

        return [
            'store' => $store,
            'form' => $form->createView(),
        ];
    }

    /**
     * Create form for manipulating store comments
     *
     * @param Store $store
     * @param Request $request
     * 
     * @return Form
     * @author Michael Strohyi
     **/
    private function createCommentsForm(Store $store, Request $request)
    {
        $form = $this->createForm(new CommentsType, $store);
        $form->handleRequest($request);

        return $form;
    }

    /**
     * Persist all store comments
     *
     * @param Store $store
     * 
     * @return void
     * @author Michael Strohyi
     **/
    private function persistComments(Store $store)
    {
        $entity_manager = $this->getDoctrine()->getEntityManager();

        $entity_manager->persist($store);
        $entity_manager->flush();
    }
}
