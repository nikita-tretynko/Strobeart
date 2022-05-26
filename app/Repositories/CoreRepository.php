<?php


namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CoreRepository
 * @package App\Repositories
 */
abstract class CoreRepository
{

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Model|mixed
     */
    protected function startConditions()
    {
        return clone $this->model;
    }

    /**
     * @var \Illuminate\Contracts\Foundation\Application|Model|mixed
     */
    protected Model $model;

    /**
     * @return mixed
     */
    abstract protected function getModelClass();

    /**
     * CoreRepository constructor.
     */
    public function __construct()
    {
        $this->model = app($this->getModelClass());
    }

    /**
     * data from admin request for table (Vue Good Table)
     *
     * @param array $data
     * @return array
     */
    protected function getVariablesForTables(array $data): array
    {
        $sort_field = $data['sort'][0]['field'] ?? null;
        $sort_direction = $data['sort'][0]['type'] ?? null;
        $sort_direction = $sort_direction === 'none' ? null : $sort_direction;
        $collected_data = collect($data);
        $page = (int)$collected_data->get('page', 1);
        $limit = (int)$collected_data->get('perPage', 25);
        $offset = $page * $limit - $limit;
        $search = $collected_data->get('search');

        return compact('sort_field', 'sort_direction', 'limit', 'offset', 'search');
    }

}
