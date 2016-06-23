<?php

namespace Caffeinated\Repository\Repositories;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class EloquentRepository extends Repository
{
    /**
     * Find an entity by its primary key.
     *
     * @param  integer  $id
     * @param  array  $columns
     * @param  array  $with
     */
    public function find($id, $columns = ['*'], $with = [])
    {
        $cacheKey = $this->generateKey([$id, $columns, $with]);

        return $this->cacheResults(get_called_class(), __FUNCTION__, $cacheKey, function() use ($id, $columns, $with) {
            return $this->model->with($with)
                ->find($id, $columns);
        });
    }

    /**
     * Find the entity by the given attribute.
     *
     * @param  string  $attribute
     * @param  string  $value
     * @param  array  $columns
     * @param  array  $with
     */
    public function findBy($attribute, $value, $columns = ['*'], $with = [])
    {
        $cacheKey = $this->generateKey([$attribute, $value, $columns, $with]);

        return $this->cacheResults(get_called_class(), __FUNCTION__, $cacheKey, function() use ($attribute, $value, $columns, $with) {
            return $this->model->with($with)
                ->where($attribute, '=', $value)
                ->first($columns);
        });
    }

    /**
     * Find all entities.
     *
     * @param  array  $columns
     * @param  array  $with
     */
    public function findAll($columns = ['*'], $with = [])
    {
        $cacheKey = $this->generateKey([$columns, $with]);

        return $this->cacheResults(get_called_class(), __FUNCTION__, $cacheKey, function() use ($columns, $with) {
            return $this->model->with($with)
                ->get($columns);
        });
    }

    /**
     * Find all entities matching where conditions.
     *
     * @param  mixed  $where
     * @param  array  $columns
     * @param  array  $with
     */
    public function findWhere($where, $columns = ['*'], $with = [])
    {
        $where    = $this->castRequest($where);
        $cacheKey = $this->generateKey([$where, $columns, $with]);

        return $this->cacheResults(get_called_class(), __FUNCTION__, $cacheKey, function() use ($where, $columns, $with) {
            foreach ($where as $attribute => $value) {
                if (is_array($value)) {
                    list($attribute, $condition, $value) = $value;
                    $this->model->where($attribute, $condition, $value);
                } else {
                    $this->model->where($attribute, '=', $value);
                }
            }

            return $this->model->with($with)
                ->get($columns);
        });
    }

    /**
     * Find all entities matching whereIn conditions.
     *
     * @param  string  $attribute
     * @param  array  $values
     * @param  array  $columns
     * @param  array  $with
     */
    public function findWhereIn($attribute, $values, $columns = ['*'], $with = [])
    {
        $values   = $this->castRequest($values);
        $cacheKey = $this->generateKey([$attribute, $values, $columns, $with]);

        return $this->cacheResults(get_called_class(), __FUNCTION__, $cacheKey, function() use ($attribute, $values, $columns, $with) {
            return $this->model->with($with)
                ->whereIn($attribute, $values)
                ->get($columns);
        });
    }

    /**
     * Find all entities matching whereNotIn conditions.
     *
     * @param  string  $attribute
     * @param  array  $values
     * @param  array  $columns
     * @param  array  $with
     */
    public function findWhereNotIn($attribute, $values, $columns = ['*'], $with = [])
    {
        $values   = $this->castRequest($values);
        $cacheKey = $this->generateKey([$attribute, $values, $columns, $with]);

        return $this->cacheResults(get_called_class(), __FUNCTION__, $cacheKey, function() use ($attribute, $values, $columns, $with) {
            return $this->model->with($with)
                ->whereNotIn($attribute, $values)
                ->get($columns);
        });
    }

    /**
     * Find an entity matching the given attributes or create it.
     *
     * @param  array  $attributes
     */
    public function findOrCreate($attributes)
    {
        $attributes = $this->castRequest($attributes);

        if (! is_null($instance = $this->findWhere($attributes)->first())) {
            return $instance;
        }

        return $this->create($attributes);
    }

    /**
     * Get an array with the values of the given column from entities.
     *
     * @param  string  $column
     * @param  string|null  $key
     */
    public function pluck($column, $key = null)
    {
        $cacheKey = $this->generateKey([$column, $key]);

        return $this->cacheResults(get_called_class(), __FUNCTION__, $cacheKey, function() use ($column, $key) {
            return $this->model->pluck($column, $key);
        });
    }

    /**
     * Paginate the given query for retrieving entities.
     *
     * @param  int|null  $perPage
     * @param  array  $columns
     * @param  string  $pageName
     * @param  int|null  $page
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $cacheKey = $this->generateKey([$perPage, $columns, $pageName, $page]);

        return $this->cacheResults(get_called_class(), __FUNCTION__, $cacheKey, function() use ($perPage, $columns, $pageName, $page) {
            return $this->model->paginate($perPage, $columns, $pageName, $page);
        });
    }

    /**
     * Create a new entity with the given attributes.
     *
     * @param  array  $attributes
     */
    public function create($attributes)
    {
        $attributes = $this->castRequest($attributes);
        $instance   = $this->model->newInstance($attributes);
        $created    = $instance->save();

        event(get_called_class().'.entity.created', [$this, $instance]);

        return [
            $created,
            $instance
        ];
    }

    /**
     * Update an entity with the given attributes.
     *
     * @param  mixed  $id
     * @param  array  $attributes
     */
    public function update($id, $attributes)
    {
        $attributes = $this->castRequest($attributes);
        $updated    = false;
        $instance   = $id instanceof Model ? $id : $this->find($id);

        if ($instance) {
            $updated = $instance->update($attributes);

            event(get_called_class().'.entity.updated', [$this, $instance]);
        }

        return [
            $updated,
            $instance
        ];
    }

    /**
     * Delete an entity with the given ID.
     *
     * @param  mixed  $id
     * @return array
     */
    public function delete($id)
    {
        $deleted  = false;
        $instance = $id instanceof Model ? $id : $this->find($id);

        if ($instance) {
            $deleted = $instance->delete();

            event(get_called_class().'.entity.deleted', [$this, $instance]);
        }

        return [
            $deleted,
            $instance
        ];
    }

    /**
     * Cast HTTP request object to an array if need be.
     *
     * @param  array|Request  $request
     * @return array
     */
    protected function castRequest($request)
    {
        return $request instanceof Request ? $request->all() : $request;
    }
}
