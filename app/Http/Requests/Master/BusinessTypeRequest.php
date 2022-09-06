<?php

namespace App\Http\Requests\Master;

use App\Rules\LocalizedName;
use App\Rules\NotEmptyArray;
use App\Http\Requests\BaseFormRequest;

class BusinessTypeRequest extends BaseFormRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'name'              => json_decode($this->name, true),
            'app_id'            => !isNull($this->app_id) ? $this->app_id : null,
            'description'       => !isNull($this->description) ? $this->description : null,
            'sales_systems'     => json_decode($this->sales_systems, true),
            'translations'      => json_decode($this->translations, true),
            'cashier_settings'  => json_decode($this->cashier_settings, true),
            'modules'           => json_decode($this->modules, true),
            'forms'             => json_decode($this->forms, true),
            'database'          => json_decode($this->database, true),
        ]);

        foreach (['idle', 'busy', 'reserved'] as $type) {
            $image = $this->{$type};
            $this->merge([$type => !isNull($image) ? $image : null]);
        }

        $this->merge([
            'database_file'     => $this->database['option'] == 'file' && !isNull($this->database_file) ? $this->database_file : null,
        ]);
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                new LocalizedName,
            ],
            'app_id' => [
                'required',
                'exists:apps,id',
            ],
            'description' => [
                'nullable',
                new LocalizedName,
            ],
            'sales_systems' => [
                'required',
                new NotEmptyArray,
            ],
            'translations' => [
                'required',
                'array',
            ],
            'cashier_settings' => [
                'required',
                new NotEmptyArray,
            ],
            'modules' => [
                'required',
                'array',
            ],
            "database_file" => [
                $this->database['option'] == 'file' && !isset($this->id) ? 'required' : 'nullable',
            ],
        ];
    }
}
