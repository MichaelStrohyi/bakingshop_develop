<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageControllerTest extends WebTestCase
{
    public function testSidebarmenu()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/sidebarMenu');
    }

}
