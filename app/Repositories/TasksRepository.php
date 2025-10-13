<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface TasksRepository.
 *
 * @package namespace App\Repositories;
 */
interface TasksRepository extends RepositoryInterface
{
    public function initModel($id = null);
    public function totalData($request);
    public function findAll($request, $raw = false);
    public function findById($request, $id, $relations);
    public function getList($request, $relations);
    public function store($request, $id = null, $customRequest = null);
    public function remove($request, $id);
    public function restore($request, $id);
}
