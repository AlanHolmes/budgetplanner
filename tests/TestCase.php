<?php

namespace Tests;

use App\Exceptions\Handler;
use Exception;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp()
    {
        parent::setUp();

        TestResponse::macro('data', function ($key) {
            return $this->original->getData()[$key];
        });
    }

    /**
     * Disables exception handling, so you can see the actual errors rather than a caught exception
     *
     * @author Alan Holmes
     */
    protected function disableExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, new class extends Handler {
            public function __construct() {}
            public function report(Exception $e) {}
            public function render($request, Exception $e) {
                throw $e;
            }
        });
    }

    /**
     * set the url that you are posting from
     *
     * useful when you want to test being redirected back to a form
     *
     * @param $url
     *
     * @return $this
     * @author Alan Holmes
     */
    protected function from($url)
    {
        session()->setPreviousUrl(url($url));
        return $this;
    }
}
