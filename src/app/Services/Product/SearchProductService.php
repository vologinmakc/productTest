<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Services\Helper\String\LinguaStemRu;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * В данном сервисе будем искать похожие товары с не нулевой популярностью
 */
class SearchProductService
{
    const COUNT_TOTAL_RESULT = 15;

    private array $productNameArray;
    private Builder $query;
    private Product $product;

    private int $maxCountResult;
    private int $currentCountResult = 0;
    private array $foundIds = [];

    /**
     * Основной продукт на основе которого будем искать похожие товары
     * @param Product $product
     *
     * Максимальное количество товара к показу
     * @param int     $maxCountResult
     */
    public function __construct(Product $product, int $maxCountResult = self::COUNT_TOTAL_RESULT)
    {
        $this->maxCountResult = $maxCountResult;
        $this->product = $product;
        $this->initService($product->name);
    }

    public function search()
    {
        // Найдем все похожие товары по имени
        $resultTotalProducts = $this->getSimilarProducts();

        if ($this->currentCountResult < $this->maxCountResult) {
            // Если мы не нашли достаточно похожих найдем популярные товары исключая уже найденные
            $popularProducts = $this->getPopularProducts();
            $resultTotalProducts = array_merge($resultTotalProducts, $popularProducts);

            if ($this->currentCountResult < $this->maxCountResult) {
                $otherRandomProducts = $this->searchOtherRandom();
                $resultTotalProducts = array_merge($resultTotalProducts, $otherRandomProducts);
            }
        }


        return $resultTotalProducts;
    }

    private function addResultCount(int $count)
    {
        $this->currentCountResult += $count;

        return;
    }

    private function getFoundIds()
    {
        return $this->foundIds;
    }

    private function getSimilarProducts(): array
    {
        // Проверим, может на данный запрос уже есть кеш то вернем его
        $keyCache = json_encode($this->productNameArray) . $this->maxCountResult;
        $result = Cache::get($keyCache);

        if (!$result) {
            $service = new SimilarProductSearchService($this->getQuery(), $this->productNameArray);
            $result = $service->search();

            $this->addResultCount($result->count());
            $this->addFoundIds($result);

            $result = $result->toArray();
            Cache::put($keyCache, $result, 3600);
        }


        return $result;
    }

    private function getPopularProducts(): array
    {
        $service = new SearchPopularityProductService($this->getQuery(), $this->getFoundIds(), $this->getLimitRow());
        $result = $service->search();

        $this->addResultCount($result->count());
        $this->foundIds += $result->pluck('id')->toArray();

        return $result->toArray();
    }

    private function initService(string $name)
    {
        // Разобьем название на составные части будем искать по ним
        $arrProductNames = explode(' ', $name);
        $stemmer = new LinguaStemRu();
        foreach ($arrProductNames as $name) {
            $this->productNameArray[] = $stemmer->stem_word($name);
        }

        // Исключим товар на основе которого делаем выборку
        $this->query = Product::query();
        $this->query->where('id', '<>', $this->product->id);
    }

    private function getLimitRow()
    {
        return $this->maxCountResult - $this->currentCountResult;
    }

    private function searchOtherRandom(): array
    {
        $query = $this->getQuery();
        return $query->inRandomOrder()
            ->whereNotIn('id', $this->getFoundIds())
            ->whereNull('popularity')
            ->limit($this->getLimitRow())
            ->get()->toArray();
    }

    private function addFoundIds(Collection $result)
    {
        $this->foundIds += $result->pluck('id')->toArray();

        return;
    }

    private function getQuery()
    {
        return clone $this->query;
    }
}
