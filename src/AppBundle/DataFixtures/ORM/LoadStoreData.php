<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Store;
use AppBundle\Entity\StoreCoupon;

class LoadMenuData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $coupon1 = new StoreCoupon;
        $coupon1
            ->setLabel("First coupon")
            ->setLink("http://first.coupon.link")
            ->setPosition(1)
            ->activate()
        ;
        $manager->persist($coupon1);

        $coupon2 = new StoreCoupon;
        $coupon2
            ->setLabel("Second coupon")
            ->setLink("http://second.coupon/link")
            ->setPosition(2)
            ->deactivate()
        ;
        $manager->persist($coupon2);

        $coupon3 = new StoreCoupon;
        $coupon3
            ->setLabel("Third coupon label")
            ->setLink("http://second.coupon/link")
            ->setPosition(0)
            ->activate()
            ->setCode("3rd")
        ;
        $manager->persist($coupon3);

        $store1 = new Store;
        $store1
            ->setLink("http://walmart.com")
            ->setName("Walmart")
            ->addCoupon($coupon1)
            ->addCoupon($coupon2)
            ->addCoupon($coupon3)
        ;
        $manager->persist($store1);

        $store2 = new Store;
        $store2
            ->setLink("http://amazon.com")
            ->setName("Amazon")
            ->setDescription("Amazon - the great store")
        ;
        $manager->persist($store2);

        $store3 = new Store;
        $store3
            ->setLink("https://aliexpress.com")
            ->setName("Aliexpress")
            ->setKeywords("aliexpress china market")
        ;
        $manager->persist($store3);

        $manager->flush();
    }
}