<?php

namespace App\Traits;

use Illuminate\Http\Resources\Json\JsonResource;

trait RestfulResponse
{
    /**
     * Return '201 Created' response
     *
     * @param null|string|array $data
     * @param array $headers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function created($data = null, array $headers = [])
    {
        return response()->json($data, 201, $headers);
    }

    /**
     * Return '204 No Content' response
     *
     * @param array $headers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function noContent(array $headers = [])
    {
        return response()->json(null, 204, $headers);
    }

    /**
     * Return '200 OK' response
     *
     * @param mixed $data
     * @param array $headers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function ok($data = null, array $headers = [])
    {
        if ($data instanceof JsonResource) {
            return $data->response()->withHeaders($headers)->setStatusCode(200);
        } else {
            return response()->json($data, 200, $headers);
        }
    }

    /**
     * Return '400 Bad Request' response
     *
     * @param string $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error(string $message = '')
    {
        return response()->json(compact('message'), 400);
    }
}
