<?php

namespace Domain\Shared\Foundation\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest as Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

abstract class FormRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the error messages for the defined validation rules.*
     *
     *
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator): void
    {
        value(
            value: function (ValidationException $e) {
                $e->response = response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'data' => $e->errors(),
                ], status: Response::HTTP_UNPROCESSABLE_ENTITY);

                throw $e;
            },

            e: new ValidationException($validator)
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    abstract public function rules(): array;
}
