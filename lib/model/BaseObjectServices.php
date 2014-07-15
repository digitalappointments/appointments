<?php

abstract class BaseObjectServices
{
    const DEFAULT_ROWS_PER_REQUEST = 100;
    public $className;

    public function filter($options=array())
    {
        $baseObject = new $this->className();

        $rows = array();
        $order_by  = empty($options['order_by']) ? false : $options['order_by'];
        $order_dir = empty($options['order_dir']) ? "ASC" : $options['order_dir'];
        $last_id  = empty($options['last_id']) ? '0' : $options['last_id'];
        $max_rows = empty($options['max_num']) ? self::DEFAULT_ROWS_PER_REQUEST : (int) $options['max_num'];

        $stmt = false;
        try {
            $fieldKeys = array_keys($baseObject->fields);
            $select_fields = D::$dbm->getSelectSet($fieldKeys);
            $sql  = "SELECT {$select_fields} FROM {$baseObject->tableName}";
            $bindTemplate = '';
            $bindParams = array();
            if (!empty($order_by)) {
                $sql .= " ORDER BY {$order_by} {$order_dir}";
                //$bindTemplate .= 'ss';
                //$bindParams[] = $order_by;
                //$bindParams[] = $order_dir;
            }
            $sql .= " LIMIT ?";
            $bindTemplate .= 'i';
            $bindParams[] = $max_rows;

            $stmt = D::$dbm->prepare($sql);

            D::$dbm->bindParameters($stmt, $bindTemplate, $bindParams);
            D::$dbm->execute($stmt);
            while($row = D::$dbm->fetchResult($stmt, $fieldKeys)) {
                $rows[] = new $this->className($row);
            }
            D::$dbm->closeStatement($stmt);
        } catch (DatabaseException $e) {
            if ($stmt !== false) {
                D::$dbm->closeStatement($stmt);
            }
            Log::error($e->getLogMessage());
        } catch (Exception $e) {
            if ($stmt !== false) {
                D::$dbm->closeStatement($stmt);
            }
            Log::error($e->getMessage());
        }
        return $rows;
    }
}
