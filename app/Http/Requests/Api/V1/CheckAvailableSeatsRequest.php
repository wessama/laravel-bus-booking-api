<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Knuckles\Scribe\Attributes\BodyParam;

#[BodyParam('start_station', 'int', "Where the user's trip will start.")]
#[BodyParam('end_station', 'int', "Where the user's trip will end.")]
class CheckAvailableSeatsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize() : bool
    {
        return Auth::guard('api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules() : array
    {
        return [
            'start_station' => 'required|exists:stations,id',
            'end_station' => 'required|exists:stations,id',
        ];
    }
}
