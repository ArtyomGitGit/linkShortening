<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateShortenLinkRequest extends FormRequest
{
    public function rules() : array
    {
        return [
            'link' => 'required|url|unique:links,original|max:380',
        ];
    }

    protected function failedValidation(Validator $validator) : void
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
