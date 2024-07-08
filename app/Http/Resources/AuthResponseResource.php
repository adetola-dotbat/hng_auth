<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResponseResource extends JsonResource
{
    protected $statusCode;

    public function __construct($resource, $statusCode = 200)
    {
        $this->statusCode = $statusCode;
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'status' => 'success',
            'message' => $this->resource->message,
            'data' => [
                'accessToken' => $this->resource->accessToken,
                'user' => new UserResource($this->resource->user),
            ],
        ];
    }

    public function withResponse($request, $response)
    {
        $response->setStatusCode($this->statusCode);
    }
}
