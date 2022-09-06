<?php

use Illuminate\Support\Facades\Artisan;

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

function success_msg($action, $text)
{
	echo $action->getStatusCode() == 200 ? "☑ Reset $text successfully<br />" : $action->original;
}

function artisan_msg($command)
{
	Artisan::call($command);
	echo "Artisan ($command) : " . Artisan::output() . " <br />";
}

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

try {

	$url = getConfig('url');
	$timeout = 1; // in secs

	if (!file_exists(base_path('.env')))
		throw new \Exception('Cannot find environment file');

	artisan_msg('optimize');
	artisan_msg('config:cache');
	artisan_msg('view:clear');
	artisan_msg('cache:clear');
	artisan_msg('route:cache');
	artisan_msg('storage:link');

	success_msg(app('App\Http\Controllers\API\Lang\LocalesController')->index(), 'locales & translations');
	success_msg(app('App\Http\Controllers\API\Locations\LocationsController')->index(), 'locations');
	success_msg(app('App\Http\Controllers\API\ConfigurationsController')->index(), 'configurations');

	echo "<br/>☑ All done <br/>";
	
	// redirect to $url after $timeout seconds
	echo "Redirecting to <a href='$url'> $url</a> in $timeout seconds ..";
	echo "<script>setTimeout(() => window.location.replace('$url'), " . ($timeout * 1000) . ");</script>";

} catch (\Exception $e) {
	?>
		<div style="height: 100%; display: flex; align-items: center; justify-content: center;">
			<div style="text-align: center">
				<p style="font-size: 2.5rem; margin-bottom: 2rem;">{{ $e->getMessage() }}</p>
				<a href="{{ getConfig('url') }}/auth/login">عودة إلى الرئيسية</a>
			</div>
		</div>
	<?php
}