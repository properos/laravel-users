<?php

namespace Properos\Users\Classes;

use Properos\Base\Classes\Base;
use Properos\Users\Models\Permission;

class CPermission extends Base
{
    public function __construct()
    {
        parent::__construct(Permission::class, 'Permission');
    }

    public function init_fillable()
    {
        foreach ($this->model->getFillable() as $key) {
            switch ($key) {
                case 'url':
                case 'description':
                    $this->fillable[$key] = null;
                    break;
                default:
                    $this->fillable[$key] = '';
                    break;
            }
        }
    }

    public function create(array $data)
    {
        $rules = [
            'label' => 'required|string|max:50',
            'description' => 'nullable|string|255'
        ];

        return $this->createModel($data, $rules);
    }

    public function update(array $data)
    {
        $rules = [
            'id' => 'required|integer',
            'label' => 'required|string|max:50',
            'description' => 'nullable|string|255'
        ];

        return $this->updateModel($data, $rules);
    }

    
    public function delete($id, $where = [])
    {
        return $this->deleteModel($id, $where);
    }

    public function formatting($data)
    {
        if(isset($data['label'])){
            $data['label'] = preg_replace('/\s+/g', ' ', trim($data['label']));
            $data['name'] = preg_replace('/\s+/g', '_', $data['label']);
        }

        return $data;
    }
}
