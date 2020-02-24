<?php

namespace App\Http\Requests\GroupManage;

use Illuminate\Foundation\Http\FormRequest;

class UserAdd extends FormRequest
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
            'user_name' => 'required|string|max:255',
            'password' => 'required|password|min:6',
            'email' => 'required|email|max:255',
        ];
    }

    public function attributes()
    {
        return [
            'user_name' => '名前',
            'email' => 'メールアドレス',
            'password' => 'パスワード'
        ];
    }
}
