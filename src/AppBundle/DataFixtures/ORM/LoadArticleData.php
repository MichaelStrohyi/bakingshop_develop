<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Article;

class LoadArticleData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $article1 = new Article;
        $article1
            ->setUrl("/first_article")
            ->setHeader("This is our first article")
            ->setBody("The text of our first article will be here")
        ;
        $manager->persist($article1);

        $article2 = new Article;
        $article2
            ->setUrl("/second_article")
            ->setHeader("It is the second article")
            ->setBody("The second article is not very long")
        ;
        $manager->persist($article2);

        $manager->flush();
    }
}