<?php

declare(strict_types=1);

namespace Stepovenko\FilterableAndSortable\Strategies;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stepovenko\FilterableAndSortable\Contracts\FilterStrategyContract;
use Stepovenko\FilterableAndSortable\Contracts\SortStrategyContract;
use Stepovenko\FilterableAndSortable\Filters\QueryFilter;

class SortStrategy implements SortStrategyContract
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
        if (!$queryString = $this->getSortableFieldsFromRequest()) {
            return;
        }

        $fieldsList = explode(config('filterable-and-sortable.sort_delimiter_field'), $queryString);
        foreach ($fieldsList as $field) {
            $value = explode(config('filterable-and-sortable.sort_delimiter_direction'), $field);

            if (count($value) !== 2) {
                return;
            }
            $fieldName = Str::lower($value[0]);
            $sort      = Str::lower($value[1]);

            if (!in_array($fieldName, $this->getMergedSortableFields())) {
                return;
            }

            $direction = 'ASC';
            if ($sort === 'desc') {
                $direction = 'DESC';
            }

            $methodName = config('filterable-and-sortable.sort_function_prefix') . $fieldName;
            if (method_exists($this, $methodName)) {
                $this->$methodName($direction);
            } else {
                if (in_array($fieldName, $this->queryFilters->getSortableFieldsLikeNumber())) {
                    $this->builder->orderByRaw("case when `$fieldName` is null then 1 else 0 end, `$fieldName` * 1 $direction");
                } else {
                    $this->builder->orderByRaw("case when `$fieldName` is null then 1 else 0 end, `$fieldName` $direction");
                }
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
     * @return string|null
     */
    protected function getSortableFieldsFromRequest(): ?string
    {
        return $this->request->get(config('filterable-and-sortable.sort_field_name'));
    }

    /**
     * @return array
     */
    private function getMergedSortableFields(): array
    {
        return array_merge($this->queryFilters->getSortableFields(), $this->queryFilters->getSortableFieldsLikeNumber());
    }


}
