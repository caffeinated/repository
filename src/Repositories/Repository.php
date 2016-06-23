<?php

namespace Caffeinated\Repository\Repositories;

use Log;
use Caffeinated\Repository\Contracts\Repository as RepositoryInterface;
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
        $tag = $class;

        if (method_exists($this->app->make('cache')->getStore(), 'tags')) {
            Log::info('Caching database queries!', [$class, $method]);
            return $this->app->make('cache')->tags($tag)->remember($key, 60, $closure);
        }

        Log::warning('Current cache driver does not support tagging. Not able to cache database queries.');

        return call_user_func($closure);
    }

    /**
     * Flush the repository cache results.
     *
     * @return $this
     */
    public function flushCache()
    {
        if (method_exists($this->app->make('cache')->getStore(), 'tags')) {
            Log::info('Flushing repository cache', [get_called_class()]);

            $this->app->make('cache')->tags(get_called_class())->flush();
        }

        Log::warning('Current cache driver does not support tagging. Not able to flush cache.');

        return $this;
    }
}
