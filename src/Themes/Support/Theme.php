<?php

namespace Myrtle\Core\Themes\Support;

use ArrayAccess;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class Theme implements ArrayAccess
{
	public $name;

	protected $enabled;

	public $providers;

	protected $type;

	protected $registerables;

	protected $viewPath;

	protected $publicPath;

	public function __construct($name, $type)
	{
		$this->name = $name;
		$this->type = $type;
		$this->configBase = 'themes.' . $this->type . '.' . $this->name . '.';
		$this->buildRegisterableDictionary();
		$this->description = Config::get($this->configBase() . 'description');
		$this->publicPath = Config::get($this->configBase() . 'public.path');
		$this->viewPath = Config::get($this->configBase() . 'views.path');
		$this->enabled = $this->enabled();
		$this->providers = collect(Config::get($this->configBase() . 'providers', null));

	}

	protected function buildRegisterableDictionary()
	{
		$this->registerables =
			collect(Config::get($this->configBase() . 'registerable'), [])
				->keyBy(function ($registerable, $key)
				{
					return $registerable;
				})
				->transform(function ($registerable, $key)
				{
					return Config::get($this->configBase() . $registerable);
				});
	}

	public function configBase()
	{
		return $this->configBase;
	}

	public function enabled()
	{
		return env('THEMES_' . Str::upper($this->type) . '_ENABLED') === $this->name;
	}

	public function viewPath()
	{
		return $this->viewPath;
	}

	public function publicPath()
	{
		return $this->publicPath;
	}

	public function registerables()
	{
		return $this->registerables;
	}

	public function providers()
	{
		return $this->providers;
	}

	public function type()
	{
		return $this->type;
	}

	public function enable()
	{
		return;
	}

	public function disabled()
	{
		return ! $this->enabled();
	}

	public function offsetExists($key)
	{
		return $this->has($key);
	}

	public function has($key)
	{
		return Arr::has($this->options, $key);
	}

	public function offsetGet($key)
	{
		return $this->get($key);
	}

	public function get($key, $default = null)
	{
		return Arr::get($this->options, $key, $default);
	}

	public function offsetSet($key, $value)
	{
		$this->set($key, $value);
	}

	public function set($key, $value)
	{
		if (is_array($key))
		{
			foreach ($key as $innerKey => $innerValue)
			{
				Arr::set($this->options, $innerKey, $innerValue);
			}
		} else
		{
			Arr::set($this->options, $key, $value);
		}
	}

	public function offsetUnset($key)
	{
		$this->set($key, null);
	}
}