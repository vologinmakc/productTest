<?php

class ProductSearchControllerTest extends \Tests\TestCase
{
    public function testSearchProduct()
    {
        $this->postJson('api/product-search')->assertJson([
            'result_code' => 'FULL_ACCESS'
        ]);

        $this->assertEquals(1, 1);
    }
}
