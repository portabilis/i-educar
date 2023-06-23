<?php

namespace App\Http\Requests\Api\Resource\EducationNetwork;

use App\Http\Requests\Api\Resource\ResourceRequest;

class ResourceEducationNetworkRequest extends ResourceRequest
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
