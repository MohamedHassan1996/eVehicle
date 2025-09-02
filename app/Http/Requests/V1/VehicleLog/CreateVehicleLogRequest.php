<?php

namespace App\Http\Requests\V1\VehicleLog;

use App\Enums\ResponseCode\HttpStatusCode;
use App\Helpers\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class CreateVehicleLogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'vehicleId' => ['required'],
            'weight' => ['required'],
            'weightType' => ['nullable', 'integer'],
            'note' => ['nullable', 'string'],
            'date' => ['nullable', 'date'],
        ];
    }
    public function failedValidation(Validator $validator)
    {
        /*throw new HttpResponseException(response()->json([
            'message' => $validator->errors()
        ], 422));*/

        throw new HttpResponseException(
            ApiResponse::error('', $validator->errors(), HttpStatusCode::UNPROCESSABLE_ENTITY)
        );
    }

    /*public function messages()
    {
        return [
            'username.unique' => __('validation.custom.username.unique'),
        ];
    }*/

}
