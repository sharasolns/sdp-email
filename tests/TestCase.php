<?php

namespace Sdp\Email\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Sdp\Email\SdpEmailServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [SdpEmailServiceProvider::class];
    }
}
