<?php

//------ Table: account ------
Model::$definitions['accounts'] = array(
    'id' => array(
        'type' => 'guid',
        'key' => true,
        'len' => 36,
        'api' => true,
    ),
    'name' => array(
        'type' => 'varchar',
        'len' => 150,
        'api' => true,
    ),
    'dateEntered' => array(
        'type' => 'datetime',
        'api' => true,
    ),
    'dateModified' => array(
        'type' => 'datetime',
        'api' => true,
        'write_reset' => true,
    ),
    'deleted' => array(
        'type' => 'int',
        'len' => 11,
        'api' => true,
    ),
    'industry' => array(
        'type' => 'varchar',
        'len' => 100,
        'api' => true,
    ),
    'addressStreet' => array(
        'type' => 'varchar',
        'len' => 150,
        'api' => true,
    ),
    'addressCity' => array(
        'type' => 'varchar',
        'len' => 100,
        'api' => true,
    ),
    'addressState' => array(
        'type' => 'varchar',
        'len' => 100,
        'api' => true,
    ),
    'addressPostalcode' => array(
        'type' => 'varchar',
        'len' => 20,
        'api' => true,
    ),
    'addressCountry' => array(
        'type' => 'varchar',
        'len' => 150,
        'api' => true,
    ),
    'officePhone' => array(
        'type' => 'varchar',
        'len' => 100,
        'api' => true,
    ),
    'altPhone' => array(
        'type' => 'varchar',
        'len' => 100,
        'api' => true,
    ),
    'website' => array(
        'type' => 'varchar',
        'len' => 255,
        'api' => true,
    ),
    'active' => array(
        'type' => 'int',
        'len' => 11,
        'api' => true,
    ),
    'trial' => array(
        'type' => 'int',
        'len' => 11,
        'api' => true,
    ),
);


//------ Table: user ------
Model::$definitions['users'] = array(
    'id' => array(
        'type' => 'guid',
        'key' => true,
        'len' => 36,
        'api' => true,
    ),
    'email' => array(
        'type' => 'varchar',
        'len' => 100,
        'api' => true,
    ),
    'password' => array(
        'type' => 'varchar',
        'len' => 100,
        'api' => true,
    ),
    'dateEntered' => array(
        'type' => 'datetime',
        'api' => true,
    ),
    'dateModified' => array(
        'type' => 'datetime',
        'api' => true,
        'write_reset' => true,
    ),
    'deleted' => array(
        'type' => 'int',
        'len' => 11,
        'api' => true,
    ),
    'firstName' => array(
        'type' => 'varchar',
        'len' => 100,
        'api' => true,
    ),
    'lastName' => array(
        'type' => 'varchar',
        'len' => 100,
        'api' => true,
    ),
    'title' => array(
        'type' => 'varchar',
        'len' => 100,
        'api' => true,
    ),
    'phoneHome' => array(
        'type' => 'varchar',
        'len' => 100,
        'api' => true,
    ),
    'phoneMobile' => array(
        'type' => 'varchar',
        'len' => 100,
        'api' => true,
    ),
    'phoneWork' => array(
        'type' => 'varchar',
        'len' => 100,
        'api' => true,
    ),
    'phoneOther' => array(
        'type' => 'varchar',
        'len' => 100,
        'api' => true,
    ),
    'phoneFax' => array(
        'type' => 'varchar',
        'len' => 100,
        'api' => true,
    ),
    'addressStreet' => array(
        'type' => 'varchar',
        'len' => 150,
        'api' => true,
    ),
    'addressCity' => array(
        'type' => 'varchar',
        'len' => 100,
        'api' => true,
    ),
    'addressState' => array(
        'type' => 'varchar',
        'len' => 100,
        'api' => true,
    ),
    'addressPostalcode' => array(
        'type' => 'varchar',
        'len' => 20,
        'api' => true,
    ),
    'address_country' => array(
        'type' => 'varchar',
        'len' => 150,
        'api' => true,
    ),
    'active' => array(
        'type' => 'int',
        'len' => 11,
        'api' => true,
    ),
);
