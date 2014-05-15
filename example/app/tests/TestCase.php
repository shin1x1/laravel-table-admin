<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     *
     */
    public function setUp()
    {
        parent::setUp();

        Config::set('app.url', 'a');
    }



    /**
     * Creates the application.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $unitTesting = true;

        $testEnvironment = 'testing';

        return require __DIR__ . '/../../bootstrap/start.php';
    }

}
