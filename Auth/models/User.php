<?php

namespace Auth\models;

use Core\Model;
use Core\Query;
use Core\Password;

class User extends Model
{
    protected $permittedColumns = array(
        'site_id',
        'lastname',
        'firstname',
        'email',
        'password',
        'address',
        'email_verification_code',
        'email_verification_date',
        'media_id',
        'group_id',
        'active'
    );

    public $labelOption = 'fullname';
    public $valueOption = 'id';

    public function __construct($data = array())
    {
        parent::__construct($data);
        
        $this->fullname = $this->getFullName();
    }

    /**
     * Get list of associed table(s)
     *
     * @return mixed
     */
    public function getAssociations()
    {
        return array(
            'site' => array(
                'type' => '1',
                'model' => 'Multisite\\models\\Site',
                'key' => 'site_id'
            ),
            'media' => array(
                'type' => '1',
                'model' => 'Media\\models\\Media',
                'key' => 'media_id'
            ),
            'group' => array(
                'type' => '1',
                'model' => 'Auth\\models\\Group',
                'key' => 'group_id'
            )
        );
    }

    public function getValidations()
    {
        $validations = parent::getValidations();

        $validations = array_merge($validations, array(
            'site_id' => array(
                'type' => 'required',
                'defaultValue' => null
            ),
            'lastname' => array(
                'type' => 'required',
                'filters' => 'trim',
                'error' => 'Nom obligatoire'
            ),
            'firstname' => array(
                'type' => 'required',
                'filters' => 'trim',
                'error' => 'Prénom obligatoire'
            ),
            'email' => array(
                array(
                    'type' => 'required',
                    'filters' => 'trim',
                    'error' => 'Email obligatoire'
                ),
                array(
                    'type' => 'email',
                    'error' => 'Email invalide'
                )
            )
        ));

        return $validations;
    }

    /**
     * Get an user by email
     *
     * @param string $email
     *
     * @return Auth\models\User
     */
    public static function findByEmail($email)
    {
        $res = self::findAll('email = \''.$email.'\'');
        if (!empty($res)) {
            return $res[0];
        } else {
            return null;
        }
    }

    /**
     * Get fullname : <lastname>[ <firstname>]
     *
     * @return string
     */
    public function getFullName()
    {
        return ltrim($this->firstname.' ').$this->lastname;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImageUrl()
    {
        if ($this->media !== null) {
        return $this->media->getUrl();
        } else {
            return '';
        }
    }
}
