<?php

namespace APIServices\Facebook\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class FacebookGetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $authorization = env('FACEBOOK_APP_SECRET');
        $params = $this->all();

        return array_key_exists('hub_verify_token', $params) && $params['hub_verify_token'] == $authorization;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'hub_mode' => 'required',
            'hub_challenge' => 'required',
            'hub_verify_token' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'hub_mode.required' => 'mode is required.',
            'hub_challenge.required' => 'challenge is required.',
            'hub_verify_token.required' => 'token is required.'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new UnprocessableEntityHttpException($validator->errors()->toJson());
    }

    protected function failedAuthorization()
    {
        Log::alert($this->getClientIp().': UnAuthorized Access.');
        throw new UnauthorizedHttpException("Your access will be deny and log");
    }
}