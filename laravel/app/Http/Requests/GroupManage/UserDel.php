<?php

namespace App\Http\Requests\GroupManage;

use Illuminate\Foundation\Http\FormRequest;

class UserDel extends FormRequest
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
            'version' => 'required_without_all:back|numeric|min:1|nullable',
        ];
    }
}
