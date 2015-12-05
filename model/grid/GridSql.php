<?php

class GridSql
{
    //

    public static function LIMIT($params)
    {
        $enabled = (boolean) $params['enabled'];
        $start = (int) $params['start'];
        $length = (int) $params['length'];

        if ($enabled) {
            $limit = 'LIMIT '.$start.','.$length;
        } else {
            $limit = '';
        }

        return $limit;
    }

    //

    public static function ORDERBY($params)
    {
        $enabled = (boolean) $params['enabled'];
        $fieldToOrder = (array) $params['fieldToOrder'];
        $fieldSortable = (array) $params['fieldSortable'];
        $fieldDirection = (array) $params['fieldDirection'];
        $fieldName = (array) $params['fieldName'];
        $fieldAs = (array) $params['fieldAs'];

        if ($enabled) {
            $orderby = [];
            foreach ($fieldToOrder as $i => $fieldIndex) {
                if ($fieldSortable[$i] == 'true') {
                    $f = $fieldAs[$fieldIndex];
                    if (!is_string($f)) {
                        $f = $fieldName[$fieldIndex];
                    }
                    $orderby[] = $f.' '.(strtolower($fieldDirection[$i]) === 'asc' ? 'ASC' : 'DESC');
                }
            }
            $orderby = count($orderby) > 0 ? 'ORDER BY '.implode(',', $orderby) : ' ';
        } else {
            $orderby = ' ';
        }

        return $orderby;
    }

    //

    public static function FIELDS($params)
    {
        $enabled = (boolean) $params['enabled'];
        $fieldArray = (array) $params['fieldArray'];

        if ($enabled) {
            $fields = '';
            foreach ($fieldArray as $fieldAs => $field) {
                if (is_string($fieldAs)) {
                    $fields .= $field.' AS '.$fieldAs.',';
                } else {
                    $fields .= '`'.$field.'`,';
                }
            }
            // remove last char
            $fields = ' '.rtrim($fields, ',').' ';
        } else {
            $fields = ' * ';
        }

        return $fields;
    }

    public static function WHERE($params)
    {
        $enabled = (boolean) $params['enabled'];
        $search = (string) $params['search'];
        $whereCondition = (string) $params['whereCondition'];
        $fieldName = (array) $params['fieldName'];
        $fieldAs = (array) $params['fieldAs'];
        $fieldSearchable = (array) $params['fieldSearchable'];

        $searchs = explode(' ', $search);
        ## filter by keyword search
        if ($enabled && $search != '') {
            $where = [];

            for ($i = 0;$i < count($fieldName);$i++) {
                #if (isset($_POST['bSearchable_'.$i])&&$_POST['bSearchable_'.$i]=="true") {
                if (isset($fieldSearchable[$i]) && $fieldSearchable[$i] == 'true') {
                    $where[] = $fieldName[$i]." LIKE '%".@mysql_real_escape_string($search)."%'";
                    foreach ($searchs as $searcht) {
                        $where[] = $fieldName[$i]." LIKE '%".@mysql_real_escape_string($searcht)."%' ";
                    }
                    /*
                    if (is_string($field)) {
                        $where[] = $field." LIKE '%".@mysql_real_escape_string( $search )."%'";
                        
                        foreach ($searchs as $searcht){
                            $where[] = $field." LIKE '%".@mysql_real_escape_string( $searcht )."%' ";
                        }
                    } else {
                        $where[] = $field." LIKE '%".@mysql_real_escape_string( $search )."%'";
                        
                        foreach ($searchs as $searcht){
                            $where[] = $field." LIKE '%".@mysql_real_escape_string( $searcht )."%' ";
                        }
                    }*/
                }
            }

            $where = count($where) > 0 ? 'WHERE ('.implode(' OR ', $where).') ' : '';

            if (!empty($whereCondition)) {
                if (empty($where)) {
                    $where = ' WHERE '.$whereCondition;
                } else {
                    $where .= ' AND '.$whereCondition;
                }
            }
        } else {
            if (!empty($whereCondition)) {
                $where = ' WHERE '.$whereCondition;
            } else {
                $where = '';
            }
        }

        return $where;
    }
}
