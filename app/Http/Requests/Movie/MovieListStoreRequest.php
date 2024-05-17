<?php

declare(strict_types=1);

namespace App\Http\Requests\Movie;

use App\Http\Payloads\WatchListPayload;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MovieListStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'imdb_id' => ['string', 'nullable'],
            'yts_id' => ['integer', 'nullable'],
            'image' => ['string', 'nullable'],
            'name' => ['string', 'required'],
            'genres' => ['array', 'nullable'],
            'my_rating' => ['integer', 'nullable', 'between:0,100'],
            'released_date' => ['string', 'nullable'],
            'downloaded_status' => ['date', 'nullable'],
            'watched_status' => ['date', 'nullable'],
            'description' => ['string', 'nullable'],
        ];
    }

    public function payload(): WatchListPayload
    {
        return new WatchListPayload(
            name: $this->validated('name'),
            imdb_id: $this->validated('imdb_id'),
            yts_id: (int) $this->validated('yts_id'),
            image: $this->validated('image'),
            genres: $this->validated('genres'),
            my_rating: (int) $this->validated('my_rating'),
            released_date: $this->validated('released_date'),
            downloaded_status: $this->validated('downloaded_status'),
            watched_status: $this->validated('watched_status'),
            description: $this->validated('description'),
        );
    }
}
