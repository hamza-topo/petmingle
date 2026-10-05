<?php

namespace App\Http\Requests\Api\Pet;

final class PetImageRules
{
    public const MAX_KILOBYTES = 10240;

    public static function optional(): array
    {
        return [
            'nullable',
            'file',
            'image',
            'mimetypes:image/jpeg,image/png',
            'mimes:jpg,jpeg,png',
            'extensions:jpg,jpeg,png',
            'max:' . self::MAX_KILOBYTES,
        ];
    }

    public static function messages(): array
    {
        return [
            'image.file' => __('The pet image must be a file.'),
            'image.image' => __('The pet image must be a valid image.'),
            'image.mimetypes' => __('The pet image must be a JPG or PNG file.'),
            'image.mimes' => __('The pet image must be a JPG or PNG file.'),
            'image.extensions' => __('The pet image must use a JPG, JPEG, or PNG extension.'),
            'image.max' => __('The pet image may not be larger than 10MB.'),
        ];
    }
}
