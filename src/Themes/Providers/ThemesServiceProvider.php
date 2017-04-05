<?php

namespace Myrtle\Core\Themes\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Myrtle\Themes\Facades\Themes;
use Myrtle\Themes\Support\Store;

class ThemesServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->bootBladeDirectives();
		$this->bootAssetFinder();

        //$address = '3053 Brereton Street Apt 3 Pittsburgh, PA 15219';
        //
        //$address = str_replace(" ", "+", "$address");
        //$url = "http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false";
        //$result = file_get_contents("$url");
        //$json = json_decode($result);
        //foreach ($json->results as $result) {
        //    foreach($result->address_components as $addressPart) {
        //        if((in_array('locality', $addressPart->types)) && (in_array('political', $addressPart->types)))
        //            $city = $addressPart->long_name;
        //        else if((in_array('administrative_area_level_1', $addressPart->types)) && (in_array('political', $addressPart->types)))
        //            $state = $addressPart->long_name;
        //        else if((in_array('country', $addressPart->types)) && (in_array('political', $addressPart->types)))
        //            $country = $addressPart->long_name;
        //    }
        //}
        //
        //// return $address;
        //dd($result->address_components);
	}

	protected function bootBladeDirectives()
	{
		Blade::directive('blocks', function ($expression)
		{
			return "<?php echo view('blocks.users.index')->render(); ?>";
		});

		Blade::directive('hasSection', function ($expression)
		{
			return "<?php if(View::hasSection($expression)): ?>";
		});

		Blade::directive('endHasSection', function ($expression)
		{
			return '<?php endif; ?>';
		});
	}

	protected function bootAssetFinder()
	{
		// https://laracasts.com/discuss/channels/laravel/linking-assets-outside-of-public
		Themes::enabled()->each(function ($theme, $key)
		{
			Route::get('/public/assets/themes/' . $theme->type() . '/' . $theme->name . '/{path}', function ($path) use ($theme)
			{
				$file = $theme->publicPath() . '/' . $path;

				if (File::exists($file))
				{
					if (File::extension($file) == 'js')
					{
						return response()->file($file, ['Content-Type' => 'application/javascript']);
					} elseif (File::extension($file) == 'css')
					{
						return response()->file($file, ['Content-Type' => 'text/css']);
					} else
					{
						return response()->file($file, ['Content-Type' => File::mimeType($file)]);
					}
				}
			})->where('path', '(.*)');
		});
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerAppBindings();

		$this->registerEnabledThemes();
	}

	protected function registerAppBindings()
	{
		App::singleton('themes', Store::class);
	}

	protected function registerEnabledThemes()
	{
		Themes::enabled()->each(function ($theme, $key)
		{
			$theme->registerables()->each(function ($registerable, $k)
			{
				// as a reference $k could look like gate.definitions
				// convert this instance of a registerable Config item
				// to it's corresponding method on Docks\Repository
				// i.e. registerGateDefinitions

				$method = 'register' . Str::ucfirst(Str::camel(Str::replaceFirst('.', '_', $k)));

				// call that method on the Docks\Repository
				// pass it the registerable Config value
				Themes::$method($registerable);
			});

			View::addNamespace($theme->type(), base_path() . $theme->viewPath());
			View::addNamespace('pagination', base_path() . $theme->viewPath() . '/pagination');
			Paginator::defaultView('pagination::default');
			Paginator::defaultSimpleView('pagination::simple');
		});
	}
}
