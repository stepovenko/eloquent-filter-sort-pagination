<?php

declare(strict_types=1);

namespace Stepovenko\FilterableAndSortable\Strategies;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Stepovenko\FilterableAndSortable\Contracts\FilterStrategyContract;
use Stepovenko\FilterableAndSortable\Filters\QueryFilter;

class FilterStrategy implements FilterStrategyContract
{
    /**
     * @var \Stepovenko\FilterableAndSortable\Filters\QueryFilter
     */
    private QueryFilter $queryFilters;

    /**
     * @var \Illuminate\Http\Request
     */
    private Request $request;

    /**
     * @var \Illuminate\Database\Eloquent\Builder
     */
    private Builder $builder;

    /**
     * @param \Stepovenko\FilterableAndSortable\Filters\QueryFilter $queryFilters
     *
     * @return void
     */
    public function handle(QueryFilter $queryFilters)
    {
        $this->init($queryFilters);
        $filterableFields = $this->getFilterableFieldsFromRequest();
        foreach ($filterableFields as $name => $value) {
            if (!method_exists($this->queryFilters, $name)) {
                $this->defaultFilter($name, $value);
                return;
            }
            if (strlen($value)) {
                $this->queryFilters->$name($value);
            } else {
                $this->queryFilters->$name();
            }
        }
    }

    /**
     * @param \Stepovenko\FilterableAndSortable\Filters\QueryFilter $queryFilters
     *
     * @return void
     */
    private function init(QueryFilter $queryFilters)
    {
        $this->queryFilters = $queryFilters;
        $this->request = $this->queryFilters->getRequest();
        $this->builder = $this->queryFilters->getBuilder();
    }

    /**
     * @param $fieldName
     * @param $term
     *
     * @return void
     */
    protected function defaultFilter($fieldName, $term)
    {
        $this->builder->where($fieldName, $term);
    }

    /**
     * @return array
     */
    protected function getFilterableFieldsFromRequest(): array
    {
        return $this->request->only($this->queryFilters->getFilterableFields());
    }
}
