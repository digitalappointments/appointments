<?php

abstract class BaseObject
{
    public $debug=false;

    public $fields = array();
    public $tableName;

    private $api_fields = array();
    private $auto_increment_key_field;

    private $field_types = array(
        'guid'      => '*',
        'char'      => '',
        'varchar'   => '',
        'int'       => 0,
        'tinyint'   => 0,
        'bigint'    => 0,
        'date'      => '*',  // '*' - compute at runtime
        'time'      => '*',
        'datetime'  => '*',
        'timestamp'  => '*',
        'decimal'   => 0.0,
    );

    public function __construct($values = array())
    {
        if (empty($this->tableName) || !is_string($this->tableName)) {
            throw new ServiceException(get_class($this) . " is missing Table Definition.");
        }
        if (empty($this->fields) && !empty(Model::$definitions[$this->tableName])) {
            $this->fields = Model::$definitions[$this->tableName];
        }
        if (empty($this->fields) || !is_array($this->fields)) {
            throw new ServiceException(get_class($this) . " is missing Field Definition.");
        }

        if (!is_array($values)) {
            throw new ServiceException(get_class($this) . ' - invalid argument sent to constructor.', Service::InvalidParam);
        }

        $this->api_fields = array();
        $this->primaryKeys = array();
        foreach($this->fields AS $fieldName => $fieldInfo) {
            if (!empty($fieldInfo['api'])) {
                $this->api_fields[] = $fieldName;
            }
            if (!empty($fieldInfo['auto'])) {              // AutoIncrement Keys (only)
                $this->auto_increment_key_field = $fieldName;
            } elseif (!empty($fieldInfo['key'])) {         // Non-AutoIncrement Key Combination
                $this->primaryKeys[$fieldName]  = 'key';
            }
        }

        $this->initialize();

        foreach ($values as $fieldName => $value) {  // An Array of Values Passed in Will OverWrite Any Initialization Values
            if (!isset($this->fields[$fieldName])) {
                throw new ServiceException("No such field name {$fieldName} on " . get_class($this));
            }
            $this->$fieldName = $value;
        }
    }

    /**
     * This magic method handles getting and setting
     */
    public function __call($methodName, array $parameters)
    {
        if (substr($methodName, 0, 3) == 'get') {
            $fieldName = lcfirst(substr($methodName, 3));
            if (isset($this->fields[$fieldName])) {
                return $this->$fieldName;
            } else {
                throw new ServiceException("No such field name $fieldName on " . get_class($this), Service::Error);
            }
        } elseif (substr($methodName, 0, 3) == 'set') {
            $fieldName = lcfirst(substr($methodName, 3));
            if (isset($this->fields[$fieldName])) {
                if(count($parameters) == 1) {
                    $this->$fieldName = $parameters[0];
                } else {
                    throw new ServiceException("Set $fieldName on " . get_class($this) . " requires exactly one parameter.", Service::Error);
                }
            } else {
                throw new ServiceException("No such field name {$fieldName} on " . get_class($this), Service::Error);
            }
        } else {
            throw new ServiceException("No such method {$methodName} on " . get_class($this), Service::Error);
        }
    }


    //--------------------------------------------------------------------------
    // PUBLIC Methods
    //--------------------------------------------------------------------------

