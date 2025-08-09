<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class DemoTest extends TestCase
{
    public function testSuccess(): void
    {
        $response = $this->get('/demo');

        $response->assertOk();
    }
}
