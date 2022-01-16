<?php

return [
    'macro_filter_method_name' => 'filter',
    'macro_paginate_method_name' => 'paginateFilter',
    'sort_field_name' => 'sort',
    'sort_delimiter_field' => ',',
    'sort_delimiter_direction' => '-',
    'sort_function_prefix' => 'sort_',

    'sort_strategy' => \Stepovenko\FilterableAndSortable\Strategies\SortStrategy::class,
    'filter_strategy' => \Stepovenko\FilterableAndSortable\Strategies\FilterStrategy::class
];
