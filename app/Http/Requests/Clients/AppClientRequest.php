<?php

namespace App\Http\Requests\Clients;

use App\Http\Requests\BaseFormRequest;

# rules
use App\Rules\UniqueExceptCurrent;

# models
use App\Models\Apps\AppClient;
use App\Rules\LocalizedName;

class AppClientRequest extends BaseFormRequest
{
    public function prepareForValidation()
    {
        $unique_name = normalize(str_replace('https://', '', str_replace('.rgbksa.com', '', $this->domain)));
        $this->merge([
            'domain'    => castNull($unique_name) ? "https://$unique_name.rgbksa.com" : null,
            'root_dir'  => getConfig('clients_root_dir') . "/$unique_name",
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
            'client_id' => [
                'required',
                'exists:clients,id',
            ],
            'domain' => [
                $this->app_id == 1 ? 'required' : 'nullable',
                new UniqueExceptCurrent(AppClient::class, $this->id),
            ],
            'root_dir' => [
                'required',
                new UniqueExceptCurrent(AppClient::class, $this->id),
            ],
            'business_type_id' => [
                'required',
                'exists:business_types,id',
            ],
        ];
    }
}
