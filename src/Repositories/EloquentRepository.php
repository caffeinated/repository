<?php

namespace Caffeinated\Repository\Repositories;

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
        return $this->model->with($with)
            ->find($id, $columns);
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
        return $this->model->with($with)
            ->where($attribute, '=', $value)
            ->first($columns);
    }

    /**
     * Find all entities.
     *
     * @param  array  $columns
     * @param  array  $with
     */
    public function findAll($columns = ['*'], $with = [])
    {
        return $this->model->with($with)
            ->get($columns);
    }

    /**
     * Find all entities matching where conditions.
     *
     * @param  array  $where
     * @param  array  $columns
     * @param  array  $with
     */
    public function findWhere(array $where, $columns = ['*'], $with = [])
    {
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
    }

    /**
     * Find all entities matching whereIn conditions.
     *
     * @param  string  $attribute
     * @param  array  $values
     * @param  array  $columns
     * @param  array  $with
     */
    public function findWhereIn($attribute, array $values, $columns = ['*'], $with = [])
    {
        return $this->model->with($with)
            ->whereIn($attribute, $values)
            ->get($columns);
    }

    /**
     * Find all entities matching whereNotIn conditions.
     *
     * @param  string  $attribute
     * @param  array  $values
     * @param  array  $columns
     * @param  array  $with
     */
    public function findWhereNotIn($attribute, array $values, $columns = ['*'], $with = [])
    {
        return $this->model->with($with)
            ->whereNotIn($attribute, $values)
            ->get($columns);
    }

    /**
     * Find an entity matching the given attributes or create it.
     *
     * @param  array  $attributes
     */
    public function findOrCreate(array $attributes)
    {
        if (! is_null($instance = $this->findWhere($attributes)->first())) {
            return $instance;
        }

        return $this->create($attributes);
    }

    /**
     * Create a new entity with the given attributes.
     *
     * @param  array  $attributes
     */
    public function create(array $attributes = [])
    {
        $instance = $this->model->newInstance($attributes);
        $created  = $instance->save();

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
    public function update($id, array $attributes = [])
    {
        $updated  = false;
        $instance = $id instanceof Model ? $id : $this->find($id);

        if ($instance) {
            $updated = $instance->update($attributes);
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
        }

        return [
            $deleted,
            $instance
        ];
    }

    /**
     * Get an array with the values of the given column from entities.
     *
     * @param  string  $column
     * @param  string|null  $key
     */
    public function pluck($column, $key = null)
    {
        return $this->model->pluck($column, $key);
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
        return $this->model->paginate($perPage, $columns, $pageName, $page);
    }
}
