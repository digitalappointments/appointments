<?php

class Account extends BaseObject
{
    public $tableName = 'account';

    public $fields = array(
        'id' => array(
            'type' => 'guid',
            'len'  => 36,
            'api'  => true,
            'key'  => true,
        ),
        'name' => array(
            'type' => 'varchar',
            'len'  => 150,
            'api'  => true,
        ),
        'dateEntered' => array(
            'type' => 'datetime',
            'api'  => true,
        ),
        'dateModified' => array(
            'type' => 'datetime',
            'write_reset' => true,
            'api'  => true,
        ),
        'deleted' => array(
            'type' => 'int',
            'api'  => true,
        ),
        'industry' => array(
            'type' => 'varchar',
            'len'  => 50,
            'api'  => true,
        ),
        'addressStreet' => array(
            'type' => 'varchar',
            'len'  => 150,
            'api'  => true,
        ),
        'addressCity' => array(
            'type' => 'varchar',
            'len'  => 100,
            'api'  => true,
        ),
        'addressState' => array(
            'type' => 'varchar',
            'len'  => 100,
            'api'  => true,
        ),
        'addressPostalcode' => array(
            'type' => 'varchar',
            'len'  => 20,
            'api'  => true,
        ),
        'addressCountry' => array(
            'type' => 'varchar',
            'len'  => 150,
            'api'  => true,
        ),
        'officePhone' => array(
            'type' => 'varchar',
            'len'  => 100,
            'api'  => true,
        ),
        'altPhone' => array(
            'type' => 'varchar',
            'len'  => 100,
            'api'  => true,
        ),
        'website' => array(
            'type' => 'varchar',
            'len'  => 255,
            'api'  => true,
        ),
        'active' => array(
            'type' => 'int',
            'api'  => true,
        ),
        'trial' => array(
            'type' => 'int',
            'api'  => true,
        ),
    );

}