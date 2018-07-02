<?php

namespace Caffeinated\Repository\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class EloquentRepository extends Repository
{
    /**
     * Find an entity by its primary key.
     *
     * @param int   $id
     * @param array $columns
     * @param array $with
     */
    public function find($id, $columns = ['*'], $with = [])
    {
        $cacheKey = $this->generateKey([$id, $columns, $with]);

        return $this->cacheResults(get_called_class(), __FUNCTION__, $cacheKey, function () use ($id, $columns, $with) {
            return $this->model->with($with)
                ->find($id, $columns);
        });
    }

    /**
     * Find the entity by the given attribute.
     *
     * @param string $attribute
     * @param string $value
     * @param array  $columns
     * @param array  $with
     */
    public function findBy($attribute, $value, $columns = ['*'], $with = [])
    {
        $cacheKey = $this->generateKey([$attribute, $value, $columns, $with]);

        return $this->cacheResults(get_called_class(), __FUNCTION__, $cacheKey, function () use ($attribute, $value, $columns, $with) {
            return $this->model->with($with)
                ->where($attribute, '=', $value)
                ->first($columns);
        });
    }

    /**
     * Find all entities.
     *
     * @param array $columns
     * @param array $with
     */
    public function findAll($columns = ['*'], $with = [])
    {
        $cacheKey = $this->generateKey([$columns, $with]);

        return $this->cacheResults(get_called_class(), __FUNCTION__, $cacheKey, function () use ($columns, $with) {
            return $this->model->with($with)
                ->get($columns);
        });
    }

    /**
     * Find an entity by its primary key or fail if it doesn't exist.
     *
     * @param int   $id
     * @param array $columns
     * @param array $with
     */
    public function findOrFail($id, $columns = ['*'], $with = [])
    {
        $cacheKey = $this->generateKey([$id, $columns, $with]);

        return $this->cacheResults(get_called_class(), __FUNCTION__, $cacheKey, function () use ($id, $columns, $with) {
            return $this->model->with($with)
                ->findOrFail($id, $columns);
        });
    }

    /**
     * Find all entities matching where conditions.
     *
     * @param mixed $where
     * @param array $columns
     * @param array $with
     */
    public function findWhere($where, $columns = ['*'], $with = [])
    {
        $where    = $this->castRequest($where);
        $cacheKey = $this->generateKey([$where, $columns, $with]);

        return $this->cacheResults(get_called_class(), __FUNCTION__, $cacheKey, function () use ($where, $columns, $with) {
            $model = $this->model instanceof Model ? $this->model->query() : $this->model;

            foreach ($where as $attribute => $value) {
                if (is_array($value)) {
                    list($attribute, $condition, $value) = $value;

                    $model->where($attribute, $condition, $value);
                } else {
                    $model->where($attribute, '=', $value);
                }
            }

            return $model->with($with)
                ->get($columns);
        });
    }

    /**
     * Find all entities matching whereBetween conditions.
     *
     * @param  string  $attribute
     * @param  array  $values
     * @param  array  $columns
     * @param  array  $with
     */
    public function findWhereBetween($attribute, $values, $columns = ['*'], $with = [])
    {
        $values   = $this->castRequest($values);
        $cacheKey = $this->generateKey([$attribute, $values, $columns, $with]);

        return $this->cacheResults(get_called_class(), __FUNCTION__, $cacheKey, function () use ($attribute, $values, $columns, $with) {
            return $this->model->with($with)
                ->whereBetween($attribute, $values)
                ->get($columns);
        });
    }

    /**
     * Find all entities matching whereIn conditions.
     *
     * @param string $attribute
     * @param array  $values
     * @param array  $columns
     * @param array  $with
     */
    public function findWhereIn($attribute, $values, $columns = ['*'], $with = [])
    {
        $values   = $this->castRequest($values);
        $cacheKey = $this->generateKey([$attribute, $values, $columns, $with]);

        return $this->cacheResults(get_called_class(), __FUNCTION__, $cacheKey, function () use ($attribute, $values, $columns, $with) {
            return $this->model->with($with)
                ->whereIn($attribute, $values)
                ->get($columns);
        });
    }

