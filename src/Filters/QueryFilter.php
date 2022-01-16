<?php

namespace Stepovenko\FilterableAndSortable\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stepovenko\FilterableAndSortable\Contracts\FilterStrategyContract;
use Stepovenko\FilterableAndSortable\Contracts\SortStrategyContract;

abstract class QueryFilter
{
    /**
     * @var \Illuminate\Http\Request
     */
    private Request $request;

    /**
     * @var \Illuminate\Database\Eloquent\Builder|null
     */
    protected ?Builder $builder;

    /**
     * @var \Stepovenko\FilterableAndSortable\Contracts\FilterStrategyContract
     */
    private FilterStrategyContract $filterStrategy;

    /**
     * @var \Stepovenko\FilterableAndSortable\Contracts\SortStrategyContract
     */
    private SortStrategyContract $sortStrategy;

    /**
     * @var string
     */
    protected string $defaultSort = '';

    /**
     * @var bool
     */
    private bool $isSortFromRequest = true;

    /**
     * @var int
     */
    protected int $maxPerPage = 100;
    /**
     * @var int
     */
    protected int $defaultPerPage = 15;

    /**
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request, FilterStrategyContract $filterStrategy, SortStrategyContract $sortStrategy)
    {
        $this->request        = $request;
        $this->filterStrategy = $filterStrategy;
        $this->sortStrategy   = $sortStrategy;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder|null
     */
    public function apply(Builder $builder): ?Builder
    {
        $this->builder = $builder;

        $this->defaultSort();
        $this->sortStrategy->handle($this);
        $this->filterStrategy->handle($this);

        return $this->builder;
    }

    protected function defaultSort()
    {
        if (!$this->request->sort && $this->defaultSort) {
            $this->isSortFromRequest = false;
            $this->request->request->add([config('filterable-and-sortable.sort_field_name') => $this->defaultSort]);
        }
    }

    /**
     * @param int $maxPerPage
     */
    public function setMaxPerPage(int $maxPerPage): void
    {
        $this->maxPerPage = $maxPerPage;
    }

    /**
     * @param string|null $sort
     */
    public function setDefaultSort(string $sort = null)
    {
        if ($sort && !$this->isSortFromRequest) {
            $this->request->request->add([config('filterable-and-sortable.sort_field_name') => $sort]);
        }
    }

    /**
     * @return array
     */
    public function getAppendableFields(): array
    {
        return $this->request->only(
            array_merge($this->getFilterableFields(), [config('filterable-and-sortable.sort_field_name'), 'per_page'])
        );
    }

    /**
     * @return mixed
     */
    public function getPerPage(): mixed
    {
        $perPage = request('per_page', $this->defaultPerPage);

        return min($this->maxPerPage, (int)$perPage);
    }

    /**
     * @return \Illuminate\Http\Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    /**
     * @return array
     */
    public function getFilterableFields(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getSortableFieldsLikeNumber(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getSortableFields(): array
    {
        return [];
    }
}
