<?php

namespace App\Traits;

use Exception;

# controllers
use App\Http\Controllers\API\ImagesController;

# models
use App\Models\Image;

trait HasImage
{
    # observers

    // public static function boot()
    // {
    //     parent::boot();
    //     self::creating(function ($model) { });
    //     self::updating(function ($model) { });
    // }


    # relationships

    public function image()
    {
        return $this->morphOne('App\Models\Image', 'imageable');
    }

    public function images()
    {
        return $this->morphMany('App\Models\Image', 'imageable');
    }


    # functions

    public function storeImg($image)
    {
        return ImagesController::store($image, $this);
    }

    public function updateImg($image)
    {
        return ImagesController::update($image, $this);
    }

    public function deleteImg()
    {
        ImagesController::destroy($this->image);
    }
}
