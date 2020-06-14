<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkTypeEdit extends FormRequest
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
            'work_color' => 'required|string|max:50',
            'version' => 'required|numeric',
        ];
    }

    public function attributes()
    {
        return [
            'code' => 'コード',
            'name' => '名称',
            'work_color' => '表示色',
        ];
    }
}
