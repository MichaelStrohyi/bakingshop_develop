<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\Article;

class ArticleController extends Controller
{
    /**
     * @Route("/article/{id}", name="article_page",
     *     requirements={"id": "\d+"}
     * )
     * @ParamConverter("article", class="AppBundle:Article")
     * @Template()
     */
    public function pageAction(Article $article)
    {
        return [
            'article' => $article,
        ];
    }
}
