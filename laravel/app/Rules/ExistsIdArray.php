<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class ExistsIdArray implements Rule
{
    private $request;
    private $class;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Request $request, string $class)
    {
        $this->request = $request;
        $this->class = $class;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!is_array($value)) {
            return false;
        }
        return $this->class::whereIn('id', $value)->whereGroupId($this->request->session()->get('group_id'))->count() === count($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '有効でないIDが含まれます';
    }
}
