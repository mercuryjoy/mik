<?php

namespace App\Http\Controllers\API;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

use App\Http\Requests;

/**
 * @SWG\Swagger(basePath="/api",
 *      @SWG\Info(title="Mik API", description="米客之家API", version="0.1")
 * )
 */
class APIController extends BaseController
{
    use ValidatesRequests;

    protected function buildFailedValidationResponse(Request $request, array $errors) {
        $errors = array_reduce($errors, function($carry, $item) {
            return array_merge($carry, $item);
        }, []);

        return new JsonResponse($this->buildErrorResponse($errors), 400);
    }

    protected function buildErrorResponse($strings) {
        if (is_string($strings))
            $strings = [$strings];
        $errors = array_map(function($item) {
            $m = explode('|', $item, 2);
            if (count($m) == 2) {
                return ["code" => $m[0], 'message' => $m[1], 'messages' => $m[1]];
            } else {
                return ['message' => $item, 'messages' => $item];
            }
        }, $strings);
        return ['errors' => $errors];
    }
}
