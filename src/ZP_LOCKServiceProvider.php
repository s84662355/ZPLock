<?php
namespace ZPLock;

use Zookeeper;
use Throwable;


class ZP_LOCKServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        
    ];


    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config' => config_path()], 'ZPLOCK-config');
        }


        $this->app->singleton(
            'ZPLOCK',
            function (){
                  return new  LockControl(config('zp_lock'));
            }
        );
    }



    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }

}
