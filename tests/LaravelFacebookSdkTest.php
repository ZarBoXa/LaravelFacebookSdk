<?php

use Mockery as m;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;

class LaravelFacebookSdkTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var \Illuminate\Config\Repository
     */
    protected $config_mock;

    /**
     * @var LaravelFacebookSdk
     */
    protected $laravel_facebook_sdk;

    public function setUp()
    {
        $this->config_mock = m::mock('Illuminate\Config\Repository');
        $this->laravel_facebook_sdk = new LaravelFacebookSdk($this->config_mock, [
            'app_id' => 'foo_id',
            'app_secret' => 'foo_secret',
            'persistent_data_handler' => 'memory',
        ]);
    }

    public function tearDown()
    {
        m::close();
    }

    /** @test */
    public function when_no_arguments_are_passed_the_get_login_url_method_will_default_to_config()
    {
        $this->config_mock
            ->shouldReceive('get')
            ->with('laravel-facebook-sdk::default_scope')
            ->once()
            ->andReturn(['foo', 'bar']);
        $this->config_mock
            ->shouldReceive('get')
            ->with('app.url')
            ->once()
            ->andReturn('https://foohost');
        $this->config_mock
            ->shouldReceive('get')
            ->with('laravel-facebook-sdk::default_redirect_uri')
            ->once()
            ->andReturn('/foo');

        $login_url = $this->laravel_facebook_sdk->getLoginUrl();

        $this->assertContains('redirect_uri=https%3A%2F%2Ffoohost%2Ffoo', $login_url);
        $this->assertContains('scope=foo%2Cbar', $login_url);
    }

    /** @test */
    public function the_default_config_can_be_overwritten_by_passing_arguments_to_get_login_url()
    {
        $this->config_mock
            ->shouldReceive('get')
            ->never();

        $login_url = $this->laravel_facebook_sdk->getLoginUrl(['dance', 'totes'], 'https://poop.fart/callback');

        $this->assertContains('redirect_uri=https%3A%2F%2Fpoop.fart%2Fcallback', $login_url);
        $this->assertContains('scope=dance%2Ctotes', $login_url);
    }

}
