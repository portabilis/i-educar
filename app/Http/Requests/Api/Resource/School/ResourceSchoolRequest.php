<?php

namespace App\Http\Requests\Api\Resource\School;

use App\Http\Requests\Api\Resource\ResourceRequest;

class ResourceSchoolRequest extends ResourceRequest
{
    public function rules(): array
    {
        return [
            'institution' => ['required', 'integer', 'min:1'],
        ];
    }

    public function attributes()
    {
        return [
            'institution' => 'Instituição',
        ];
    }
}
