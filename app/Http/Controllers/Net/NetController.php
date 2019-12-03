<?php

namespace App\Http\Controllers\Net;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class NetController extends BaseController
{
    use ValidatesRequests;

    protected function jsonReturn($code = '', $msg = '', $data = null)
    {
        return [
            'Code' => $code,
            'Msg'  => $msg,
            'Info' => $data
        ];
    }

    protected function buildFailedValidationResponse(Request $request, array $errors) {
        $errors = array_reduce($errors, function($carry, $item) {
            return $item;
        }, []);

        return new JsonResponse($this->buildErrorResponse($errors), 200);
    }

    protected function buildErrorResponse($strings) {
        if (is_string($strings)) {
            $strings = [$strings];
        }

        $errors = array_map(function($item) {
            $m = explode('|', $item, 2);

            if (count($m) === 2) {
                return ['Code' => (integer)$m[0],'Msg' => $m[1],'Info' => null];
            }

            return ['Code' => 403,'Msg' => $item,'Info' => null];
        }, $strings);

        return $errors[0];
    }
}
