<?php

namespace App\Http\Requests\admin\Employee;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
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
            'email' => ['required', 'string', Rule::unique('employees', 'email')->ignore($this->route('employee'))],
            'phone' => ['required','string', 'max:20'],
            'joining_date' => ['required','date'],
            'department_id' => ['required', 'exists:departments,id'],
            'profile_photo' => ['nullable','image', 'max:2048'],
        ];
    }
}
