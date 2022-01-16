<?php

namespace Stepovenko\FilterableAndSortable\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;
use Stepovenko\FilterableAndSortable\Console\Commands\FilterCommand;
use Stepovenko\FilterableAndSortable\Contracts\FilterStrategyContract;
use Stepovenko\FilterableAndSortable\Contracts\SortStrategyContract;
use Stepovenko\FilterableAndSortable\Filters\QueryFilter;
use Stepovenko\FilterableAndSortable\Strategies\FilterStrategy;
use Stepovenko\FilterableAndSortable\Strategies\SortStrategy;

class FilterableAndSortableProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/filterable-and-sortable.php', 'filterable-and-sortable'
        );
    }

    /**
     * Bootstrap services.
     *
     * @RETURN VOID
     */
    public function boot()
    {
        $this->app->bind(FilterStrategyContract::class, function($app) {
            return new(config('filterable-and-sortable.filter_strategy'));
        });

        $this->app->bind(SortStrategyContract::class, function($app) {
            return new(config('filterable-and-sortable.sort_strategy'));
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                FilterCommand::class,
            ]);
        }

        $this->initMacros();

        $this->publishes([
            __DIR__ . '/../../config/filterable-and-sortable.php' => config_path('filterable-and-sortable.php'),
        ], 'filterable-and-sortable-config');
    }

    /**
     * @return void
     */
    public function initMacros(): void
    {
        Builder::macro(config('filterable-and-sortable.macro_filter_method_name'), function (QueryFilter $filter) {
            $this->queryFilterObject = $filter;

            return $filter->apply($this);
        });

        Builder::macro(config('filterable-and-sortable.macro_paginate_method_name'), function (QueryFilter $filter = null) {
            if (isset($this->queryFilterObject) && $this->queryFilterObject) {
                $paginator = $this->paginate($this->queryFilterObject->getPerPage());

                return $paginator->appends($this->queryFilterObject->getAppendableFields());
            } elseif ($filter) {
                $paginator = $filter->apply($this)->paginate($filter->getPerPage());

                return $paginator->appends($filter->getAppendableFields());
            }

            return $this->paginate();
        });
    }
}
