<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\Image;

# single image at a time

class ImagesController extends Controller
{

    private static $prefix = 'App\\Models\\';

    private static $map = [
        'User'                      => 'Users',
        'Clients\Client'            => 'clients',
        'Apps\App'                  => 'apps',
    ];

    #=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

    public static function store($file, $model)
    {
        if (!is_image($file)) {
            $model->image;
            return null;
        }

        $class = get_class($model);
        $dir = self::$map[substr($class, strlen(self::$prefix))];

        $image = Image::create([
            'imageable_type'    => $class,
            'imageable_id'      => $model->id,
            'src'               => Storage::disk('public')->put($dir, $file),
        ]);

        self::updateModel($model, $image);

        return $image;
    }

    public static function update($file, $model)
    {
        if (!is_image($file)) {
            $model->image;
            return null;
        }

        self::destroy($model->image);
        return self::store($file, $model);
    }

    public static function destroy($image)
    {
        if (!isset($image->src)) return false;

        if ($image->src[0] != '-') Storage::disk('public')->delete($image->src);
        self::updateModel($image->imageable, $image);
        return $image->delete();
    }

    public static function updateModel($model, $image)
    {
        unset($model->image);
        $model->image = $image;
    }
}
