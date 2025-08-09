<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\PhoneNew;
use Illuminate\Foundation\Http\FormRequest;

class DemoRequest extends FormRequest
{

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone_number' => [
                'fart',
                'required',
                'string',
                // 'phone',
//                new PhoneNew(),
                'phone_new',
                'max:255',
            ]
        ];
    }
}
