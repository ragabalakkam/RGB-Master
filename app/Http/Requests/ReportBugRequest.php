<?php

namespace App\Http\Requests;

class ReportBugRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'message' => [
                'required',
            ],
        ];
    }
}
