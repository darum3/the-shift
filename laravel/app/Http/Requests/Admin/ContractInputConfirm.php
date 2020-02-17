<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ContractInputConfirm extends FormRequest
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
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'user_name' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '契約名の指定は必須です',
            'name.max' => '契約名は50文字以内です',
        ];
    }

    public function attributes()
    {
        return [
            'email' => 'メールアドレス',
            'user_name' => '管理ユーザ名',
        ];
    }
}
