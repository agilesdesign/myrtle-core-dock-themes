<?php

namespace Myrtle\Core\Themes\Facades;

use Illuminate\Support\Facades\Facade;

class Themes extends Facade {

	public static function getFacadeAccessor()
	{
		return 'themes';
	}
}