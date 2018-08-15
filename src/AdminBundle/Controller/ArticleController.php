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
        $article_list = $this->getDoctrine()->getRepository("AppBundle:Article")->findAllByHeader(true);
        return [
            'article_list' => $article_list,
            'article_types' => Article::getTypes(),
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
            $this->handleLogo($article, $request);
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
        if ($article->getId() === 0) {
            return $this->redirectToRoute("admin_article_index");
        }

        $form = $this->createFormBuilder([])->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity_manager = $this->getDoctrine()->getEntityManager();
            $article_id = $article->getId();
            # delete article
            $entity_manager->remove($article);
            $entity_manager->flush();
            # delete urls for current article from db and from menu-items
            $this->deleteFromMenus(Article::PAGE_TYPE, $article, $article_id);
            $this->deletePageUrls(Article::PAGE_TYPE, $article, $article_id);

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
        $article_repo = $entity_manager->getRepository('AppBundle:Article');
        # get old article url from db
        $old_url = $article_repo->getUrlFromDB($article);
        # get old article header from db
        $old_header = $article_repo->findHeaderById($article->getId());
        # save article into db
        $entity_manager->persist($article);
        $entity_manager->flush();

        # add/update article url in database
        $this->updatePageUrls(Article::PAGE_TYPE, $article);
        $this->updateHomepage($article);
        # return at this point if it is a new article
        if (empty($article->getId())) {
            return;
        }

        $menu_item_repo = $entity_manager->getRepository('AppBundle:MenuItem');
        # update article url in menus if it was changed
        if ($article->getUrl() !== $old_url) {
            $menu_item_repo->updateUrls($old_url, $article->getUrl());
        }
        # update article header in menus if it was changed
        if ($article->getHeader() !== $old_header) {
            $menu_item_repo->updateTitles($old_header, $article->getHeader());
        }
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

    /**
     * Reset homepage flag for other articles
     *
     * @param  Article  $article
     * 
     * @return void
     * 
     * @author Mykola Martynov
     **/
    private function updateHomepage(Article $article)
    {
        if (!$article->isHomepage()) {
            return;
        }

        $article_repo = $this->getDoctrine()->getRepository('AppBundle:Article');
        $article_repo->resetHomepage($article);
    }

    /**
     * Remove article logo if crrent logo was deleted and new logo was not selected
     *
     * @param Article $article
     * @param Request $request
     *
     * @return void
     * @author Michael Strohyi
     **/
    private function handleLogo(Article $article, Request $request)
    {
        if (null === $request->request->get('current_logo') && (null === $article->getLogo() || null === $article->getLogo()->getImageFile())) {
            $article->removeLogo();
        }
    }
}
