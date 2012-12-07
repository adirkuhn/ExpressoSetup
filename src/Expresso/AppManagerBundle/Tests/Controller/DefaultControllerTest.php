<?php

namespace Expresso\AppManagerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/expresso/appmanager/');

        $this->assertTrue($crawler->filter('html:contains("Hello")')->count() > 0);
    }
}
