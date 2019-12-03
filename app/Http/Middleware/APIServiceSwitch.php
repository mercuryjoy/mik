<?php

namespace App\Http\Middleware;

use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Contracts\Routing\ResponseFactory;

class APIServiceSwitch
{
    /**
     * @var \Illuminate\Contracts\Routing\ResponseFactory
     */
    protected $response;

    public function __construct(ResponseFactory $response)
    {
        $this->response = $response;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $status = Settings::get('app.api_service.status', 'true');

        if ($status == 'off') {
            return $this->response->json(['errors' => [['message' => '服务正在维护', 'messages' => '服务正在维护']]], 500);
        }
        return $next($request);
    }
}
