<?php

namespace App\Models\Configurations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

# models
use App\Models\Configuration;

class Group extends BaseModel
{
    use HasFactory;

    public $table = 'configuration_groups';
    public $timestamps = false;

    protected $fillable = [
        'key',
        'hidden',
        'disabled',
    ];

    protected $casts = [
        'hidden'    => 'boolean',
        'disabled'  => 'boolean',
    ];


    # appends

    protected $appends = [
        'value',
    ];

    public function getValueAttribute()
    {
        $arr = [];

        foreach ($this->configurations as $config) {
            $arr[$config->key] = $config->value;
        }

        unset($this->configurations);

        return $arr;
    }

    public function setValueAttribute($data)
    {
        foreach ($data as $key => $value) {
            Configuration::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }


    # relationships

    public function configurations()
    {
        return $this->hasManyThrough(Configuration::class, ConfigurationGroup::class, 'configuration_group_id', 'id', 'id', 'configuration_id');
    }

    public function configuration_groups()
    {
        return $this->hasMany(ConfigurationGroup::class, 'configuration_group_id');
    }


    # functions

    public function update(array $attributes = [], array $options = [])
    {
        $this->value = $attributes['value'];
        parent::update($attributes, $options);
    }

    
    # static functions

    public static function createFromKeys($data)
    {
        $group = self::create(['key' => $data['key']]);

        foreach ($data['keys'] as $key) {
            $group->configuration_groups()->create(['configuration_id' => getConfigModel($key)->id]);
        }

        $group = self::find($group->id);
        $group->value;
        return $group;
    }
}