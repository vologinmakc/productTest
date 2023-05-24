<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Product\SearchProductService;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        // Разобьем на слова наш искомый товар
        $searchProductService = new SearchProductService($product);
        $resultOptionalProducts = $searchProductService->search();

        return response()->json([
            'result_code' => 'FULL_ACCESS',
            'data'        => [
                'product'    => $product,
                'popularity' => $resultOptionalProducts
            ]
        ]);
    }
}
