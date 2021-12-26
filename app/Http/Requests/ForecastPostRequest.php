<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class ForecastPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'city_name' => 'required|max:100',
            'state_code' => 'required|max:100',
            'country_code' => 'required|max:100',
        ];
    }

    public function messages()
    {
        return [
            'city_name.required' => 'City Name is required',
            'city_name.max' => 'Maximum 100 characters only!',
            'state_code.required' => 'State Code is required',
            'state_code.max' => 'Maximum 100 characters only for State Code!',
            'country_code.required' => 'Country Code is required',
            'country_code.max' => 'Maximum 100 characters only for Country Code!'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            // response()->json(['result' => false, 'message' => json_encode($errors)], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            response()->json(['result' => false, 'message' => 'Invalid Parameters.'], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
