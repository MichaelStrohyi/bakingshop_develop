<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
}
