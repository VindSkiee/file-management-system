<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFileRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            // whereNull('deleted_at') rejects soft-deleted references.
            'department_id' => ['required', 'integer', Rule::exists('departments', 'id')->whereNull('deleted_at')],
            'folder_id' => ['nullable', 'integer', Rule::exists('folders', 'id')->whereNull('deleted_at')],
            // Physical file replacement is optional while editing metadata.
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,jpg,jpeg,png,gif,webp', 'max:10240'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Title wajib diisi.',
            'title.string' => 'Title harus berupa teks.',
            'title.max' => 'Title maksimal 255 karakter.',
            'department_id.required' => 'Department wajib dipilih.',
            'department_id.exists' => 'Department yang dipilih tidak valid.',
            'folder_id.exists' => 'Folder yang dipilih tidak valid.',
            'file.file' => 'Input file tidak valid.',
            'file.mimes' => 'Format file harus berupa dokumen atau gambar (pdf, doc, xls, ppt, txt, csv, jpg, png, dll).',
            'file.max' => 'Ukuran file maksimal 10MB.',
        ];
    }
}
