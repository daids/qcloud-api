<?php 
namespace Daids\QcloudApi;

use Illuminate\Support\ServiceProvider;
use Daids\QcloudApi\Module\YunsouApi;

class QcloudApiServicePorvider extends ServiceProvider
{
	protected $defer = true;
	
	public function register()
	{
		$this->app->bind('qcloud.yunsou', function($app){
			return new YunsouApi($app->config);
		});
	}

    public function boot()
    {
		$this->publishes([
			__DIR__.'/config/qcloud.php'  => config_path('qcloud.php'),
		]);
	}


	public function provides()
	{
		return ['qcloud.yunsou'];
	}
}