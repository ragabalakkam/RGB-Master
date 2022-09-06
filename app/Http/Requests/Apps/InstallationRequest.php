<?php

namespace App\Http\Requests\Apps;

# requests
use App\Http\Requests\BaseFormRequest;

# rules
use App\Rules\LocalizedName;
// use App\Rules\StartsWith;
// use App\Rules\UniqueExceptCurrent;

class InstallationRequest extends BaseFormRequest
{
    public function prepareForValidation()
    {
        $app_name = json_decode($this->name, true);

        $this->merge([
            'name'              => $app_name,
            'slogan'            => json_decode($this->slogan, true),
            'email'             => normalize_email(castNull($this->email)),
            'phone'             => castNull($this->phone),

            'address'           => json_decode($this->address, true),
            'location'          => json_decode($this->location, true),

            'apps'              => json_decode($this->apps, true),

            'tax_number'        => castNull($this->tax_number),
            'commercial_reg_no' => castNull($this->commercial_reg_no),
            'email_verified_at' => castNull($this->email_verified_at),
            'phone_verified_at' => castNull($this->phone_verified_at),
            'notes'             => castNull($this->notes),
            'extra'             => castNull($this->extra),

            'user_id'           => auth()->id() ?? null,
        ]);

        $this->merge([
            'full_address'      => getFullAddress($this->address) ?? null,
        ]);
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                new LocalizedName,
            ],
            "email" => [
                "nullable",
                // "email:rfc,dns",
                // new UniqueExceptCurrent(Client::class, $this->id),
            ],
            "phone" => [
                "required",
                // 'digits:12',
                // new StartsWith('966'),
                // new UniqueExceptCurrent(Client::class, $this->id),
            ],
            'tax_number' => [
                'nullable',
                // 'digits:15',
            ],
            'commercial_reg_no' => [
                'nullable',
                // 'digits:10',
            ],
        ];
    }
}