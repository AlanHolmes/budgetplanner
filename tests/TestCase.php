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

    protected $old_attributes =[];
    protected $valid_params =[];

    protected function setUp()
    {
        parent::setUp();

        TestResponse::macro('data', function ($key) {
            return $this->original->getData()[$key];
        });
    }

    /**
     * Get the tests default old attributes, and merges in any overrides
     *
     * @param array $overrides
     *
     * @return array
     * @author Alan Holmes
     */
    protected function oldAttributes($overrides = [])
    {
        return array_merge($this->old_attributes, $overrides);
    }

    /**
     * Gets the tests default valid params, and merges in any overrides
     *
     * @param array $overrides
     *
     * @return array
     * @author Alan Holmes
     */
    protected function validParams($overrides = [])
    {
        return array_merge($this->valid_params, $overrides);
    }

    /**
     * Checks that the model attributes are the same as oldAttributes
     * with given data overrides
     *
     * @param $model
     * @param $data
     * @param $useOldAttributes
     *
     * @author Alan Holmes
     */
    protected function assertModelMatchesData($model, $data, $useOldAttributes = true)
    {
        if ($useOldAttributes) {
            $data = $this->oldAttributes($data);
        }

        $this->assertArraySubset(
            $data,
            $model->fresh()->getAttributes(),
            false,
            'The data in the model is not the same as the given data'
        );
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
