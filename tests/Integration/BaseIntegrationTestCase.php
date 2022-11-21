<?php

namespace Test\Integration;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class BaseIntegrationTestCase extends MockeryTestCase
{
    public function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }
}
