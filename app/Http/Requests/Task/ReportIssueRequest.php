<?php

namespace App\Http\Requests\Task;

use Domain\Shared\Foundation\Requests\FormRequest;

class ReportIssueRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'issue_reports' => [
                'array'
            ],
            'issue_reports.*.title' => [
                'required',
                'max:255'
            ],
            'issue_reports.*.description' => [
                'nullable'
            ],
            'issue_reports.*.images' => [
                'nullable',
            ],
            'issue_reports.*.images.*' => [
                'image',
                'max:2048'
            ],
            'costs' => [
                'required',
                'array'
            ],
            'costs.*.description' => [
                'nullable'
            ],
            'costs.*.cost' => [
                'required',
                'numeric'
            ]
            // 'cost' => [
            //     'required',
            //     'numeric'
            // ],
        ];
    }
}
