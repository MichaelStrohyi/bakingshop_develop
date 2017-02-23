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
        // !!! stub
    }
}