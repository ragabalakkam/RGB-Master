<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Shortlink extends BaseModel
{
    use HasFactory;

    protected $primaryKey = 'short';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'short',
        'link',
        'visited_at',
        'expires_at',
    ];  

    protected $dates = [
        'visited_at',
        'expires_at',
    ];

    protected $appends = [
        'shortlink',
    ];


    # appends

    public function getShortlinkAttribute()
    {
        return str_replace('http://', '', str_replace('https://', '', getConfig('url'))) . "/" . $this->short;
    }


    # functions

    public static function make($link, $expires_at = null, $length = 5)
    {
        do {
            $short = Str::random($length);
        } while (Shortlink::where('short', $short)->count());

        return Shortlink::create([
            'link'          => $link,
            'short'         => $short,
            'expires_at'    => $expires_at,
        ]);
    }

    public function setVisited()
    {
        return $this->update(['visited_at' => now()]);
    }

    public function setExpired()
    {
        return $this->update(['expires_at' => now()]);
    }
}
