<?php
namespace Caffeinated\Repository\Contracts;

interface RepositoryInterface
{
    public function delete($id);
    public function find($id);
    public function findBySlug($slug);
    public function getAll($orderBy = array('id', 'asc'));
    public function getAllPaginated($orderBy = array('id', 'asc'), $perPage = 25);
    public function store($request);
    public function update($id, $request);
    public function with($relationships);
    public function dropdown($name, $value);
}
