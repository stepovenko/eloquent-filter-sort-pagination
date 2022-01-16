# Laravel eloquent Filter, Sort and Paginate

Simple filtering and sorting of data in Eloquent uses query parameters for example
`?sort=price-desc&name=iphone`

```php
<?php

namespace App\Http\Controllers;

use App\Filters\ProductFilter;
use App\Models\Product;

class ProductController extends Controller
{

    public function index(ProductFilter $productFilter)
    {
        return Product::filter($productFilter)->paginate();
    }

}
```

Console command to create a filter

```php arisan make:filter Product```

This is what ProductFilter will look like

```php
<?php

namespace App\Filters;

use Stepovenko\FilterableAndSortable\Filters\QueryFilter;

class ProductFilter extends QueryFilters
{
    public function getFilterableFields(): array
    {
        return ['name'];
    }

    public function getSortableFields(): array
    {
        return ['price'];
    }

    public function name($term)
    {
        $this->builder->where('tokens.name', 'LIKE', "%$term%");
    }
}
```

config publication

```php artisan vendor:publish --tag=filterable-and-sortable-config```
