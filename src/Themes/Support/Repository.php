<?php

namespace Myrtle\Core\Themes\Support;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

abstract class Repository {

	protected $themeType;

	protected $themes;

	public function __construct()
	{
		$this->buildThemes();
	}

	protected function buildThemes()
	{
		$this->themes = collect(Config::get('themes.' . $this->themeType))->transform(function ($theme, $key)
		{
			$class = (new \ReflectionClass($this))->getShortName();
			return new Theme($key, Str::lower($class));
		});
	}

	public function get($name)
	{
		return $this->all()->reject(function ($theme, $key) use ($name)
		{
			return $theme->name !== $name;
		})->first();
	}

	public function all()
	{
		return $this->themes();
	}

	public function themes()
	{
		return $this->themes;
	}

	public function enabled()
	{
		return $this->all()->reject(function ($theme, $key)
		{
			return ! $theme->enabled();
		})->first();
	}

	public function isDisabled($name)
	{
		return ! $this->isEnabled($name);
	}
}