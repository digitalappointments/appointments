<?php
require_once(dirname(__FILE__) . "/../../lib/env/bootstrap.php");
define('ENTRY_POINT_TYPE', 'test');

include_once("lib/model/account/Account.php");

/**
mysql> show fields from account;
+-------------------+--------------+------+-----+---------+-------+
| Field             | Type         | Null | Key | Default | Extra |
+-------------------+--------------+------+-----+---------+-------+
| id                | char(36)     | NO   | PRI | NULL    |       |
| name              | varchar(150) | YES  | MUL | NULL    |       |
| dateEntered       | datetime     | YES  |     | NULL    |       |
| dateModified      | datetime     | YES  |     | NULL    |       |
| deleted           | int(11)      | YES  |     | 0       |       |
| industry          | varchar(100) | YES  |     | NULL    |       |
| addressStreet     | varchar(150) | YES  |     | NULL    |       |
| addressCity       | varchar(100) | YES  |     | NULL    |       |
| addressState      | varchar(100) | YES  |     | NULL    |       |
| addressPostalcode | varchar(20)  | YES  |     | NULL    |       |
| addressCountry    | varchar(150) | YES  |     | NULL    |       |
| officePhone       | varchar(100) | YES  |     | NULL    |       |
| altPhone          | varchar(100) | YES  |     | NULL    |       |
| website           | varchar(255) | YES  |     | NULL    |       |
| active            | int(11)      | YES  |     | 0       |       |
| trial             | int(11)      | YES  |     | 0       |       |
+-------------------+--------------+------+-----+---------+-------+
**/
//
//$bindTemplate = 'ssi';
//$values=array( '123', 'abc', 0);
//$sql = "INSERT into qwerty (id, name, deleted) VALUES (?,?,?)";
//
//// $sql = "INSERT into {$this->tableName} ({$fieldSet}) VALUES {$placeholders};";
//$stmt = D::$dbm->prepare($sql);
//
//D::$dbm->bindParameters($stmt, $bindTemplate, $values);
//D::$dbm->execute($stmt);
//$errno = D::$dbm->getErrno();
//$affectedRows = D::$dbm->affectedRows($stmt);
//D::$dbm->closeStatement($stmt);
//
//exit;
//
//
//

$account = new Account();

$account->setName('Tri-Star, LTD');
$account->setDateEntered('2014-08-24 16:15:00');
$account->setDateModified('2014-08-25 10:15:00');

$account->insert();
print_r($account);

$account2 = new Account();
$account2->retrieve($account->getId());
print_r($account2);

$account->delete();

exit;




class Test
{
    protected $dbm;
    const DEFAULT_ROWS_PER_REQUEST = 100;

    protected $fields = array(
        'id' => array(
            'type' => 'char',
            'len'  => 36,
            'api'  => true,
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
        ),
        'deleted' => array(
            'type' => 'int',
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
            'api'  => false,
        ),
        'addressCountry' => array(
            'type' => 'varchar',
            'len'  => 150,
            'api'  => false,
        ),
        'officePhone' => array(
            'type' => 'varchar',
            'len'  => 100,
            'api'  => true,
        ),
        'altPhone' => array(
            'type' => 'varchar',
            'len'  => 100,
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
            'api'  => false,
        ),
    );

    protected $api_fields;
    protected $database_columns;
    protected $tableName = 'account';

    /**
     *
     */
    function __construct()
    {
        $this->dbm = DBManagerFactory::getDatabaseManager();
        if (empty($this->dbm)) {
            Log::fatal('MailStatus: Unable to connect to Database ...');
            throw new DatabaseException(DATABASE_EXCEPTION_CONNECTION_FAILED, DatabaseException::ConnectionFailed);
        }

        $event_notice_setting = Config::get('misc.send_event_notice');
        if ($event_notice_setting == 'y' || $event_notice_setting == 'Y') {
            $this->send_event_notice = true;
        }

        $this->database_columns = array_keys($this->fields);
        $this->api_fields = array();
        foreach($this->fields As $fieldName => $fieldInfo) {
            if (!empty($fieldInfo['api'])) {
                $this->api_fields[] = $fieldName;
            }
        }

        // print_r($this->database_columns);
        // print_r($this->api_fields);
          
    	/*----
        $fields = array();
        $sql = "DESCRIBE " . $this->tableName;
        printf("\n%s\n",$sql);
        $result = $this->dbm->query($sql);
        while($row = $this->dbm->fetchArray($result)) {
			$info = array();
			$t=explode('(', $row['Type']); 
			print_r($t);
			$info['type'] = $t[0];
			if (count($t)>1) {
			   $info['len'] = (int) $t[1];
			}
			$fields[$row['Field']] = $info;
        }
		print_r($fields);
		---*/  
    }


