<?php namespace App\Repositories;


class DbRepository
{

    protected $model;

    function __construct($model)
    {
        $this->model = $model;
    }

    public function store($data)
    {
        return $this->model->create($data);
    }

    public function destroy($id)
    {
        $model = $this->model->findOrFail($id);
        $model->delete();

        return $model;
    }

    public function getTotal()
    {
        return $this->model->count();
    }

    public function update_state($id, $state)
    {

        $model = $this->findById($id);
        $model->published = $state;
        $model->save();

        return $model;
    }

    public function findById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function update_active($id, $state)
    {

        $model = $this->findById($id);
        $model->active = $state;
        $model->save();

        return $model;
    }

    public function update_feat($id, $feat)
    {
        $model = $this->findById($id);
        $model->featured = $feat;
        $model->save();

        return $model;
    }

    public function update_status($id, $status)
    {

        $model = $this->findById($id);
        $model->status = $status;
        $model->save();

        return $model;
    }


}