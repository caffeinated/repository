<?php

namespace Caffeinated\Repository\Contracts;

interface Repository
{
    /**
     * Find an entity by its primary key.
     *
     * @param int   $id
     * @param array $columns
     * @param array $with
     */
    public function find($id, $columns = ['*'], $with = []);

    /**
     * Find the entity by the given attribute.
     *
     * @param string $attribute
     * @param string $value
     * @param array  $columns
     * @param array  $with
     */
    public function findBy($attribute, $value, $columns = ['*'], $with = []);

    /**
     * Find all entities.
     *
     * @param array $columns
     * @param array $with
     */
    public function findAll($columns = ['*'], $with = []);

    /**
     * Find all entities matching where conditions.
     *
     * @param array $where
     * @param array $columns
     * @param array $with
     */
    public function findWhere($where, $columns = ['*'], $with = []);

    /**
     * Find all entities matching whereIn conditions.
     *
     * @param string $attribute
     * @param array  $values
     * @param array  $columns
     * @param array  $with
     */
    public function findWhereIn($attribute, $values, $columns = ['*'], $with = []);

    /**
     * Find all entities matching whereNotIn conditions.
     *
     * @param string $attribute
     * @param array  $values
     * @param array  $columns
     * @param array  $with
     */
    public function findWhereNotIn($attribute, $values, $columns = ['*'], $with = []);

    /**
     * Find an entity matching the given attributes or create it.
     *
     * @param array $attributes
     */
    public function findOrCreate($attributes);

    /**
     * Create a new entity with the given attributes.
     *
     * @param mixed $attributes
     */
    public function create($attributes);

    /**
     * Update an entity with the given attributes.
     *
     * @param mixed $id
     * @param mixed $attributes
     */
    public function update($id, $attributes);

    /**
     * Delete an entity with the given ID.
     *
     * @param mixed $id
     *
     * @return array
     */
    public function delete($id);

    /**
     * Set the relationships that should be eager loaded.
     *
     * @param mixed $relationships
     */
    public function with($relationships);

    /**
     * Add an "order by" clause to the repository instance.
     *
     * @param string $column
     * @param string $direction
     */
    public function orderBy($column, $direction = 'asc');

    /**
     * Get an array with the values of the given column from entities.
     *
     * @param string      $column
     * @param string|null $key
     */
    public function pluck($column, $key = null);

    /**
     * Paginate the given query for retrieving entities.
     *
     * @param int|null $perPage
     * @param array    $columns
     * @param string   $pageName
     * @param int|null $page
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null);

    /**
     * Magic callStatic method to forward static methods to the model.
     *
     * @param string $method
     * @param array  $parameters
     */
    public static function __callStatic($method, $parameters);

    /**
     * Magic call method to forward methods to the model.
     *
     * @param string $method
     * @param array  $parameters
     */
    public function __call($method, $parameters);
}
