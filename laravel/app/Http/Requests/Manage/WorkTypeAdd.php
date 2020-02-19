<?php

namespace App\Http\Requests\Manage;

use Illuminate\Foundation\Http\FormRequest;

class WorkTypeAdd extends FormRequest
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
            'code' => 'required|alpha_num_check|max:10',
            'name' => 'required|string|max:50',
        ];
    }

    public function attributes()
    {
        return [
            'code' => 'コード',
            'name' => '名称',
        ];
    }
}
