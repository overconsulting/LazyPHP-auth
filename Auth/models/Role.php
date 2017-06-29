<?php

namespace Auth\models;

use Core\Model;

class Role extends Model
{
    protected $permittedColumns = array(
        'action',
        'label'
    );

    public function getValidations()
    {
        $validations = parent::getValidations();

        $validations = array_merge($validations, array(
            'action' => array(
                'type' => 'required',
                'error' => 'Action obligatoire'
            ),
            'label' => array(
                'type' => 'required',
                'error' => 'Label obligatoire'
            )
        ));
    }
}