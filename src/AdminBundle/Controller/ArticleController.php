<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Article;
use AdminBundle\Form\ArticleType;

/**
 * @Route("/article")
 */
class ArticleController extends PageController
{
    /**
     * @Route("/", name="admin_article_index")
     * @Template()
     */
    public function indexAction()
    {        
        $article_list = $this->getDoctrine()->getRepository("AppBundle:Article")->findAllByHeader();
        return [
            'article_list' => $article_list,
        ];
    }

    /**
     * Create new article
     *
     * @param  Request  $request
     * 
     * @return Template
     * @author Michael Strohyi
     *
     * @Route("/new", name="admin_article_create")
     * @Template()
     **/
    public function createAction(Request $request)
    {
        $article = new Article;
        $form = $this->createArticleForm($article, $request);

        if ($form->isValid()) {
            $this->persistArticle($article);

            return $this->redirectToRoute("admin_article_index");
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Update article
     *
     * @param  Article  $article
     * @param  Request  $request
     * 
     * @return Template
     * @author Michael Strohyi
     *
     * @Route("/{id}/edit", name="admin_article_edit", requirements={"id": "\d+"})
     * @ParamConverter("article", class="AppBundle:Article")
     * @Template()
     **/
    public function editAction(Article $article, Request $request)
    {
        $form = $this->createArticleForm($article, $request);

        if ($form->isValid()) {
            $this->persistArticle($article);

            return $this->redirectToRoute("admin_article_index");
        }
        return [
            'article' => $article,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete given article
     *
     * @param  Article  $article
     * @param  Request  $request
     * 
     * @return Template
     * @author Michael Strohyi
     *
     * @Route("/{id}/delete", name="admin_article_delete")
     * @ParamConverter("article", class="AppBundle:Article")
     * @Template()
     **/
    public function deleteAction(Article $article, Request $request)
    {
        $form = $this->createFormBuilder([])->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity_manager = $this->getDoctrine()->getEntityManager();

            $entity_manager->remove($article);
            $entity_manager->flush();

            $this->deletePageUrls(Article::PAGE_TYPE, $article);

            return $this->redirectToRoute("admin_article_index");
        }

        return [
            'article' => $article,
            'form' => $form->createView(),
        ];
    }

    /**
     * Save given article into database
     *
     * @param  Article  $article
     * 
     * @return void
     * @author Michael Strohyi
     **/
    private function persistArticle(Article $article)
    {
        $entity_manager = $this->getDoctrine()->getEntityManager();

        $entity_manager->persist($article);
        $entity_manager->flush();
        
        # add/update article url in database
        $this->updatePageUrls(Article::PAGE_TYPE, $article);
    }

     /**
     * Return form for create/edit article
     *
     * @param Article $article
     * @param Request $request
     *
     * @return Form
     * @author Michael Strohyi
     **/
    private function createArticleForm(Article $article, Request $request)
    {
            $form = $this->createForm(new ArticleType(), $article);
            $form->handleRequest($request);

            return $form;
    }

}
