<?php


declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetNearbyStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'postcode' => 'required|string|exists:postcodes,postcode',
            'radius' => 'nullable|integer|min:1',
            'delivery' => 'nullable|string|in:true,false',
            'per_page' => 'nullable|integer|min:1|max:50',
        ];
    }

    /**
     * Prepare user inputted postcode for validation by trimming unnecessary spaces.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'postcode' => str_replace(' ', '', $this->postcode),
        ]);
    }
}
