<?php

namespace App\Http\Requests\Admin\Employee;

use Illuminate\Foundation\Http\FormRequest;

class CreateEmployeeRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required','string', 'min:3', 'max:255'],
            'email' => ['required','string', 'email','max:255', 'unique:employees'],
            'phone' => ['required','string', 'max:20'],
            'joining_date' => ['required','date'],
            'department_id' => ['required', 'exists:departments,id'],
            'profile_photo' => ['required','image', 'max:2048'],
        ];
    }

    public function messages()
    {
        return [
            'department_id.required' => 'Department is required.',
            'department_id.exists' => 'Selected department is invalid.',
        ];
    }
}
