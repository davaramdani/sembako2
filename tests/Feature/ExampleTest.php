<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_home_page_redirects_to_the_shop(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/shop');
    }
}
