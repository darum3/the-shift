<?php

namespace App\Http\Requests\Member;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class DesiredRegister extends FormRequest
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
            '*.target_date' => ['required', 'date'],
            '*.desired' => ['required', 'array'],
            '*.desired.*.work_type' => ['nullable',
                Rule::exists('work_types', 'code')->where(function($query) {
                    $query->where('contract_id', session('contract_id'));
                }),
            ],
            '*.desired.*.start' => ['required', 'date_format:Hi'],
            '*.desired.*.end' => ['required', 'date_format:Hi'],
        ];
    }
}
