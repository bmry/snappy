<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\UniqueCoordinatesAndStoreName;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'location.0' => 'required|numeric',
            'location.1' => 'required|numeric',
            'location' => [
                'required',
                'array',
                'size:2',
                'min:2',
                new UniqueCoordinatesAndStoreName(),
            ],
            'status' => 'required|in:open,closed',
            'type' => 'required|in:takeaway,shop,restaurant',
            'max_delivery_distance' => 'required|integer|min:1',
        ];
    }


    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Store name is required.',
            'name.string' => 'Store name must be a valid string.',
            'location.required' => 'Location is required.',
            'location.size' => 'Location must consist of two numeric values: latitude and longitude.',
            'location.*.numeric' => 'Both latitude and longitude must be numeric values.',
            'status.required' => 'Store status is required.',
            'status.in' => 'Store status must be either "open" or "closed".',
            'type.required' => 'Store type is required.',
            'type.in' => 'Store type must be either "takeaway", "shop", or "restaurant".',
            'max_delivery_distance.required' => 'Max delivery distance is required.',
            'max_delivery_distance.integer' => 'Max delivery distance must be an integer.',
            'max_delivery_distance.min' => 'Max delivery distance must be at least 1.',
        ];
    }
}
