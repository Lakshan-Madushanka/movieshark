<?php

declare(strict_types=1);

namespace App\Http\Requests\WatchList;

use App\Http\Payloads\WatchListPayload;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WatchListStoreRequest extends FormRequest
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
            'name' => ['string', 'required', Rule::unique('watch_list')->ignore($this->id)],
            'genres' => ['array', 'nullable'],
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
            yts_id: $this->validated('yts_id'),
            image: $this->validated('image'),
            genres: $this->validated('genres'),
            released_date: $this->validated('released_date'),
            downloaded_status: $this->validated('downloaded_status'),
            watched_status: $this->validated('watched_status'),
            description: $this->validated('description'),
        );
    }
}
