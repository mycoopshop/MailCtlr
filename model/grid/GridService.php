<?php

##
class GridService
{
    ##

    public static function getGridServiceResponse($tbl, $filter, $class = null)
    {
        global $db;

        $fld = isset($filter['fields']) ? array_values($filter['fields']) : ['id'];
        $fldAs = array_keys($fld);

        #var_dump($fld);

        // limit results
        $limit = GridSql::LIMIT([
            'enabled'      => isset($_POST['iDisplayStart']),
            'start'        => $_POST['iDisplayStart'],
            'length'       => $_POST['iDisplayLength'],
        ]);

        // sort by results
        $fieldToOrder = [];
        $fieldSortable = [];
        $fieldDirection = [];
        for ($i = 0;$i < (int) $_POST['iSortingCols'];$i++) {
            $fieldIndex = $_POST['iSortCol_'.$i];
            $fieldSortable[$i] = $_POST['bSortable_'.$fieldIndex];
            $fieldDirection[$i] = $_POST['sSortDir_'.$i];
            $fieldToOrder[$i] = $fieldIndex;
        }
        $orderby = GridSql::ORDERBY([
            'enabled'          => isset($_POST['iSortCol_0']),
            'fieldToOrder'     => $fieldToOrder,
            'fieldSortable'    => $fieldSortable,
            'fieldDirection'   => $fieldDirection,
            'fieldName'        => $fld,
            'fieldAs'          => $fldAs,
        ]);

        // fields in select
        $fields = GridSql::FIELDS([
            'enabled'        => isset($filter['fields']),
            'fieldArray'     => $filter['fields'],
        ]);

        // where cond
        for ($i = 0;$i < count($filter['fields']);$i++) {
            $fieldSearchable[$i] = @$_POST['bSearchable_'.$i];
        }
        $where = GridSql::WHERE([
            'enabled'              => isset($_POST['sSearch']),
            'search'               => @$_POST['sSearch'],
            'fieldName'            => $fld,
            'fieldAs'              => $fldAs,
            'whereCondition'       => @$filter['where'],
            'fieldSearchable'      => $fieldSearchable,
        ]);

        // the queries
        $sql_fetch = "SELECT {$fields} FROM {$tbl} {$where} {$orderby} {$limit}";

        //return self::handleGridServiceResponse($tbl, $where, $sql_fetch, $class);
        //$dbx = Context::getInstance()->getDB();
        $dbx = $db;

        ## prepare response $res
        $res = new stdClass();
        $res->sEcho = $_POST['sEcho'];

        $sql_count = "SELECT COUNT(Id) FROM {$tbl} {$where}";
        $sql_total = "SELECT COUNT(Id) FROM {$tbl}";

        ## fetch rows
        $res->SQL = $sql_fetch;
        $dat = $dbx->get_results($sql_fetch);

        ## apply filter to encode special chars
        if ($class && $dat) {
            foreach ($dat as &$row) {
                foreach ($row as &$value) {
                    $value = mb_convert_encoding($value, 'UTF-8');
                }
            }
        }

        ## apply Set/Get filter to parse and prepare data for visualization
        if ($class && $dat) {
            foreach ($dat as &$row) {
                //DBUtils::applyGetFilter($row,$class);
            }
        }

        ## apply html template or functions for fields
        if ($dat) {
            foreach ($dat as &$row) {
                foreach ($row as $k => &$v) {
                    $v = self::applyTemplate($v, $k, $filter['templates'][$k], $row);
                    $v = self::applyFunction($v, $k, $filter['functions'][$k], $row);
                }
            }
        }

        ## prepare aaData with $dat
        $res->aaData = [];
        if ($dat) {
            foreach ($dat as $row0) {
                $res->aaData[] = array_values((array) $row0);
            }
        }

        ## manage total
        $res->iTotalRecords = (int) $dbx->get_var($sql_total);
        $res->iTotalDisplayRecords = (int) $dbx->get_var($sql_count);

        ## return response
        return $res;
    }

    ##

    private static function applyTemplate($value, $key, $template, $row)
    {
        if (!$template) {
            return $value;
        }

        return str_replace(['{'.$key.'}', '{?}'], [$value, $value], $template);
    }

    ##

    private static function applyFunction($value, $key, $function, $row)
    {
        if ($function && is_callable($function, true)) {
            return call_user_func_array($function, [$value, $row]);
        } else {
            return $value;
        }
    }
}
