<?php

return [
    /*
     * Method name
     * Product::filter($productFilter);
     */
    'macro_filter_method_name' => 'filter',

    /*
     * Method name
     * Product::paginationFilter($productFilter);
     */
    'macro_paginate_method_name' => 'paginationFilter',

    /*
     * Prefix to custom sort method
     *
     * public function sort_categories_name($term){};
     */
    'sort_function_prefix' => 'sort_',

    /*
     * Query param name
     * ?sort=price-desc&name=iphone
     */
    'sort_field_name' => 'sort',

    /*
     * Separator between sorted options
     * ?sort=price-desc,old_price=asc&name=iphone
     */
    'sort_delimiter_field' => ',',

    /*
     * Separator between sorted key => value
     * ?sort=price-desc,old_price=asc&name=iphone
     */
    'sort_delimiter_direction' => '-',

    /*
     * Sort strategy class
     */
    'sort_strategy' => \Stepovenko\FilterableAndSortable\Strategies\SortStrategy::class,

    /*
     * Filter strategy class
     */
    'filter_strategy' => \Stepovenko\FilterableAndSortable\Strategies\FilterStrategy::class
];
