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
    const COMMENT_OPERATOR_EMAIL = 'operator123@bakingshop.com';
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
        # get current date
        $cur_date = new \DateTimeImmutable();
        # create new comment and form for it
        $comment = new Comment;
        $form = $this->createCommentForm($comment, $request);
        $formView = $form->createView();
        $comment_status = '';
        # check if form was submitted and is valid
        if ($form->isSubmitted() && $form->isValid()) {
            # set comment status "coment added"
            $comment_status = 'Your comment have been added and will be shown after moderation!';
            # remove html tags from comment
            $comment->setLabel(strip_tags($comment->getLabel()));
            $comment->setAuthor(strip_tags($comment->getAuthor()));
            # try to find comment for current store with the same label, author and email in db
            if (!$this->getDoctrine()->getRepository('AppBundle:Comment')->commentExists($comment, $store->getId())) {
                # if comment doesn't exist set current date as comment's addedDate
                $comment->setAddedDate($cur_date);
                # set current store as comment's store
                $comment->setStore($store);
                # check if comment has email, used by of operators
                if (strtolower($comment->getEmail()) === strtolower(self::COMMENT_OPERATOR_EMAIL)) {
                    # if current email is operator's email lowercase it and verify current comment
                    $comment->setEmail(self::COMMENT_OPERATOR_EMAIL);
                    $comment->setIsVerified(true);
                }
                # save comment into db
                $this->persistComment($comment);
            } else {
                # if comment already exists in db set comment status "already added"
                $comment_status = 'Your comment is already added!';
            }

            # create new comment and form for it
            $comment = new Comment;
            $form = $this->createCommentForm($comment, new Request);
            $formView = $form->createView();
        }
        # set all needed parameters
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
