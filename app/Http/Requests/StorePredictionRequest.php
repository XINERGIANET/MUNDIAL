<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePredictionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'participant_id'       => ['required', 'integer'],
            'predicted_home_score' => ['required', 'integer', 'min:0', 'max:30'],
            'predicted_away_score' => ['required', 'integer', 'min:0', 'max:30'],
        ];
    }
}
