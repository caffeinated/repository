<?php

namespace Caffeinated\Repository\Repositories;

use Caffeinated\Repository\Contracts\Repository as RepositoryInterface;

abstract class Repository implements RepositoryInterface
{
    /**
     * The repository model class.
     *
     * @var object
     */
    public $model;

    /**
     * Create a new Repository instance.
     *
     * @param  App  $app
     */
    public function __construct()
    {
        $this->model = app()->make($this->model);
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

    /**
     * Generate an unique key for caching results.
     *
     * @param  array  $data
     * @return string
     */
    protected function generateKey(array $data = [])
    {
        $data[] = $this->model->toSql();

        return md5(json_encode($data));
    }

    /**
     * Execute the provided callback and cache the results.
     *
     * @param  string  $class
     * @param  string  $method
     * @param  string  $key
     * @param  Closure  $closure
     * @return mixed
     */
    protected function cacheResults($class, $method, $key, $closure)
    {
        $key = $class.'@'.$method.'.'.$key;

        if (method_exists(app()->make('cache')->getStore(), 'tags')) {
            return app()->make('cache')->tags($this->tag)->remember($key, 60, $closure);
        }

        return call_user_func($closure);
    }

    /**
     * Flush the repository cache results.
     *
     * @return $this
     */
    public function flushCache()
    {
        if (method_exists(app()->make('cache')->getStore(), 'tags')) {
            app()->make('cache')->tags($this->tag)->flush();

            event(implode('-', $this->tag).'.entity.cache.flushed', [$this]);
        }

        return $this;
    }
}
