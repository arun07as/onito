<?php

namespace App\Http\Requests\API\V1;

use App\Entities\MovieData;
use Illuminate\Foundation\Http\FormRequest;

class SaveMovieRequest extends FormRequest
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
            'titleType' => ['bail', 'required', 'string', 'max:10'],
            'primaryTitle' => ['bail', 'required', 'string', 'max:150'],
            'runtimeMinutes' => ['bail', 'required', 'integer', 'min:0', 'max:65535'],
            'genres' => ['bail', 'required', 'string', 'max:50'],
        ];
    }

    public function toEntity()
    {
        return new MovieData(
            null,
            null,
            $this->titleType,
            $this->primaryTitle,
            (int)$this->runtimeMinutes,
            $this->genres
        );
    }
}
