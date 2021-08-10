<?php

namespace App\Http\Requests\Client;

use App\Http\Requests\ApiFormRequest;
use Laravel\Passport\Http\Rules\RedirectRule;

class RegisterRequest extends ApiFormRequest
{
    protected $redirectRule;

    public function __construct(RedirectRule $redirectRule)
    {
        $this->redirectRule = $redirectRule;
    }

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
            'name' => 'required|max:191',
            'redirect' => ['required', $this->redirectRule],
            'confidential' => 'boolean',
        ];
    }
}
