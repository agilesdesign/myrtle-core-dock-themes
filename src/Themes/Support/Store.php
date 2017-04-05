<?php

namespace Myrtle\Core\Themes\Support;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class Store
{

	public function __call($method, $parameters)
	{
		$type = '\App\Support\Components\Themes\\' . Str::ucfirst($method);

		return new $type;
	}

	public function enabled()
	{
		return collect([
			(new Admin)->enabled(),
			(new Auth)->enabled(),
			(new Front)->enabled(),
			(new Landing)->enabled(),
			(new Installer)->enabled(),
		])->reject(function ($theme, $key)
		{
			return is_null($theme);
		});
	}

	public function registerProviders(array $providers)
	{
		collect($providers)->each(function ($provider, $key)
		{
			$this->registerProvider($provider);
		});
	}

	public function registerProvider($provider)
	{
		if ( ! get_parent_class($provider) === ServiceProvider::class)
		{
			return;
		}

		App::register($provider);
	}
}