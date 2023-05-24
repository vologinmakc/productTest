<?php

namespace App\Services\Product;

use Illuminate\Database\Eloquent\Builder;

class SearchPopularityProductService
{
    private array $exceptIds;
    private Builder $query;
    private int $limitRow;

    /**
     * поиск самых популярных товаров используя возможность исключить определенные
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
        return $this->query->whereNotIn('id', $this->exceptIds)
            ->whereNotNull('popularity')
            ->orderBy('popularity', 'desc')
            ->limit($this->limitRow)
            ->get();
    }
}
