<?php

namespace App\Services\Product;

use Illuminate\Database\Eloquent\Builder;

class SearchPopularityProductService
{
    const HIGH_POPULATION_INTERVAL_MIN   = 7;
    const HIGH_POPULATION_INTERVAL_MAX   = 10;

    const MIDDLE_POPULATION_INTERVAL_MIN = 3;
    const MIDDLE_POPULATION_INTERVAL_MAX = 6;

    const LOW_POPULATION_INTERVAL_MIN    = 1;
    const LOW_POPULATION_INTERVAL_MAX    = 2;

    const COUNT_POPULATION_CATEGORIES = 3;

    private array $exceptIds;
    private Builder $query;
    private int $limitRow;

    /**
     * поиск самых популярных товаров используя возможность исключить определенные
     *
     * @param array $exceptIds
     */
    public function __construct(Builder $query, array $exceptIds = [], $limitRow = 1)
    {
        $this->exceptIds = $exceptIds;
        $this->query = $query;
        $this->limitRow = $limitRow;
    }

    public function search()
    {
        $this->basicQuery();
        $this->searchHighPopulationProducts();
        $this->searchMiddlePopulationProducts();
        $this->searchLowPopulationProducts();

        return $this->getResult();
    }

    private function searchHighPopulationProducts()
    {
        $this->query->whereRaw('id in (select products.id from "products" where "popularity" between '
            . self::HIGH_POPULATION_INTERVAL_MIN . ' and ' . self::HIGH_POPULATION_INTERVAL_MAX . ' order by RANDOM() limit '
            . $this->getLimitHighCategories() . ')');
    }

    private function basicQuery()
    {
        $this->query->whereNotIn('id', $this->exceptIds);
    }

    private function getLimitHighCategories()
    {
        $limit = ($this->limitRow % self::COUNT_POPULATION_CATEGORIES) ? $this->limitRow / self::COUNT_POPULATION_CATEGORIES + 1
            : $this->limitRow / self::COUNT_POPULATION_CATEGORIES;

        return round($limit);
    }

    private function searchMiddlePopulationProducts()
    {
        $this->query->orWhereRaw('id in (select products.id from "products" where "popularity" between '
            . self::MIDDLE_POPULATION_INTERVAL_MIN . ' and ' . self::MIDDLE_POPULATION_INTERVAL_MAX . ' order by RANDOM() limit '
            . $this->getLimitOtherCategories() . ')');
    }

    private function getLimitOtherCategories()
    {
        return round($this->limitRow / self::COUNT_POPULATION_CATEGORIES);
    }

    private function searchLowPopulationProducts()
    {
        $this->query->orWhereRaw('id in (select products.id from "products" where "popularity" between '
            . self::LOW_POPULATION_INTERVAL_MIN . ' and ' . self::LOW_POPULATION_INTERVAL_MAX . ' order by RANDOM() limit '
            . $this->getLimitOtherCategories() . ')');
    }

    private function getResult()
    {
        return $this->query->orderBy('popularity', 'desc')->get();
    }
}
