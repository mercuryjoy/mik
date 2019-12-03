<?php

namespace App\Http\Controllers\Net;

use App\Distributor;
use DB;

class DistributorsController extends NetController
{
    public function index()
    {
        // $distributors = Distributor::get();
        $distributors = DB::table('distributors')->get();
        return $distributors;
    }
}
