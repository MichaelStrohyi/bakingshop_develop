<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use AppBundle\Form\CommentType;
use AppBundle\Entity\Store;
use AppBundle\Entity\Comment;

class StoreController extends Controller
{
    /**
     * @Route("/{prefix}store/{id}/coupons", name="store_coupons_page",
     *     requirements={"id": "\d+", "prefix": "amp/|"},
     *     defaults={"prefix": ""},
     * )
     * @ParamConverter("store", class="AppBundle:Store")
     */
    public function couponsAction(Store $store, Request $request, $prefix = null)
    {
        # get from-amp flag from parameters
        $amp_flag = $request->query->get('a');
        $cur_date = new \DateTimeImmutable();
        $comment = new Comment;
        $form = $this->createCommentForm($comment, $request);
        $formView = $form->createView();
        $set_focus = false;
        $comment_status = '';
        if ($form->isSubmitted() && $form->isValid()) {
            $comment_status = 'Your comment have been added and will be shown after moderation!';
            if (!$this->getDoctrine()->getRepository('AppBundle:Comment')->commentExists($comment->getLabel(), $comment->getAuthor(), $comment->getEmail(), $store->getId())) {
                $comment->setAddedDate(new \DateTimeImmutable);
                $comment->setStore($store);
                $this->persistComment($comment);
            } else {
                $comment_status = 'Your comment is already added!';
            }
        $comment = new Comment;
        $form = $this->createCommentForm($comment, new Request);
        $formView = $form->createView();
        }

        $parameters = [
            'store' => $store,
            'crosslink' => $this->generateUrl('homepage', [], true)  . $this->getDoctrine()->getRepository("USPCPageBundle:Page")->createCrossLink($prefix, $this->container->getParameter('amp_prefix'), $request->getPathInfo()),
            'menus' => $this->getDoctrine()->getRepository('AppBundle:Menu')->findAllbyPosition(),
            'cur_date' => $cur_date->format('F Y'),
            'title_discount' => $store->getMainDiscount(true),
            'form' => $formView,
            'comment_status' => $comment_status,
        ];

        # if prefix is not set render html page
        if (empty($prefix)) {
            return $this->render('AppBundle:Store:coupons.html.twig', $parameters);
        }

        # if amp_flag is set render amplified html page otherwise render amp-html page
        return ($amp_flag == 'a') ? $this->render('AppBundle:amplified/Store:coupons.html.twig', $parameters) : $this->render('AppBundle:amp/Store:coupons.html.twig', $parameters);
    }

    /**
     * @Route("/go/{store_name},{type},{id}", name="out_link",
     *     defaults={"coupon_id": null},
     * )
     */
    public function goAction($type, $id)
    {
        switch ($type) {
            case 'cp':
                 $res = $this->getDoctrine()->getRepository('AppBundle:Coupon')->findLinkById($id);
                break;
            case 'st':
                 $res = $this->getDoctrine()->getRepository('AppBundle:Store')->findLinkById($id);
                break;
            default:
                $res = null;
                break;
        }
        if (empty($res)) {
            throw $this->createNotFoundException();
        }

        return  $this->redirect($res, 301);
    }

    /**
     * Create form for adding comment
     *
     * @param Comment $comment
     * @param Request $request
     *
     * @return Form
     *
     * @author Michael Strohyi
     **/
    private function createCommentForm(Comment $comment, Request $request)
    {
        $form = $this->createForm(new CommentType, $comment);
        $form->handleRequest($request);

        return $form;
    }

    /**
     * Save given comment into database
     *
     * @param Comment $comment
     *
     * @return void
     * @author Michael Strohyi
     **/
    private function persistComment(Comment $comment)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($comment);
        $em->flush();
    }

}
