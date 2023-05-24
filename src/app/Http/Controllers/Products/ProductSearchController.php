<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;

class ProductSearchController extends Controller
{
    public function index()
    {
        return response()->json([
            'result_code' => 'FULL_ACCESS'
        ]);
    }
}
