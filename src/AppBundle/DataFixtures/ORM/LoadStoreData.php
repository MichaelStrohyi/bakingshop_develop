<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Store;

class LoadMenuData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $store1 = new Store;
        $store1
            ->setUrl("http://walmart.com")
            ->setName("Walmart")
        ;
        $manager->persist($store1);

        $store2 = new Store;
        $store2
            ->setUrl("http://amazon.com")
            ->setName("Amazon")
            ->setDescription("Amazon - the great store")
        ;
        $manager->persist($store2);

        $store3 = new Store;
        $store3
            ->setUrl("https://aliexpress.com")
            ->setName("Aliexpress")
            ->setKeywords("aliexpress china market")
        ;
        $manager->persist($store3);

        $manager->flush();
    }
}