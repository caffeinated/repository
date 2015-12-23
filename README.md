Caffeinated Repository
======================
[![Laravel 5.2](https://img.shields.io/badge/Laravel-5.2-orange.svg?style=flat-square)](http://laravel.com)
[![Source](http://img.shields.io/badge/source-caffeinated/repository-blue.svg?style=flat-square)](https://github.com/caffeinated/repository)
[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://tldrlegal.com/license/mit-license)

The Caffeinated Repository package allows the means to implement a standard boilerplate repository interface. This covers the standard Eloquent methods in a non-static, non-facade driven way right out of the box. Fear not though Batman! The Caffeinated Repository package does not limit you in any way when it comes to customizing (e.g overriding) the provided interface or adding your own methods.

The package follows the FIG standards PSR-1, PSR-2, and PSR-4 to ensure a high level of interoperability between shared PHP code.

Quick Installation
------------------
Begin by installing the package through Composer.

```
composer require caffeinated/repository=~1.0
```

And that's it! With your coffee in reach, start building out some awesome web applications!

Provides
--------
Out of the box, Caffeinated Repository provides the following repository implementations:

```php
// Common CRUD methods
public function delete($id);
public function find($id);
public function findBySlug($slug);
public function getAll($orderBy = array('id', 'asc'));
public function getAllPaginated($orderBy = array('id', 'asc'), $perPage = 25);
public function store($request);
public function update($id, $request);
public function with($relationships);

// Additional candy methods
public function dropdown($name, $value);
```

Implementation
--------------

### YourRepositoryInterface
Create your own repository interface per normal, but instead have it extend `Caffeinated\Repository\Contracts\RepositoryInterface`. If you don't need any custom repository methods, then you don't need to do anything else! Otherwise, feel free to add any additional methods you need here.

```php
<?php
namespace App\Repositories;

use Caffeinated\Repository\Contracts\RepositoryInterface;

interface YourRepositoryInterface extends RepositoryInterface
{
    // Add any additional methods here that you may need.
}
```

### YourRepository
Create your repository class that implements `YourRepositoryInterface` and extends `Caffeinated\Repository\Eloquent\AbstractRepository`.

In order for the `AbstractRepository` class to know what Model class it should use, simply set a protected variable `$namespace` with the full namespace to your Model class:

```php
<?php
namespace App\Repositories;

use Caffeinated\Repository\Eloquent\AbstractRepository;

class YourRepository extends AbstractRepository implements YourRepositoryInterface
{
    /**
     * @var string
     */
    protected $namespace = 'App\YourModel';
}
```

Finally, this is where you would implement any custom methods that you may have defined in `YourRepositoryInterface`. Otherwise, there's nothing else for you to do here!

Events
------
Caffeinated Repository will fire off events at key moments during the create, update, and delete processes if you desire to make use of them.

| Event | Description | Arguments |
|-------|-------------|-----------|
| `creating` | Fired prior to persisting a new entry in the database. | `$request` |
| `created`  | Fired after a new entry was persisted in the database. | `$created` (new entry) |
| `updating` | Fired prior to updating an existing entry in the database. | `$id`, `$request` |
| `updated`  | Fired after an existing entry was updated in the database. | `$id`, `$updated` (updated entry) |
| `deleting` | Fired prior to deleting an existing entry from the database. | `$id` |
| `deleted`  | Fired after an existing entry was deleted from the database. | `$id`, `$deleted` (deleted entry) |

### Usage
In order to make use of the provided events, you must declare which events you'd like to use as well as supply the namespace to your event handler. You can do this by defining a protected `$fireEvents` array within your repository implementation:

```php
/**
 * @var array
 */
protected $fireEvents = [
    'created' => 'YourCreatedEventHandler',
    'updated' => 'YourUpdatedEventHandler',
    'deleted' => 'YourDeletedEventHandler'
];
```

---

If you'd like to read more on repository abstraction, the package was heavily influenced by the article "[A Pattern for Reusable Repository Design in Laravel](http://slashnode.com/reusable-repository-design-in-laravel/)" by Corey McMahon.
