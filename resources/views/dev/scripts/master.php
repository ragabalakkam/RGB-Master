<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

function update_master()
{
  $file = base_path('../MASTER-UPDATE.zip');

  if (!file_exists($file))
    return "File $file does not exist";

  if (extract_zip($file, '/home/rgbksaco/RGB/master')) {
    if (unlink($file)) {
      Artisan::call('locales:update');
      Http::get(getConfig('url') . "/dev@afaqrgb/clear");
      return 'Updated succesfully';
    }
  }

  return 'Failed to update master';
}
