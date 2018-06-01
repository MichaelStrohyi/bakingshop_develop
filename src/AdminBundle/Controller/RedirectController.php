<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Redirect;
use AdminBundle\Form\RedirectsType;

class RedirectController extends Controller
{
    /**
     * Display all items in given menu.
     *
     * @param Request $request
     * 
     * @return Template
     * @author Michael Strohyi
     *
     * @Route("/redirect", name="admin_redirect")
     * @Template()
     **/
    public function indexAction(Request $request)
    {
        # get all redirects
        $repo = $this->getDoctrine()->getRepository('AppBundle:Redirect');
        $redirects = $repo->findAll();
        # create form
        $form = $this->createRedirectsForm($redirects, $request);
        # check if form has been submitted and is valid
        if ($form->isValid()) {
            # save all redirects
            $this->saveRedirectsChanges($form->getData()['items']);

            return $this->redirectToRoute("admin_panel_index");
        }

        return [
            'form' => $form->createView(),
        ];
    }


    /**
     * Return form for manage redirects
     *
     * @param array $redirects
     * @param Request $request
     *
     * @return FormBuilder
     *
     * @author Michael Strohyi
     **/
    private function createRedirectsForm($redirects, Request $request)
    {
        $form = $this->createForm(new RedirectsType, ['items' => $redirects]);
        $form->handleRequest($request);

        return $form;
    }

    /**
     * Get redirects info from given array, analyze changes and save them
     *
     * @param array $redirects
     * @return void
     * @author Michael Strohyi
     **/
    private function saveRedirectsChanges($redirects)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $redirects_ids = [];
        $articles_persist = [];
        $redirect_repo = $this->getDoctrine()->getRepository('AppBundle:Redirect');
        $article_repo = $this->getDoctrine()->getRepository('AppBundle:Article');
        $page_repo = $this->getDoctrine()->getRepository('USPCPageBundle:Page');
        # go through redirects
        foreach ($redirects as $redirect) {
            # check if current redirect is new or already exists
            if (!empty($redirect->getId())) {
                # if redirect is not new
                # save id of current redirect into array
                $redirects_ids[] = $redirect->getId();
                # get original redirect's data
                $uow =  $em->getUnitOfWork();
                $original_data = $uow->getOriginalEntityData($redirect);
                $original_url = $page_repo->getUrlFromRes($original_data['url']);
                # check if redirect's url was edited
                if ($original_url !== $redirect->getUrl()) {
                    # find articles which used original url and current edited url of redirect and save them into list
                    $articles_persist += $article_repo->findByUsedRedirect($original_url);
                    $articles_persist += $article_repo->findByUsedRedirect($redirect->getUrl());
                }
                # check if redirect's prodUrl was edited
                if ($page_repo->getUrlFromRes($original_data['prodUrl']) !== $redirect->getProdUrl()) {
                    # find articles which  use current url of redirect and save them into list
                    $articles_persist += $article_repo->findByUsedRedirect($redirect->getUrl());
                }
            } else {
                #if redirect is new
                # find articles which use current url of redirect and save them into list
                $articles_persist += $article_repo->findByUsedRedirect($redirect->getUrl());
            }

            $em->persist($redirect);
        }

        # find deleted redirects
        $deleted_redirects = $redirect_repo->getAllRedirects($redirects_ids);
        # go through deleted redirects
        foreach ($deleted_redirects as $redirect) {
            # find articles which use current url of redirect and save them into list
            $articles_persist += $article_repo->findByUsedRedirect($redirect->getUrl());
            # remove deleted redirects from db
            $em->remove($redirect);
        }

        $em->flush();
        # go through list of articles, which need to be re-saved to update prodBody because of redirects changes
        foreach ($articles_persist as $article) {
            # save article
            $em->persist($article);
        }

        $em->flush();
    }
}
