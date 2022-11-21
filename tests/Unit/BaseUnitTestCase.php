<?php

namespace Test\Unit;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class BaseUnitTestCase extends MockeryTestCase
{
    public function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }
}