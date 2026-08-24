<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'business_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'area' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'service_interest' => ['required', 'in:AI Automation,AI Chatbot,Website Development,CRM Development,App Development,SEO,Other'],
            'source' => ['required', 'in:Website,Facebook,Instagram,LinkedIn,Google,Manual,Import,Other'],
            'status' => ['required', 'in:new,contacted,interested,follow_up,converted,not_interested,do_not_contact'],
            'priority' => ['required', 'in:low,medium,high'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'business_name.required' => 'Business name is required.',
            'contact_name.required' => 'Contact name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please provide a valid email address.',
            'country.required' => 'Country is required.',
            'category.required' => 'Category is required.',
            'service_interest.required' => 'Please select a service interest.',
            'source.required' => 'Please select a source.',
            'status.required' => 'Status is required.',
            'priority.required' => 'Priority is required.',
        ];
    }
}