    public function go()
    {
        $rows = array();
        $last_id  = empty($options['last_id']) ? '0' : $options['last_id'];
        $max_rows = empty($options['max_num']) ? self::DEFAULT_ROWS_PER_REQUEST : (int) $options['max_num'];

        $stmt = false;
        try {
            $api_select_fields = $this->dbm->getSelectSet(self::$api_fields);
            $sql  = "SELECT {$api_select_fields} FROM tracking";
            $sql .= " WHERE customer_id = ?";
            $sql .= " AND id > ?";
            $sql .= " ORDER by id LIMIT ?";
            $stmt = $this->dbm->prepare($sql);

            $bindTemplate = 'ssi';
            $bindParams = array($customer_id, $last_id, $max_rows);
            $this->dbm->bindParameters($stmt, $bindTemplate, $bindParams);
            $this->dbm->execute($stmt);
            while($row = $this->dbm->fetchResult($stmt, self::$api_fields)) {
                if (!empty($row['location'])) {
                    $row['location'] = json_decode(urldecode($row['location']), true);
                }
                $rows[] = $row;
            }
            $count = count($rows);
            if ($count > 0) {
                $last_id_response = $rows[$count-1]['id'];
                $this->updateTrackingStatus($customer_id, $last_id, $last_id_response);
            }
            $this->dbm->closeStatement($stmt);
        } catch (DatabaseException $e) {
            if ($stmt !== false) {
                $this->dbm->closeStatement($stmt);
            }
            Log::error($e->getLogMessage());
        } catch (Exception $e) {
            if ($stmt !== false) {
                $this->dbm->closeStatement($stmt);
            }
            Log::error($e->getMessage());
        }
        return $rows;
    }

    /**
     * @access public
     * @param string $customer_id required
     * @param array $options required
     */
    public function getTrackingRecords($customer_id, $options)
    {
        $rows = array();
        $last_id  = empty($options['last_id']) ? '0' : $options['last_id'];
        $max_rows = empty($options['max_num']) ? self::DEFAULT_ROWS_PER_REQUEST : (int) $options['max_num'];

        $stmt = false;
        try {
            $api_select_fields = $this->dbm->getSelectSet(self::$api_fields);
            $sql  = "SELECT {$api_select_fields} FROM tracking";
            $sql .= " WHERE customer_id = ?";
            $sql .= " AND id > ?";
            $sql .= " ORDER by id LIMIT ?";
            $stmt = $this->dbm->prepare($sql);

            $bindTemplate = 'ssi';
            $bindParams = array($customer_id, $last_id, $max_rows);
            $this->dbm->bindParameters($stmt, $bindTemplate, $bindParams);
            $this->dbm->execute($stmt);
            while($row = $this->dbm->fetchResult($stmt, self::$api_fields)) {
                if (!empty($row['location'])) {
                    $row['location'] = json_decode(urldecode($row['location']), true);
                }
                $rows[] = $row;
            }
            $count = count($rows);
            if ($count > 0) {
                $last_id_response = $rows[$count-1]['id'];
                $this->updateTrackingStatus($customer_id, $last_id, $last_id_response);
            }
            $this->dbm->closeStatement($stmt);
        } catch (DatabaseException $e) {
            if ($stmt !== false) {
                $this->dbm->closeStatement($stmt);
            }
            Log::error($e->getLogMessage());
        } catch (Exception $e) {
            if ($stmt !== false) {
                $this->dbm->closeStatement($stmt);
            }
            Log::error($e->getMessage());
        }
        return $rows;
    }
}

$test = new Test();

// $test->run();

exit;
