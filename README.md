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
        return Product::paginationFilter($productFilter);
    }

}
```

Or you can use

```php
$products = Product::filter($productFilter)->paginationFilter();

$products = Product::filter($productFilter)->get();

$products = Product::filter($productFilter)->pagination();

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

Additional methods and variables

```php
<?php

namespace App\Filters;

use Stepovenko\FilterableAndSortable\Filters\QueryFilter;

class ProductFilter extends QueryFilters
{
    protected string $defaultSort = 'price-asc';
    protected int $maxPerPage = 100;
    protected int $defaultPerPage = 15;

    public function getSortableFields(): array
    {
        return ['category_name'];
    }

    // if you saved price as string
    public function getSortableFieldsLikeNumber(): array
    {
        return ['price'];
    }

    // override standard sort prefix sort_
    public function sort_categories_name($term)
    {
        $this->builder->where('category.name', 'LIKE', "%$term%");
    }
}
```

```php
<?php

namespace App\Http\Controllers;

use App\Filters\ProductFilter;
use App\Models\Product;

class ProductController extends Controller
{

    public function index(ProductFilter $productFilter)
    {
        // internal public methods
        $productFilter->setDefaultSort('price-desc');
        $perPega = $productFilter->getPerPage();
        $request = $productFilter->getRequest();
        $builder = $productFilter->getBuilder();
    }

}
```

### You can change the way you sort and filter over classes

config publication

```php artisan vendor:publish --tag=filterable-and-sortable-config```
