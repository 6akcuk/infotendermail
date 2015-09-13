<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\User;

class UsersRequest extends Request
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
        $user = User::find($this->users);

        switch ($this->method())
        {
            case 'POST':
            {
                return [
                    'name' => 'required',
                    'email' => 'required|email|unique:users',
                    'password' => 'required'
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email,'. $user->id
                ];
            }
        }


    }
}