    /**
     * Find all entities matching whereNotIn conditions.
     *
     * @param string $attribute
     * @param array  $values
     * @param array  $columns
     * @param array  $with
     */
    public function findWhereNotIn($attribute, $values, $columns = ['*'], $with = [])
    {
        $values   = $this->castRequest($values);
        $cacheKey = $this->generateKey([$attribute, $values, $columns, $with]);

        return $this->cacheResults(get_called_class(), __FUNCTION__, $cacheKey, function () use ($attribute, $values, $columns, $with) {
            return $this->model->with($with)
                ->whereNotIn($attribute, $values)
                ->get($columns);
        });
    }

    /**
     * Find an entity matching the given attributes or create it.
     *
     * @param array $attributes
     */
    public function findOrCreate($attributes)
    {
        $attributes = $this->castRequest($attributes);

        if (!is_null($instance = $this->findWhere($attributes)->first())) {
            return $instance;
        }

        return $this->create($attributes);
    }

    /**
     * Get an array with the values of the given column from entities.
     *
     * @param string      $column
     * @param string|null $key
     */
    public function pluck($column, $key = null)
    {
        $cacheKey = $this->generateKey([$column, $key]);

        return $this->cacheResults(get_called_class(), __FUNCTION__, $cacheKey, function () use ($column, $key) {
            return $this->model->pluck($column, $key);
        });
    }

    /**
     * Paginate the given query for retrieving entities.
     *
     * @param int|null $perPage
     * @param array    $columns
     * @param string   $pageName
     * @param int|null $page
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $cacheKey = $this->generateKey([$perPage, $columns, $pageName, $page]);

        return $this->cacheResults(get_called_class(), __FUNCTION__, $cacheKey, function () use ($perPage, $columns, $pageName, $page) {
            return $this->model->paginate($perPage, $columns, $pageName, $page);
        });
    }

    /**
     * Create a new entity with the given attributes.
     *
     * @param array $attributes
     */
    public function create($attributes)
    {
        $attributes = $this->castRequest($attributes);
        $instance   = $this->model->newInstance($attributes);

        event(implode('-', $this->tag).'.entity.creating', [$this, $instance, $attributes]);

        $created = $instance->save();

        event(implode('-', $this->tag).'.entity.created', [$this, $instance, $attributes]);

        return [
            $created,
            $instance,
        ];
    }

    /**
     * Update an entity with the given attributes.
     *
     * @param mixed $id
     * @param array $attributes
     */
    public function update($id, $attributes)
    {
        $attributes = $this->castRequest($attributes);
        $updated    = false;
        $instance   = $id instanceof Model ? $id : $this->find($id);

        if ($instance) {
            event(implode('-', $this->tag).'.entity.updating', [$this, $instance, $attributes]);

            $updated = $instance->update($attributes);

            event(implode('-', $this->tag).'.entity.updated', [$this, $instance, $attributes]);
        }

        return [
            $updated,
            $instance,
        ];
    }

    /**
     * Delete an entity with the given ID.
     *
     * @param mixed $id
     *
     * @return array
     */
    public function delete($id)
    {
        $deleted  = false;
        $instance = $id instanceof Model ? $id : $this->find($id);

        if ($instance) {
            event(implode('-', $this->tag).'.entity.deleting', [$this, $instance]);

            $deleted = $instance->delete();

            event(implode('-', $this->tag).'.entity.deleted', [$this, $instance]);
        }

        return [
            $deleted,
            $instance,
        ];
    }

    /**
     * Cast HTTP request object to an array if need be.
     *
     * @param array|Request $request
     *
     * @return array
     */
    protected function castRequest($request)
    {
        return $request instanceof Request ? $request->all() : $request;
    }
}
