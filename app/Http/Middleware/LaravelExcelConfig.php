<?php

namespace App\Http\Middleware;

use PHPExcel_Cell;
use PHPExcel_Cell_AdvancedValueBinder;

class LaravelExcelConfig
{
    public function handle($request, \Closure $next)
    {
        PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());

        return $next($request);
    }
}