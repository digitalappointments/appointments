<?php

abstract class BaseObjectServices
{
    const DEFAULT_ROWS_PER_REQUEST = 20;
    public $className;

    public function filter($filterOptions=array(), $options=array())
    {
        $baseObject = new $this->className();

        $rows = array();
        if (empty($filterOptions['order_by'])) {
            $order_by  = array();
            $order_dir = array();
        } else {
            if (is_array($filterOptions['order_by'])) {
                $order_by = $filterOptions['order_by'];
            } else {
                $order_by = array($filterOptions['order_by']);
            }
            if (empty($filterOptions['order_dir'])) {
                $order_dir  = array();
            } elseif (is_array($filterOptions['order_dir'])) {
                $order_dir = $filterOptions['order_dir'];
            } else {
                $order_dir = array($filterOptions['order_dir']);
            }
        }

        $deleted = (!empty($options['deleted']) && ($options['deleted'] === 'true'));  // default: false - do not include deleted

        $max_rows = empty($filterOptions['max_num']) ? self::DEFAULT_ROWS_PER_REQUEST : (int) $filterOptions['max_num'];

        $stmt = false;
        try {
            if (empty($filterOptions['fields']) || !is_array($filterOptions['fields'])) {
                $fieldKeys = array_keys($baseObject->fields);
            } else {
                $fieldKeys = $filterOptions['fields'];
                foreach($fieldKeys as $fieldKey) {
                    if (!isset($baseObject->fields[$fieldKey])) {
                        throw new ServiceException(get_class() . " - No such field name {$fieldKey} on Field Select List for " . $this->className);
                    }
                }
            }
            $select_fields = D::$dbm->getSelectSet($fieldKeys);
            $sql  = "SELECT {$select_fields} FROM {$baseObject->tableName}";
            if (!$deleted) {
                $sql .= " WHERE deleted = 0";
            }
            $bindTemplate = '';
            $bindParams = array();
            $sqlOrderBy = '';
            $j=0;
            foreach($order_by AS $orderByField) {
                if (!isset($baseObject->fields[$orderByField])) {
                    throw new ServiceException(get_class() . " - No such field name {$orderByField} on ORDER BY for " . $this->className);
                }
                $dir =  (empty($order_dir[$j]) || !is_string($order_dir[$j])) ? "ASC" : strtoupper($order_dir[$j]);
                if ($dir != "ASC" && $dir != "DESC") {
                    throw new ServiceException(get_class() . " - Invalid ORDER BY direction: {$dir} on " . $this->className);
                }
                if ($j==0) {
                    $sqlOrderBy = " ORDER BY ";
                } else {
                    $sqlOrderBy .= ", ";
                }
                //$bindTemplate .= 'ss';
                //$bindParams[] = $order_by;
                //$bindParams[] = $order_dir;
                $sqlOrderBy .= "{$orderByField} {$dir}";
                $j++;
            }
            $sql .= $sqlOrderBy;
            $sql .= " LIMIT ?";
            $bindTemplate .= 'i';
            $bindParams[] = $max_rows;

            $stmt = D::$dbm->prepare($sql);

            D::$dbm->bindParameters($stmt, $bindTemplate, $bindParams);
            D::$dbm->execute($stmt);
            while($row = D::$dbm->fetchResult($stmt, $fieldKeys)) {
                $account = new $this->className($row);
                $rows[] = $account->toApi($fieldKeys);
            }
            D::$dbm->closeStatement($stmt);
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
        return $rows;
    }
}
