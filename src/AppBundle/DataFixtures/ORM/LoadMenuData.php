<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Menu;
use AppBundle\Entity\MenuItem;

class LoadMenuData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $homepage = new MenuItem;
        $homepage
            ->setUrl("/")
            ->setTitle("BakingShop")
            ->setPosition(0)
        ;
        $manager->persist($homepage);

        $cake_decoration = new MenuItem;
        $cake_decoration
            ->setUrl("/cake-decoration")
            ->setTitle("Cake Decoration")
            ->setPosition(1)
        ;
        $manager->persist($cake_decoration);

        $news = new MenuItem;
        $news
            ->setUrl("/news")
            ->setTitle("News")
            ->setPosition(2)
        ;
        $manager->persist($news);

        $top_menu = new Menu();
        $top_menu
            ->setName("top-navigation")
            ->setHeader("Navigation")
            ->addItem($homepage)
            ->addItem($cake_decoration)
            ->addItem($news)
        ;

        $manager->persist($top_menu);


        $privacy_policy = new MenuItem;
        $privacy_policy
            ->setUrl("/privacy-policy")
            ->setTitle("Privacy Policy")
            ->setPosition(0)
        ;
        $manager->persist($privacy_policy);

        $shipping_information = new MenuItem;
        $shipping_information
            ->setUrl("/shipping-information")
            ->setTitle("Shipping Information")
            ->setPosition(1)
        ;
        $manager->persist($shipping_information);

        $feedback = new MenuItem;
        $feedback
            ->setUrl("/feedback")
            ->setTitle("Feedback")
            ->setPosition(2)
        ;
        $manager->persist($feedback);


        $bottom_menu = new Menu;
        $bottom_menu
            ->setName("footer")
            ->setHeader("Shopping with us is safe and secure")
            ->addItem($privacy_policy)
            ->addItem($shipping_information)
            ->addItem($feedback)
        ;
        $manager->persist($bottom_menu);

        $manager->flush();
    }
}