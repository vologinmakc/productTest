<?php

namespace App\Services\Product;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Требования к поиску:
 * ищем похожие по названию сортируя по популярности
 */
class SimilarProductSearchService
{

    private Builder $query;
    private array $productNameArray;

    /**
     * @param Builder $query
     * @param array   $productNameArray
     */
    public function __construct(Builder $query, array $productNameArray)
    {
        $this->query = $query;
        $this->productNameArray = $productNameArray;
    }

    public function search(): Collection
    {
        $this->addConditionName();
        $this->addPopularityCondition();

        return $this->query->get();
    }



    private function addConditionName()
    {
        return $this->query->where(function ($query) {
            foreach ($this->productNameArray as $name) {
                $query->orWhere('name', 'ilike', "%{$name}%");
            }
        });
    }

    private function addPopularityCondition()
    {
        return $this->query->orderBy('popularity', 'desc')
            ->whereNotNull('popularity')
            ->get();
    }
}