    /**
     * Retrieve
     */
    public function retrieve($id=null, $options=array())
    {
        if (empty($id)) {
            return false;
        }

        $deleted = (!empty($options['deleted']));  // default: false - do not include deleted
        $stmt = false;
        try {
            $fieldKeys = array_keys($this->fields);
            $select_fields = D::$dbm->getSelectSet($fieldKeys);
            $sql  = "SELECT {$select_fields} FROM {$this->tableName}";
            $sql .= " WHERE id = ?";
            if (!$deleted) {
                $sql .= " AND deleted = 0";
            }
            $stmt = D::$dbm->prepare($sql);

            // printf("\nSQL: %s\n\n",$sql);

            $bindTemplate = 's';
            $bindParams = array($id);
            D::$dbm->bindParameters($stmt, $bindTemplate, $bindParams);
            D::$dbm->execute($stmt);
            $row = D::$dbm->fetchResult($stmt, $fieldKeys);
            D::$dbm->closeStatement($stmt);
            if ($row) {
                foreach($row as $f => $v) {
                    $this->$f = $v;
                }
                return $this;
            }
            return false;
        } catch (DatabaseException $e) {
            if ($stmt !== false) {
                D::$dbm->closeStatement($stmt);
            }
            Log::error($e->getLogMessage());
            throw $e;
        } catch (Exception $e) {
            if ($stmt !== false) {
                D::$dbm->closeStatement($stmt);
            }
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * Insert
     *
     * @return boolean true if inserted
     */
    public function insert($options=array())
    {
        if(!$this->isPrimaryKeySet()) {
            $primaryKeyList = implode(',', array_keys($this->primaryKeys));
            throw new ServiceException("Cannot insert a " . get_class($this) . " without existing values for the primary key: {$primaryKeyList}.", Service::Error);
        }

        $fieldsCopy = $this->fields;
        if (!empty($this->auto_increment_key_field)) {
            unset($fieldsCopy[$this->auto_increment_key_field]);
        }

        $bindTemplate = '';
        foreach($fieldsCopy as $fieldName => $fieldInfo) {
            if ($fieldInfo['type'] === 'int' || $fieldInfo['type'] === 'tinyint' || $fieldInfo['type'] === 'bigint') {
                $bindTemplate .= 'i';
            } else {
                $bindTemplate .= 's';
            }
        }

        $fkeys = array_keys($fieldsCopy);
        $values = array();
        foreach($fkeys AS $k) {
            $values[] = $this->$k;
        }

        $fieldSet =  implode(', ', $fkeys);
        $placeholders = sqlPlaceholder( $fieldsCopy );

        $sql = "INSERT into {$this->tableName} ({$fieldSet}) VALUES {$placeholders};";
        $stmt = D::$dbm->prepare($sql);

        D::$dbm->bindParameters($stmt, $bindTemplate, $values);
        D::$dbm->execute($stmt);
        $errno = D::$dbm->getErrno();
        $affectedRows = D::$dbm->affectedRows($stmt);
        D::$dbm->closeStatement($stmt);

        if ($errno) {
            throw new ServiceException(get_class($this) . ": insert failed - " . D::$dbm->getLastError(), $errno);
        }
        if ($affectedRows != 1) {
            throw new ServiceException(get_class($this) . ": insert failed -  affected rows="  . $affectedRows, $errno);
        }

        return true;
    }

    /**
     * Update
     *
     * @return integer affectedRows
     */
    public function update($options=array())
    {
        if (!empty($options['fieldset'])) {  // If no empty, fieldset is an array of ields to be updated (as opposed to All Fields)
            $updateFields = array();
            $tm = time();
            $farray = $options['fieldset'];
            if (!is_array($farray) || count($farray) == 0) {
                throw new ServiceException(get_class($this) . " - update: fieldset option invalid");
            }
            foreach($farray AS $fieldName) {
                if (!isset($this->fields[$fieldName])) {
                    throw new ServiceException(get_class($this) . " - update: fieldset option specifies invalid field : {$fieldName}");
                }
                if (!empty($this->fields[$fieldName]['write_reset'])) {
                    $type = $this->fields[$fieldName]['type'];
                    if ($this->field_types[$type] === '*') { // Compute Type with Write_reset option - Re-Compute on Write
                        $val = $this->_compute($fieldName, $type, $tm);
                        $this->$fieldName = $val;
                    }
                }
                $updateFields[$fieldName] = $this->$fieldName;
            }
        } else {
            $updateFields = $this->toArray();
        }

        if (!empty($this->auto_increment_key_field)) {       // Remove AutoIncrement Key Fields from Update Field Set
            unset($updateFields[$this->auto_increment_key_field]);
        }
        foreach ($this->primaryKeys as $fieldName => $keyType) {   // Remove Primary Key Fields from Update Field Set
            unset($updateFields[$fieldName]);
        }

        $bindTemplate = '';
        foreach($updateFields as $fieldName => $fieldValue) {
            $fieldInfo = $this->fields[$fieldName];
            if ($fieldInfo['type'] === 'int' || $fieldInfo['type'] === 'tinyint' || $fieldInfo['type'] === 'bigint') {
                $bindTemplate .= 'i';
            } else {
                $bindTemplate .= 's';
            }
        }

        $fkeys  = array_keys($updateFields);
        $fieldList = implode("=?,", $fkeys);
        $fieldList .= "=?";

        $whereArray = array();
        $primaryValues = array();
        foreach ($this->primaryKeys as $fieldName => $keyType) {
            $whereArray[] = $fieldName . "=?";
            $primaryValues[] = $this->$fieldName;
            $fieldInfo = $this->fields[$fieldName];
            if ($fieldInfo['type'] === 'int' || $fieldInfo['type'] === 'tinyint' || $fieldInfo['type'] === 'bigint') {
                $bindTemplate .= 'i';
            } else {
                $bindTemplate .= 's';
            }
        }
        $where = implode(' AND ', $whereArray);

        $values = array_merge(array_values($updateFields), $primaryValues);

        $sql = "UPDATE {$this->tableName} SET {$fieldList} WHERE {$where}";
        $stmt = D::$dbm->prepare($sql);
        D::$dbm->bindParameters($stmt, $bindTemplate, $values);
        D::$dbm->execute($stmt);
        $errno = D::$dbm->getErrno();
        $affectedRows = D::$dbm->affectedRows($stmt);
        D::$dbm->closeStatement($stmt);

        if ($errno) {
            throw new ServiceException(get_class($this) . ": update failed - " . D::$dbm->getLastError());
        }

       return $affectedRows;
    }

    /**
     * This method attempts to insert a record and then Updates if Record Key already Exists
     *
     * @return integer AffectedRows
     */
    public function upsert($options=array())
    {
        try {
            $result = $this->insert($options);
        } catch (Exception $e) {
            if ($e->getCode() == DBManager::MYSQL_CODE_DUPLICATE_KEY) {
                $result = ($this->update($options) > 0) ? true : false;
            } else {
                throw $e;
            }
        }
        return $result;
    }

    /**
     * Delete
     *
     * This method deletes the row associated with the object from the relevant database table.
     * While the table interaction uses the database field names, of course, the exception thrown
     * in the event of unset Primary Keys refers to the friendly PHP name.
     *
     * @return integer
     */
    public function delete($options=array())
    {
        if(!$this->isPrimaryKeySet()) {
            $primaryKeyList = implode(',', array_keys($this->primaryKeys));
            throw new ServiceException("Cannot delete a " . get_class($this) . " without existing values for the primary key: {$primaryKeyList}.", Service::Error);
        }

        $deleted = (!empty($options['deleted']));  // default: false - do not include deleted

        $where = implode(" = ? AND ", array_keys($this->primaryKeys));
        $where .= " = ?";
        if (!$deleted) {
            $where .= " AND deleted = 0";
        }

        $keyValues = $this->getPrimaryKeys();
        $values = array_values($this->getPrimaryKeys());
        $bindTemplate = '';
        foreach($keyValues as $fieldName => $fieldInfo) {
            if ($fieldInfo['type'] === 'int' || $fieldInfo['type'] == 'tinyint' || $fieldInfo['type'] == 'bigint') {
                $bindTemplate .= 'i';
            } else {
                $bindTemplate .= 's';
            }
        }

        $sql = "DELETE FROM {$this->tableName} WHERE {$where}";
        $stmt = D::$dbm->prepare($sql);
        D::$dbm->bindParameters($stmt, $bindTemplate, $values);
        D::$dbm->execute($stmt);
        $affectedRows = D::$dbm->affectedRows($stmt);
        D::$dbm->closeStatement($stmt);

        return $affectedRows;
    }

    /**
     * This method returns the value of the PrimaryKey
     *
     * @return array An array of primary key values
     */
    public function getPrimaryKeys()
    {
        $keyValues = array();
        foreach($this->primaryKeys AS $fieldName => $keyType) {
            $keyValues[$fieldName] = $this->$fieldName;
        }
        return $keyValues;
    }

    /**
     * Returns an array of all field names for the object
     *
     * @return array
     */
    public function getFieldNames()
    {
        return array_keys($this->fields);
    }

    /**
     * Returns an array of API-only Fields and Values for the object
     *
     * @return array
     */
    public function toApi($fieldSet=array())
    {
        $apiFields = array();
        if (count($fieldSet > 0)) {
            // Specified Field Set (that are also API
            foreach ($fieldSet as $fieldName) {
                if (!empty($this->fields[$fieldName]['api'])) {
                    $apiFields[$fieldName] = $this->$fieldName;
                }
            }
        } else {
            // All API Fields
            foreach ($this->api_fields as $fieldName) {
                $apiFields[$fieldName] = $this->$fieldName;
            }
        }
        return $apiFields;
    }

    /**
     * Returns an array of All Fields and Values for the object
     *
     * @return array
     */
    public function toArray()
    {
        $fields = array();
        foreach($this->fields As $fieldName => $fieldInfo) {
            $fields[$fieldName] = $this->$fieldName;
        }
        return $fields;
    }

    /**
     * Answer whether a field defined as Primary Key is Set (!empty)
     *
     * @return boolean
     */
    public function isPrimaryKeySet()
    {
        if (!empty($this->primaryKeys['key'])) {
            $fieldName = $this->primaryKeys['key'];
            return !empty($this->$fieldName);
        }
        return true;  // Primary Key is Set (or there is no Primary Key Defined)
    }


    //----------------------------------------------------------
    // PROTECTED Methods
    //----------------------------------------------------------



    //----------------------------------------------------------
    // PRIVATE Methods
    //----------------------------------------------------------

    /*-------------------------
     gmdate(format,timestamp);

    Parameter 	Description
         format 	Required.
    Specifies the format of the outputted date string. The following characters can be used:

    d - The day of the month (from 01 to 31)
    D - A textual representation of a day (three letters)
    j - The day of the month without leading zeros (1 to 31)
    l (lowercase 'L') - A full textual representation of a day
    N - The ISO-8601 numeric representation of a day (1 for Monday, 7 for Sunday)
    S - The English ordinal suffix for the day of the month (2 characters st, nd, rd or th. Works well with j)
    w - A numeric representation of the day (0 for Sunday, 6 for Saturday)
    z - The day of the year (from 0 through 365)
    W - The ISO-8601 week number of year (weeks starting on Monday)
    F - A full textual representation of a month (January through December)
    m - A numeric representation of a month (from 01 to 12)
    M - A short textual representation of a month (three letters)
    n - A numeric representation of a month, without leading zeros (1 to 12)
    t - The number of days in the given month
    L - Whether it's a leap year (1 if it is a leap year, 0 otherwise)
    o - The ISO-8601 year number
    Y - A four digit representation of a year
    y - A two digit representation of a year
    a - Lowercase am or pm
    A - Uppercase AM or PM
    B - Swatch Internet time (000 to 999)
    g - 12-hour format of an hour (1 to 12)
    G - 24-hour format of an hour (0 to 23)
    h - 12-hour format of an hour (01 to 12)
    H - 24-hour format of an hour (00 to 23)
    i - Minutes with leading zeros (00 to 59)
    s - Seconds, with leading zeros (00 to 59)
    u - Microseconds (added in PHP 5.2.2)
    e - The timezone identifier (Examples: UTC, GMT, Atlantic/Azores)
    I (capital i) - Whether the date is in daylights savings time (1 if Daylight Savings Time, 0 otherwise)
    O - Difference to Greenwich time (GMT) in hours (Example: +0100)
    P - Difference to Greenwich time (GMT) in hours:minutes (added in PHP 5.1.3)
    T - Timezone abbreviations (Examples: EST, MDT)
    Z - Timezone offset in seconds. The offset for timezones west of UTC is negative (-43200 to 50400)
    c - The ISO-8601 date (e.g. 2013-05-05T16:34:42+00:00)
    r - The RFC 2822 formatted date (e.g. Fri, 12 Apr 2013 12:01:05 +0200)
    U - The seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)

          and the following predefined constants can also be used (available since PHP 5.1.0):

    DATE_ATOM - Atom (example: 2013-04-12T15:52:01+00:00)
    DATE_COOKIE - HTTP Cookies (example: Friday, 12-Apr-13 15:52:01 UTC)
    DATE_ISO8601 - ISO-8601 (example: 2013-04-12T15:52:01+0000)
    DATE_RFC822 - RFC 822 (example: Fri, 12 Apr 13 15:52:01 +0000)
    DATE_RFC850 - RFC 850 (example: Friday, 12-Apr-13 15:52:01 UTC)
    DATE_RFC1036 - RFC 1036 (example: Fri, 12 Apr 13 15:52:01 +0000)
    DATE_RFC1123 - RFC 1123 (example: Fri, 12 Apr 2013 15:52:01 +0000)
    DATE_RFC2822 - RFC 2822 (Fri, 12 Apr 2013 15:52:01 +0000)
    DATE_RFC3339 - Same as DATE_ATOM (since PHP 5.1.3)
    DATE_RSS - RSS (Fri, 12 Aug 2013 15:52:01 +0000)
    DATE_W3C - World Wide Web Consortium (example: 2013-04-12T15:52:01+00:00)


     //----------------------------
     The DATE type is used for values with a date part but no time part. MySQL retrieves and displays DATE
     values in 'YYYY-MM-DD' format. The supported range is '1000-01-01' to '9999-12-31'.

     The DATETIME type is used for values that contain both date and time parts. MySQL retrieves and displays DATETIME
     values in 'YYYY-MM-DD HH:MM:SS' format. The supported range is '1000-01-01 00:00:00' to '9999-12-31 23:59:59'.

     The TIMESTAMP data type is used for values that contain both date and time parts. TIMESTAMP
     has a range of '1970-01-01 00:00:01' UTC to '2038-01-19 03:14:07' UTC.

     MySQL converts TIMESTAMP values from the current time zone to UTC for storage, and back from UTC to the
     current time zone for retrieval. (This does not occur for other types such as DATETIME.) By default, the current
     time zone for each connection is the server's time. The time zone can be set on a per-connection basis. As long
     as the time zone setting remains constant, you get back the same value you store. If you store a TIMESTAMP value,
     and then change the time zone and retrieve the value, the retrieved value is different from the value you stored.
     This occurs because the same time zone was not used for conversion in both directions. The current time zone is
     available as the value of the time_zone system variable. For more information, see Section 10.6, “MySQL Server
     Time Zone Support”.

     The TIMESTAMP data type offers automatic initialization and updating to the current date and time. For
     more information, see Section 11.3.5, “Automatic Initialization and Updating for TIMESTAMP”.
     --------*/


    /**
     * Initialize All fields to their defaults based on type
     */
    private function initialize()
    {
        $tm = time();
        foreach($this->fields AS $fieldName => $fieldInfo) {
            $type = $fieldInfo['type'];
            if (!isset($this->field_types[$type])) {
                throw new ServiceException(get_class($this) . " - initialize(): Invalid Field Type: ({$type})");
            }
            $val = $this->field_types[$type];

            if ($val === '*') {
                $val = $this->_compute($fieldName, $type, $tm);
            }
            $this->$fieldName = $val;
        }
    }


    /**
     * Compute Value for specified FieldName and Type
     */
    private function _compute($fieldName, $fieldType, $tm)
    {
        $val = null;
        switch($fieldType) {
            case 'guid': {
                if (!empty($this->primaryKeys[$fieldName]) && $this->primaryKeys[$fieldName] === 'key') {
                    $val=GUID();
                }
                break;
            }
            case 'date': {
                $val = gmdate("Y-m-d",$tm);
                break;
            }
            case 'time': {
                $val = gmdate("H:i:s",$tm);
                break;
            }
            case 'datetime': {
                $val = gmdate("Y-m-d H:i:s",$tm);
                break;
            }
            case 'timestamp': {
                $val = gmdate("Y-m-d H:i:s",$tm);
                break;
            }
            default: break;
        }
        return $val;
    }
}
