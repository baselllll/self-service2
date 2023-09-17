<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            putenv('DUMB_TERMINAL=true');
        }
    }

}
