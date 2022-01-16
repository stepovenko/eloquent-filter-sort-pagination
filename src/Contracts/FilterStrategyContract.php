<?php

declare(strict_types=1);


namespace Stepovenko\FilterableAndSortable\Contracts;


use Stepovenko\FilterableAndSortable\Filters\QueryFilter;

interface FilterStrategyContract
{
    public function handle(QueryFilter $queryFilters);
}
