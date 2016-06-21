<?php

namespace Caffeinated\Repositories\Repositories;

use Caffeinated\Repositories\Contracts\RepositoryInterface;
use Illuminate\Contracts\Container\Container as App;

abstract class Repository implements RepositoryInterface
{
    /**
     * Laravel app container instance.
     *
     * @var App
     */
    protected $app;

    /**
     * The repository model class.
     *
     * @var object
     */
    protected $model;

    /**
     * Create a new Repository instance.
     *
     * @param  App  $app
     */
    public function __construct(App $app)
    {
        $this->app   = $app;
        $this->model = $this->app->make($this->model);
    }

    /**
     * Set the relationships that should be eager loaded.
     *
     * @param  mixed  $relationships
     */
    public function with($relationships)
    {
        $this->model = $this->model->with($relationships);

        return $this;
    }

    /**
     * Add an "order by" clause to the repository instance.
     *
     * @param  string  $column
     * @param  string  $direction
     */
    public function orderBy($column, $direction = 'asc')
    {
        $this->model = $this->model->orderBy($column, $direction);

        return $this;
    }

    /**
     * Magic callStatic method to forward static methods to the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     */
    public static function __callStatic($method, $parameters)
    {
        return call_user_func_array([new static(), $method], $parameters);
    }

    /**
     * Magic call method to forward methods to the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     */
    public function __call($method, $parameters)
    {
        $model = $this->model;

        return call_user_func_array([$model, $method], $parameters);
    }
}
