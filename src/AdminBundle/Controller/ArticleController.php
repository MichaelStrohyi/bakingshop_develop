<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Article;

class ArticleController extends Controller
{
    /**
     * @Route("/article")
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
        return [
        ];
    }
}
