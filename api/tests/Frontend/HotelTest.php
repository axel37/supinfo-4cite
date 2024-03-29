<?php

namespace App\Tests\Frontend;

use Symfony\Component\Panther\PantherTestCase;

class HotelTest extends PantherTestCase
{
    public function disabled_testMyApp(): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/admin');



//        $client->waitFor('.RaLayout-appFrame');

        // use any PHPUnit assertion, including the ones provided by Symfony...
        $this->assertPageTitleContains('API Platform Admin');
//        $this->assertSelectorTextContains('#main', 'My body');

        // ... or the one provided by Panther
//        $this->assertSelectorIsEnabled('.search');
//        $this->assertSelectorIsDisabled('[type="submit"]');
//        $this->assertSelectorIsVisible('.errors');
//        $this->assertSelectorIsNotVisible('.loading');
//        $this->assertSelectorAttributeContains('.price', 'data-old-price', '42');
//        $this->assertSelectorAttributeNotContains('.price', 'data-old-price', '36');

        // ...
    }
}
