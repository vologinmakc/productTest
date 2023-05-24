<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;

class ProductSearchControllerTest extends \Tests\TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Artisan::call('db:seed');
    }

    public function testSearchProduct()
    {
        /*Пользователь на сайте выбрал продукт Красная Рубашка, по задаче помимо красной рубашки подберем товар по алгоритму*/
        $response = $this->getJson('api/product/1')->assertJson([
            'result_code' => 'FULL_ACCESS'
        ])->getContent();


        $resultResponse = json_decode($response, true);
        $this->assertEquals(15, count($resultResponse['data']['popularity']));
    }
}
