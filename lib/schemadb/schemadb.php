<?php
/*\
 * 
 * Copyright (c) 2014 Bianco Francesco
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files "schemadb", to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
\*/

## constants
define('SCHEMADB_VERSION', '0.9.96');
define('SCHEMADB_DEBUG', false);

## schemadb mysql constants for rapid fields creation
define('MYSQL_PRIMARY_KEY', '%|key:primary_key|%');
define('MYSQL_DATE', '0000-00-00');
define('MYSQL_TIME', '00:00:00');
define('MYSQL_DATETIME', '0000-00-00 00:00:00');
define('MYSQL_TEXT', '%|type:text|%');
define('MYSQL_VARCHAR', '%|type:varchar(255)|%');
define('MYSQL_VARCHAR_80', '%|type:varchar(80)|%');
define('MYSQL_VARCHAR_255', '%|type:varchar(255)|%');
define('MYSQL_INT', '%|type:int(10)|%');
define('MYSQL_INT_10', '%|type:int(10)|%');
define('MYSQL_INT_14', '%|type:int(14)|%');
define('MYSQL_FLOAT', '%|type:float(14,4)|%');
define('MYSQL_FLOAT_14_4', '%|type:float(14,4)|%');

## usefull mysql func
function MYSQL_NOW()
{
    return @date('Y-m-d H:i:s');
}

## main class as namespace container
class schemadb
{
    ##
    private static $db = null;

    ##
    private static $default = [
        'COLUMN_ATTRIBUTE' => [
            'Type'         => 'int(10)',
            'Null'         => 'YES',
            'Key'          => '',
            'Default'      => '',
            'Extra'        => '',
        ],
    ];

    ## init database connection

    public static function &connect($host, $username, $password, $database, $prefix)
    {

        ##
        self::$db = new schemadb_ezSQL_mysql($username, $password, $database, $host);
        self::$db->prefix = $prefix;

        ##
        return self::$db;
    }

    ##

    public static function execute($method, $sql = null)
    {

        ## assert the db connection
        if (self::$db == null) {
            die('schemadb connection not found');
        }

        ## debug the queries
        if (SCHEMADB_DEBUG) {
            echo '<pre style="border:1px solid #9F6000;margin:0 0 1px 0;padding:2px;color:#9F6000;background:#FEEFB3;">';
            echo '<strong>'.str_pad($method, 10, ' ', STR_PAD_LEFT).'</strong>: '.$sql.'</pre>';
        }

        ## select appropriate method
        switch ($method) {
            case 'prefix':    return self::$db->prefix;
            case 'last_id':    return self::$db->insert_id;
            case 'query':    $return = self::$db->query($sql); break;
            case 'row':        $return = self::$db->get_row($sql, ARRAY_A); break;
            case 'results':    $return = self::$db->get_results($sql, ARRAY_A); break;
            default:        $return = self::$db->get_results($sql, ARRAY_A); break;
        }

        ## assert sql error
        $error = mysql_error();
        if ($error) {
            die('Error: '.$error.'<br/>Query: '.$sql);
        }

        ## return data
        return $return;
    }

    ## apply schema on the db

    public static function apply($schema)
    {
        return self::update($schema);
    }

    ## update db via schema

    public static function update($schema)
    {

        ## retrive queries
        $q = self::diff($schema);

        ## execute queries
        if (count($q) > 0) {
            foreach ($q as $s) {
                self::execute('query', $s);
            }
        }

        ## return queries
        return $q;
    }

    ## update table via schema

    public static function update_table($table, $schema)
    {

        ## retrive queries
        $q = self::diff_table($table, $schema);

        ## execute queries
        if (count($q) > 0) {
            foreach ($q as $s) {
                self::execute('query', $s);
            }
        }

        ## return queries
        return $q;
    }

    ## generate query to align db

    public static function diff($schema, $parse = true)
    {

        ## prepare
        $s = $parse ? self::schema_parse($schema) : $schema;
        $p = self::execute('prefix');
        $o = [];

        ## loop throu the schema
        foreach ($s as $t => $d) {
            $q = self::diff_table($p.$t, $d, false);
            if (count($q) > 0) {
                $o = array_merge($o, $q);
            }
        }

        ## return estimated sql query
        return $o;
    }

    ## generate query to align table

    public static function diff_table($table, $schema, $parse = true)
    {

        ##
        $s = $parse ? self::schema_parse_table($schema) : $schema;

        ##
        $o = [];
        $z = [];

        ## test if table exists
        $e = self::execute('row', "SHOW TABLES LIKE '{$table}'");

        ##
        if (!$e) {
            $o[] = self::create_table($table, $s);

            return $o;
        }

        ## current table description
        $a = self::desc_table($table);

        ## alter primary key flag
        $b = false;
        $i = false;

        ## test field definition
        foreach ($s as $f => $d) {
            if (SCHEMADB_DEBUG) {
                //echo '==== '.$table.'::'.$f.' ===='."\n<br/>";
            }

            ## check if column exists in current db
            if (isset($a[$f])) {

                ## update flag
                $u = false;

                ## loop throd current column property
                foreach ($a[$f] as $k => $v) {

                    ## if have a difference
                    if ($d[$k] != $v) {
                        $u = true;
                        if ($k == 'Key' && $v == 'PRI') {
                            $b = true;
                        }
                    }
                }

                ## update column
                if ($u) {
                    if ($d['Key'] == 'PRI') {
                        $i = true;
                        array_unshift($z, self::alter_table_change($table, $f, $d));
                        //$z[] = schemadb::alter_table_change($table,$f,$d);
                    } else {
                        //array_unshift($o, schemadb::alter_table_change($table,$f,$d));
                        $o[] = self::alter_table_change($table, $f, $d);
                    }
                }
            } else {
                ## add column
                if ($d['Key'] == 'PRI') {
                    $i = true;
                    array_unshift($z, self::alter_table_add($table, $f, $d));
                    //$z[] = schemadb::alter_table_add($table,$f,$d);
                } else {
                    //array_unshift($o, schemadb::alter_table_add($table,$f,$d));
                    $o[] = self::alter_table_add($table, $f, $d);
                }
            }
        }

        //echo '---'.$i.':'.$b.'---\n<br/>';
        if ($i && $b || !$i && $b) {
            array_unshift($z, self::alter_table_drop_primary_key($table));
        }

        return array_merge($z, $o);
    }

    ##

    public static function desc()
    {
        $p = self::execute('prefix');
        $l = self::execute('resutls', "SHOW TABLES LIKE '{$p}%'");
        $r = [];

        if (count($l) > 0) {
            foreach ($l as $t) {
                $t = reset($t);
                $r[$t] = self::desc_table($t);
            }
        }

        return $r;
    }

    ## describe table

    public static function desc_table($table)
    {

        ##
        $i = self::execute('results', "DESC {$table}");
        $a = [];
        $n = 0;
        $b = false;

        ##
        foreach ($i as $j) {
            $j['Before'] = $b;
            $j['First'] = $n == 0;
            $a[$j['Field']] = $j;
            $b = $j['Field'];
            $n++;
        }

        ##
        return $a;
    }

    ##

    private static function column_definition($d, $o = true)
    {

        ##
        $t = isset($d['Type']) ? $d['Type'] : self::$default['COLUMN_ATTRIBUTE']['Type'];
        $u = isset($d['Null']) && ($d['Null'] == 'NO' || !$d['Null']) ? 'NOT NULL' : 'NULL';
        $l = isset($d['Default']) && $d['Default'] ? "DEFAULT '$d[Default]'" : '';
        $e = isset($d['Extra']) ? $d['Extra'] : '';
        $p = isset($d['Key']) && $d['Key'] == 'PRI' ? 'PRIMARY KEY' : '';
        $q = "{$t} {$u} {$l} {$e} {$p}";

        ##
        if ($o) {
            $f = isset($d['First']) && $d['First'] ? 'FIRST' : '';
            $b = isset($d['Before']) && $d['Before'] ? 'AFTER '.$d['Before'] : '';
            $q .= " {$f} {$b}";
        }

        ##
        return $q;
    }

    ## retrieve sql to create a table

    private static function create_table($t, $s)
    {

        ##
        $e = [];

        ## loop throut schema
        foreach ($s as $f => $d) {
            if (is_numeric($f) && is_string($d)) {
                $f = $d;
                $d = [];
            }
            $e[] = $f.' '.self::column_definition($d, false);
        }

        ## implode
        $e = implode(',', $e);

        ## template sql to create table
        $q = "CREATE TABLE {$t} ({$e})";

        ## return the sql
        return $q;
    }

    ##

    private static function alter_table_add($t, $f, $d)
    {

        ##
        $c = self::column_definition($d);

        ##
        $q = "ALTER TABLE {$t} ADD {$f} {$c}";

        ##
        return $q;
    }

    ## retrieve sql to alter table definition

    private static function alter_table_change($t, $f, $d)
    {

        ##
        $c = self::column_definition($d);

        ##
        $q = "ALTER TABLE {$t} CHANGE {$f} {$f} {$c}";

        ##
        return $q;
    }

    ## retrive query to remove primary key

    private static function alter_table_drop_primary_key($t)
    {
        $q = "ALTER TABLE {$t} DROP PRIMARY KEY";

        return $q;
    }

    ## parse a multi-table schema to sanitize end explod implicit info

    public static function schema_parse($schema)
    {
        $s = [];

        foreach ($schema as $t => $f) {
            $s[$t] = self::schema_parse_table($f);
        }

        return $s;
    }

    ## parse table schema to sanitize end explod implicit info

    public static function schema_parse_table($schema)
    {
        $s = [];
        $b = false;

        foreach ($schema as $f => $d) {
            $s[$f] = self::schema_parse_table_column($d, $f, $b);
            $b = $f;
        }

        return $s;
    }

    ## build mysql column attribute set

    public static function schema_parse_table_column($value, $field = false, $before_field = false)
    {

        ## default schema of a column
        $d = [
            'Field'        => $field,
            'Type'         => self::$default['COLUMN_ATTRIBUTE']['Type'],
            'Null'         => self::$default['COLUMN_ATTRIBUTE']['Null'],
            'Key'          => self::$default['COLUMN_ATTRIBUTE']['Key'],
            'Default'      => self::$default['COLUMN_ATTRIBUTE']['Default'],
            'Extra'        => self::$default['COLUMN_ATTRIBUTE']['Extra'],
            'Before'       => $before_field,
            'First'        => !$before_field,
        ];

        ##
        $t = self::get_type($value);

        ##
        switch ($t) {

            case 'date':
                $d['Type'] = 'date';
                break;

            case 'datetime':
                $d['Type'] = 'datetime';
                break;

            case 'primary_key':
                $d['Type'] = 'int(10)';
                $d['Null'] = 'NO';
                $d['Key'] = 'PRI';
                $d['Extra'] = 'auto_increment';
                break;

            case 'string':
                $d['Type'] = 'varchar(255)';
                break;

            case 'text':
                $d['Type'] = 'text';
                break;

            case 'boolean':
                $d['Type'] = 'tinyint(1)';
                $d['Default'] = (int) $value;
                $d['Null'] = 'NO';
                break;

            case 'int':
                $d['Type'] = 'int(10)';
                $d['Default'] = (int) $value;
                $d['Null'] = 'NO';
                break;

            case 'float':
                $d['Type'] = 'float(12,2)';
                $d['Default'] = (int) $value;
                $d['Null'] = 'NO';
                break;

            case 'array':
                $d['Default'] = $value[0];
                $d['Null'] = in_array(null, $value) ? ' YES' : 'NO';
                $t = [];
                foreach ($value as $i) {
                    if ($i !== null) {
                        $t[] = "'".$i."'";
                    }
                }
                $d['Type'] = 'enum('.implode(',', $t).')';
                break;
        }

        return $d;
    }

    ##

    public static function get_class($value)
    {
        if (preg_match('/^<<([_a-zA-Z][_a-zA-Z0-9]*)>>$/i', $value, $d)) {
            return $d[1];
        } else {
            return false;
        }
    }

    ##

    public static function get_type($value)
    {

        ##
        $t = gettype($value);

        ##
        switch ($t) {

            ##
            case 'string':
                if (preg_match('/^\%\|([a-z]+):(.*)\|\%$/i', $value, $d)) {
                    switch ($d[1]) {
                        case 'key': return $d[2];
                        case 'type': return $d[2];
                        case 'schema': return 'schema';
                    }
                } elseif (self::get_class($value)) {
                    return 'class';
                } elseif (preg_match('/[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9] [0-9][0-9]:[0-9][0-9]:[0-9][0-9]/', $value)) {
                    return 'datetime';
                } elseif (preg_match('/[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]/', $value)) {
                    return 'date';
                } else {
                    return 'string';
                }

            ##
            case 'NULL':
                return 'string';

            ##
            case 'boolean':
                return 'boolean';

            ##
            case 'integer':
                return 'int';

            ##
            case 'double':
                return 'float';

            case 'array':
                if ($value && $value == array_values($value)) {
                    return 'array';
                } else {
                    return 'column';
                }
        }
    }

    ##

    public static function get_value($notation)
    {

        ##
        $t = self::get_type($notation);

        ##
        switch ($t) {

            ##
            case 'int':
                return (int) $notation;

            ##
            case 'boolean':
                return (boolean) $notation;

            ##
            case 'primary_key':
                return null;

            ##
            case 'string':
                return (string) $notation;

            ##
            case 'text':
                return (string) $notation;

            ##
            case 'float':
                return (float) $notation;

            ##
            case 'class':
                return null;

            ##
            case 'array':
                return null;

            ##
            case 'date':
                return self::parse_date($notation);

            ##
            case 'datetime':
                return self::parse_datetime($notation);

            ##
            case 'column':
                return null;

            ##
            default:
                trigger_error("No PSEUDOTYPE value for '{$t}' => '{$notation}'", E_USER_ERROR);
        }
    }

    ## handle creation of related object

    public static function object_build($d, $a, &$r)
    {

        ##
        $t = self::get_type($d);

        ##
        switch ($t) {
            case 'class':
                $c = self::get_class($d);
                $o = new $c();
                $o->fill($a);
                $o->store();
                $k = $o::primary_key();
                $r = $o->{$k};
                break;
        }
    }

    ## printout database status/info

    public static function parse_date($date)
    {

        ##
        if ($date != '0000-00-00') {
            return @date('Y-m-d', @strtotime(''.$date));
        } else {
            return;
        }
    }

    ## printout database status/info

    public static function parse_datetime($datetime)
    {
        if ($datetime != '0000-00-00 00:00:00') {
            return @date('Y-m-d H:i:s', @strtotime(''.$datetime));
        } else {
            return;
        }
    }

    ##

    public static function escape($value)
    {
        return mysql_real_escape_string(stripslashes($value));
    }

    ## printout database status/info

    public static function info()
    {

        ## describe databse
        $s = static::desc();

        ## printout
        echo '<pre>';
        var_dump($s);
        echo '</pre>';
    }

    ## printout database status/info

    public static function dump()
    {

        ## describe databse
        $a = static::all();

        ##
        if (count($a) > 0) {
            echo '<table><tr>';
            foreach (array_keys($a[0]) as $k) {
                echo '<th>'.$k.'</th>';
            }
            echo '</tr>';
            foreach ($a as $r) {
                echo '<tr>';
                foreach ($r as $v) {
                    echo '<td>'.$v.'</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo get_called_class().': is empty';
        }
    }
}

## static part of sdbClass
class schedadb_sdbClass_static
{
    ## bundle to collect info and stored cache
    public static $kache = [];

    ## reserved attributes for schemadb
    protected static $proto = [
        'proto',
        'class',
        'table',
        'kache',
    ];

    ## retrieve table name

    public static function table()
    {

        ## get prefix
        $p = schemadb::execute('prefix');

        ## check various possible table-name definition
        if (isset(static::$table)) {
            $t = static::$table;
        } elseif (isset(static::$class)) {
            $t = static::$class;
        } else {
            $t = get_called_class();
        }

        ## return complete table name
        return $p.$t;
    }

    ## retrieve static class name

    public static function klass()
    {

        ##
        if (isset(static::$class)) {
            $c = static::$class;
        } else {
            $c = get_called_class();
        }

        ##
        return $c;
    }

    ## load element by primary key

    public static function load($id)
    {

        ##
        $t = static::table();
        $k = static::primary_key();
        $s = "SELECT * FROM {$t} WHERE {$k}='{$id}' LIMIT 1";
        $r = schemadb::execute('row', $s);
        $o = static::build($r);

        ##
        return $o;
    }

    ##

    public static function build($data = null)
    {

        ##
        $o = new static();

        ##
        if ($data) {
            $o->fill($data);
        }

        ##
        return $o;
    }

    ##

    public static function all()
    {

        ##
        static::schemadb_update();

        ##
        $t = static::table();
        $s = "SELECT * FROM {$t}";
        $r = schemadb::execute('results', $s);

        ##
        $a = [];

        ##
        foreach ($r as $i => $o) {
            $a[$i] = static::build($o);
        }

        ##
        return $a;
    }

    ##

    public static function query($query)
    {

        ##
        $t = self::table();

        ## where block for the query
        $w = [];

        ##
        if (isset($query['where'])) {
            $w[] = $query['where'];
        }

        ##
        if (isset($query['limit'])) {
            $l = 'LIMIT '.$query['limit'];
            unset($query['limit']);
        } else {
            $l = '';
        }

        ##
        foreach ($query as $k => $v) {
            if ($k != 'sort' && $k != 'where') {
                $w[] = "{$k}='$v'";
            }
        }

        ##
        $w = count($w) > 0 ? 'WHERE '.implode(' AND ', $w) : '';

        ## order by block
        $o = isset($query['sort']) ? 'ORDER BY '.$query['sort'] : '';

        ## build query
        $q = "SELECT * FROM {$t} {$w} {$o} {$l}";

        ## fetch res
        $r = schemadb::execute('results', $q);

        ##
        $a = [];

        ##
        foreach ($r as $i => $o) {
            $a[$i] = self::build($o);
        }

        ##
        return $a;
    }

    ##

    public static function first()
    {

        ##
        $t = static::table();

        ##
        $s = "SELECT * FROM {$t} LIMIT 1";

        ##
        $r = schemadb::execute('row', $s);

        ##
        if ($r) {
            return static::build($r);
        }
    }

    ## alias 6char of ping

    public static function exists($query)
    {
        return static::ping($query);
    }

    ##

    public static function ping($query)
    {

        ##
        static::schemadb_update();

        ##
        $t = self::table();
        $w = [];

        ##
        if (isset($query['where'])) {
            $w[] = $query['where'];
            unset($query['where']);
        }

        ##
        foreach (static::skema() as $f => $d) {
            if (isset($query[$f])) {
                $v = $query[$f];
                $w[] = "{$f}='$v'";
            }
        }

        ##
        $w = count($w) > 0 ? 'WHERE '.implode(' AND ', $w) : '';

        ##
        $s = "SELECT * FROM {$t} {$w} LIMIT 1";

        ##
        $r = schemadb::execute('row', $s);

        ##
        if ($r) {
            return self::build($r);
        }
    }

    ##

    public static function submit($query)
    {

        ##
        $o = static::ping($query);

        ##
        if (!$o) {
            $o = static::build($query);
            $o->store();
        }

        ##
        return $o;
    }

    ##

    public static function insert($query)
    {

        ##
        $o = static::build($query);
        $o->store_insert();

        ##
        return $o;
    }

    ##

    public static function update($query)
    {

        ##
        $o = static::build($query);
        $o->store_update();

        ##
        return $o;
    }

    ##

    public static function import($records)
    {

        ##
        foreach ($records as $record) {

            ##
            static::insert($record);
        }
    }

    ##

    public static function encode($data)
    {

        ##
        $c = get_called_class();

        ##
        foreach ($data as $f => $v) {
            $m = 'encode_'.$f;
            if (method_exists($c, $m)) {
                if (is_object($data)) {
                    $data->{$f} = call_user_func($c.'::'.$m, $v);
                } else {
                    $data[$f] = call_user_func($c.'::'.$m, $v);
                }
            }
        }

        ##
        return $data;
    }

    ##

    public static function decode($data)
    {

        ##
        $c = get_called_class();

        ##
        foreach ($data as $f => $v) {
            $m = 'decode_'.$f;
            if (method_exists($c, $m)) {
                if (is_object($data)) {
                    $data->{$f} = call_user_func($c.'::'.$m, $v);
                } else {
                    $data[$f] = call_user_func($c.'::'.$m, $v);
                }
            }
        }

        ##
        return $data;
    }

    ##

    public static function map($data, $map)
    {

        ##
        $o = static::build($data);

        ##
        foreach ($map as $m => $f) {
            $o->{$f} = $data[$m];
        }

        ##
        return $o;
    }

    ##

    public static function dump()
    {
        $a = static::all();
        echo '<table border=1>';
        foreach ($a as $i => $r) {
            if ($i == 0) {
                echo '<tr>';
                foreach ($r as $f => $v) {
                    echo '<th>'.$f.'</th>';
                }
                echo '</tr>';
            }
            echo '<tr>';
            foreach ($r as $f => $v) {
                echo '<td>'.$v.'</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    }

    ## delete element by primary key

    public static function delete($query)
    {

        ##
        $t = static::table();

        ##
        if (is_array($query)) {

            ## where block for the query
            $w = [];

            ##
            if (isset($query['where'])) {
                $w[] = $query['where'];
            }

            ##
            foreach ($query as $k => $v) {
                if ($k != 'sort' && $k != 'where') {
                    $w[] = "{$k}='$v'";
                }
            }

            ##
            $w = count($w) > 0 ? 'WHERE '.implode(' AND ', $w) : '';

            ##
            $s = "DELETE FROM {$t} {$w}";

            ## execute query
            schemadb::execute('query', $s);
        } elseif ($query > 0) {

            ## prepare sql query
            $k = static::primary_key();
            $s = "DELETE FROM {$t} WHERE {$k}='{$query}'";

            ## execute query
            schemadb::execute('query', $s);
        }
    }

    ## drop table

    public static function drop()
    {

        ## prepare sql query
        $t = static::table();
        $s = "DROP TABLE IF EXISTS {$t}";
        $c = static::klass();

        ## clear cached
        unset(static::$kache[$c]['updated']);

        ## execute query
        schemadb::execute('query', $s);
    }

    ## instrospect and retrieve element schema

    public static function skema()
    {

        ##
        $c = static::klass();
        $f = get_class_vars($c);
        $s = [];

        ##
        foreach ($f as $k => $v) {
            if (!in_array($k, self::$proto)) {
                $s[$k] = $v;
            }
        }

        ##
        return $s;
    }

    ## update db table based on class schema

    public static function schemadb_update()
    {

        ## get class name
        $c = static::klass();

        ## avoid re-update by check the cache
        if (isset(static::$kache[$c]['updated'])) {
            return;
        }

        ## get table and model schema
        $t = static::table();
        $s = static::skema();

        ## have a valid schema update db table
        if (count($s) > 0) {
            schemadb::update_table($t, $s);
        }

        ## cache last update avoid multiple call
        static::$kache[$c]['updated'] = time();

        ## debug output
        if (SCHEMADB_DEBUG) {
            echo '<pre style="border:1px solid #9F6000;margin:0 0 1px 0;padding:2px;color:#9F6000;background:#FEEFB3;">';
            echo '<strong>'.str_pad('update', 10, ' ', STR_PAD_LEFT).'</strong>: '.$c.'</pre>';
        }
    }

    ##

    public static function connect($conn = null)
    {

        ##
        static::schemadb_update();
    }
}

## self methods of sdbClass
class schemadb_sdbClass extends schedadb_sdbClass_static
{
    ## constructor

    public function __construct()
    {

        ## update database schema
        static::schemadb_update();

        ## prepare field values strip schema definitions
        foreach ($this->fields() as $f) {
            $this->{$f} = schemadb::get_value($this->{$f});
        }
    }

    ## assign value and store object

    public function assign($query)
    {
        foreach ($query as $k => $v) {
            $this->{$k} = $v;
        }
        $this->store();
    }

    ## auto-store element method

    public function store()
    {

        ## retrieve primary key
        $k = static::primary_key();

        ## based on primary key store action
        if ($k && $this->{$k} > 0) {
            return $this->store_update();
        } else {
            return $this->store_insert();
        }
    }

    ## fill field with set parser value from array

    public function fetch($array)
    {

        ##
        foreach ($this->fields() as $f) {
            $this->set($f, $array[$f]);
        }

        ##
        $k = $this->primary_key();

        ##
        if ($k) {
            $this->{$k} = isset($array[$k]) ? (int) $array[$k] : (int) $this->{$k};
        }
    }

    ##

    public function fill($array)
    {
        foreach ($this->fields() as $f) {
            if (isset($array[$f])) {
                $this->{$f} = $array[$f];
            }
        }

        $k = $this->primary_key();

        if ($k) {
            $this->{$k} = isset($array[$k]) ? (int) $array[$k] : (int) $this->{$k};
        }
    }

    ##

    public function store_update()
    {

        ## update database schema
        static::schemadb_update();

        ##
        $k = static::primary_key();
        $s = [];

        ##
        foreach ($this->fields() as $f) {
            if ($f != $k) {
                ## prova
                $v = $this->{$f};
                $t = gettype($v);
                if ($t == 'double') {
                    $v = number_format($v, 2, '.', '');
                }
                $v = schemadb::escape($v);
                $s[] = $f." = '".$v."'";
            }
        }
        $s = implode(',', $s);

        ##
        $t = $this->table();
        $i = $this->{$k};
        $q = "UPDATE {$t} SET {$s} WHERE {$k}='{$i}'";

        ##
        schemadb::execute('query', $q);

        ##
        if ($k) {
            return $this->{$k};
        } else {
            return true;
        }
    }

    ##

    public function store_insert($force = false)
    {

        ##
        static::schemadb_update();

        ##
        $c = [];
        $v = [];
        $k = static::primary_key();

        ##
        foreach (static::skema() as $f => $d) {

            ##
            if ($f == $k && !$force) {
                continue;
            }

            ##
            $a = $this->{$f};
            $t = gettype($a);

            ##
            switch ($t) {

                ##
                case 'double':
                    $a = number_format($a, 2, '.', '');
                    break;

                ##
                case 'array':
                    schemadb::object_build($d, $a, $r);
                    $a = $r;
                    break;

            }

            ##
            $a = schemadb::escape($a);

            ##
            $c[] = $f;
            $v[] = "'".$a."'";
        }

        ##
        $c = implode(',', $c);
        $v = implode(',', $v);

        ##
        $t = static::table();
        $q = "INSERT INTO {$t} ({$c}) VALUES ({$v})";

        ##
        schemadb::execute('query', $q);

        ##
        if ($k) {
            $i = schemadb::execute('last_id');
            $this->{$k} = $i;

            return $i;
        } else {
            return true;
        }
    }

    ## return fields names

    public function fields()
    {

        ##
        $c = get_class($this);
        $f = get_class_vars($c);
        $a = [];

        ##
        foreach ($f as $k => $v) {
            if (!in_array($k, static::$proto)) {
                $a[] = $k;
            }
        }

        ##
        return $a;
    }

    ##

    public static function primary_key()
    {
        $s = static::skema();
        foreach ($s as $k => $v) {
            if ($v === MYSQL_PRIMARY_KEY) {
                return $k;
            }
        }

        return false;
    }

    ##

    public function get($field)
    {

        ##
        $m = 'get_parser_'.$field;

        ##
        if (method_exists($this, $m)) {
            return $this->{$m}($this->{$field});
        } else {
            return $this->{$field};
        }
    }

    ##

    public function set($field, $value)
    {

        ##
        $m = 'set_parser_'.$field;

        ##
        if (method_exists($this, $m)) {
            $this->{$field} = $this->{$m}($value);
        } else {
            $this->{$field} = $value;
        }
    }
}

## canonical name
class sdbClass extends schemadb_sdbClass
{
    ## only for flatman
}

## ezsql library embedded
/**********************************************************************
*  Author: Justin Vincent (jv@vip.ie)
*  Web...: http://justinvincent.com
*  Name..: ezSQL
*  Desc..: ezSQL Core module - database abstraction library to make
*          it very easy to deal with databases. ezSQLcore can not be used by
*          itself (it is designed for use by database specific modules).
*
*/

/**********************************************************************
*  ezSQL Constants
*/

define('EZSQL_VERSION', '2.17');
define('OBJECT', 'OBJECT', true);
define('ARRAY_A', 'ARRAY_A', true);
define('ARRAY_N', 'ARRAY_N', true);

/**********************************************************************
*  Core class containg common functions to manipulate query result
*  sets once returned
*/

class schemadb_ezSQLcore
{
    public $trace = false;  // same as $debug_all
    public $debug_all = false;  // same as $trace
    public $debug_called = false;
    public $vardump_called = false;
    public $show_errors = true;
    public $num_queries = 0;
    public $last_query = null;
    public $last_error = null;
    public $col_info = null;
    public $captured_errors = [];
    public $cache_dir = false;
    public $cache_queries = false;
    public $cache_inserts = false;
    public $use_disk_cache = false;
    public $cache_timeout = 24; // hours
    public $timers = [];
    public $total_query_time = 0;
    public $db_connect_time = 0;
    public $trace_log = [];
    public $use_trace_log = false;
    public $sql_log_file = false;
    public $do_profile = false;
    public $profile_times = [];

    // added to integrate schemadb
    public $prefix = '';

    // == TJH == default now needed for echo of debug function
    public $debug_echo_is_on = true;

    /**********************************************************************
    *  Constructor
    */

    public function ezSQLcore()
    {
    }

    /**********************************************************************
    *  Get host and port from an "host:port" notation.
    *  Returns array of host and port. If port is omitted, returns $default
    */

    public function get_host_port($host, $default = false)
    {
        $port = $default;
        if (false !== strpos($host, ':')) {
            list($host, $port) = explode(':', $host);
            $port = (int) $port;
        }

        return [$host, $port];
    }

    /**********************************************************************
    *  Print SQL/DB error - over-ridden by specific DB class
    */

    public function register_error($err_str)
    {
        // Keep track of last error
        $this->last_error = $err_str;

        // Capture all errors to an error array no matter what happens
        $this->captured_errors[] = 
        [
            'error_str' => $err_str,
            'query'     => $this->last_query,
        ];
    }

    /**********************************************************************
    *  Turn error handling on or off..
    */

    public function show_errors()
    {
        $this->show_errors = true;
    }

    public function hide_errors()
    {
        $this->show_errors = false;
    }

    /**********************************************************************
    *  Kill cached query results
    */

    public function flush()
    {
        // Get rid of these
        $this->last_result = null;
        $this->col_info = null;
        $this->last_query = null;
        $this->from_disk_cache = false;
    }

    /**********************************************************************
    *  Get one variable from the DB - see docs for more detail
    */

    public function get_var($query = null, $x = 0, $y = 0)
    {

        // Log how the function was called
        $this->func_call = "\$db->get_var(\"$query\",$x,$y)";

        // If there is a query then perform it if not then use cached results..
        if ($query) {
            $this->query($query);
        }

        // Extract var out of cached results based x,y vals
        if ($this->last_result[$y]) {
            $values = array_values(get_object_vars($this->last_result[$y]));
        }

        // If there is a value return it else return null
        return (isset($values[$x]) && $values[$x] !== '') ? $values[$x] : null;
    }

    /**********************************************************************
    *  Get one row from the DB - see docs for more detail
    */

    public function get_row($query = null, $output = OBJECT, $y = 0)
    {

        // Log how the function was called
        $this->func_call = "\$db->get_row(\"$query\",$output,$y)";

        // If there is a query then perform it if not then use cached results..
        if ($query) {
            $this->query($query);
        }

        // If the output is an object then return object using the row offset..
        if ($output == OBJECT) {
            return $this->last_result[$y] ? $this->last_result[$y] : null;
        }
        // If the output is an associative array then return row as such..
        elseif ($output == ARRAY_A) {
            return $this->last_result[$y] ? get_object_vars($this->last_result[$y]) : null;
        }
        // If the output is an numerical array then return row as such..
        elseif ($output == ARRAY_N) {
            return $this->last_result[$y] ? array_values(get_object_vars($this->last_result[$y])) : null;
        }
        // If invalid output type was specified..
        else {
            $this->show_errors ? trigger_error(' $db->get_row(string query, output type, int offset) -- Output type must be one of: OBJECT, ARRAY_A, ARRAY_N', E_USER_WARNING) : null;
        }
    }

    /**********************************************************************
    *  Function to get 1 column from the cached result set based in X index
    *  see docs for usage and info
    */

    public function get_col($query = null, $x = 0)
    {
        $new_array = [];

        // If there is a query then perform it if not then use cached results..
        if ($query) {
            $this->query($query);
        }

        // Extract the column values
        for ($i = 0; $i < count($this->last_result); $i++) {
            $new_array[$i] = $this->get_var(null, $x, $i);
        }

        return $new_array;
    }

    /**********************************************************************
    *  Return the the query as a result set - see docs for more details
    */

    public function get_results($query = null, $output = OBJECT)
    {

        // Log how the function was called
        $this->func_call = "\$db->get_results(\"$query\", $output)";

        // If there is a query then perform it if not then use cached results..
        if ($query) {
            $this->query($query);
        }

        // Send back array of objects. Each row is an object
        if ($output == OBJECT) {
            return $this->last_result;
        } elseif ($output == ARRAY_A || $output == ARRAY_N) {
            if ($this->last_result) {
                $i = 0;
                foreach ($this->last_result as $row) {
                    $new_array[$i] = get_object_vars($row);

                    if ($output == ARRAY_N) {
                        $new_array[$i] = array_values($new_array[$i]);
                    }

                    $i++;
                }

                return $new_array;
            } else {
                return [];
            }
        }
    }

    /**********************************************************************
    *  Function to get column meta data info pertaining to the last query
    * see docs for more info and usage
    */

    public function get_col_info($info_type = 'name', $col_offset = -1)
    {
        if ($this->col_info) {
            if ($col_offset == -1) {
                $i = 0;
                foreach ($this->col_info as $col) {
                    $new_array[$i] = $col->{$info_type};
                    $i++;
                }

                return $new_array;
            } else {
                return $this->col_info[$col_offset]->{$info_type};
            }
        }
    }

    /**********************************************************************
    *  store_cache
    */

    public function store_cache($query, $is_insert)
    {

        // The would be cache file for this query
        $cache_file = $this->cache_dir.'/'.md5($query);

        // disk caching of queries
        if ($this->use_disk_cache && ($this->cache_queries && !$is_insert) || ($this->cache_inserts && $is_insert)) {
            if (!is_dir($this->cache_dir)) {
                $this->register_error("Could not open cache dir: $this->cache_dir");
                $this->show_errors ? trigger_error("Could not open cache dir: $this->cache_dir", E_USER_WARNING) : null;
            } else {
                // Cache all result values
                $result_cache = 
                [
                    'col_info'     => $this->col_info,
                    'last_result'  => $this->last_result,
                    'num_rows'     => $this->num_rows,
                    'return_value' => $this->num_rows,
                ];
                file_put_contents($cache_file, serialize($result_cache));
                if (file_exists($cache_file.'.updating')) {
                    unlink($cache_file.'.updating');
                }
            }
        }
    }

    /**********************************************************************
    *  get_cache
    */

    public function get_cache($query)
    {

        // The would be cache file for this query
        $cache_file = $this->cache_dir.'/'.md5($query);

        // Try to get previously cached version
        if ($this->use_disk_cache && file_exists($cache_file)) {
            // Only use this cache file if less than 'cache_timeout' (hours)
            if ((time() - filemtime($cache_file)) > ($this->cache_timeout * 3600) &&
                !(file_exists($cache_file.'.updating') && (time() - filemtime($cache_file.'.updating') < 60))) {
                touch($cache_file.'.updating'); // Show that we in the process of updating the cache
            } else {
                $result_cache = unserialize(file_get_contents($cache_file));

                $this->col_info = $result_cache['col_info'];
                $this->last_result = $result_cache['last_result'];
                $this->num_rows = $result_cache['num_rows'];

                $this->from_disk_cache = true;

                // If debug ALL queries
                $this->trace || $this->debug_all ? $this->debug() : null;

                return $result_cache['return_value'];
            }
        }
    }

    /**********************************************************************
    *  Dumps the contents of any input variable to screen in a nicely
    *  formatted and easy to understand way - any type: Object, Var or Array
    */

    public function vardump($mixed = '')
    {

        // Start outup buffering
        ob_start();

        echo '<p><table><tr><td bgcolor=ffffff><blockquote><font color=000090>';
        echo '<pre><font face=arial>';

        if (!$this->vardump_called) {
            echo '<font color=800080><b>ezSQL</b> (v'.EZSQL_VERSION.") <b>Variable Dump..</b></font>\n\n";
        }

        $var_type = gettype($mixed);
        print_r(($mixed ? $mixed : '<font color=red>No Value / False</font>'));
        echo "\n\n<b>Type:</b> ".ucfirst($var_type)."\n";
        echo "<b>Last Query</b> [$this->num_queries]<b>:</b> ".($this->last_query ? $this->last_query : 'NULL')."\n";
        echo '<b>Last Function Call:</b> '.($this->func_call ? $this->func_call : 'None')."\n";
        echo '<b>Last Rows Returned:</b> '.count($this->last_result)."\n";
        echo '</font></pre></font></blockquote></td></tr></table>'.$this->donation();
        echo "\n<hr size=1 noshade color=dddddd>";

        // Stop output buffering and capture debug HTML
        $html = ob_get_contents();
        ob_end_clean();

        // Only echo output if it is turned on
        if ($this->debug_echo_is_on) {
            echo $html;
        }

        $this->vardump_called = true;

        return $html;
    }

    /**********************************************************************
    *  Alias for the above function
    */

    public function dumpvar($mixed)
    {
        $this->vardump($mixed);
    }

    /**********************************************************************
    *  Displays the last query string that was sent to the database & a
    * table listing results (if there were any).
    * (abstracted into a seperate file to save server overhead).
    */

    public function debug($print_to_screen = true)
    {

        // Start outup buffering
        ob_start();

        echo '<blockquote>';

        // Only show ezSQL credits once..
        if (!$this->debug_called) {
            echo '<font color=800080 face=arial size=2><b>ezSQL</b> (v'.EZSQL_VERSION.") <b>Debug..</b></font><p>\n";
        }

        if ($this->last_error) {
            echo "<font face=arial size=2 color=000099><b>Last Error --</b> [<font color=000000><b>$this->last_error</b></font>]<p>";
        }

        if ($this->from_disk_cache) {
            echo '<font face=arial size=2 color=000099><b>Results retrieved from disk cache</b></font><p>';
        }

        echo "<font face=arial size=2 color=000099><b>Query</b> [$this->num_queries] <b>--</b> ";
        echo "[<font color=000000><b>$this->last_query</b></font>]</font><p>";

        echo '<font face=arial size=2 color=000099><b>Query Result..</b></font>';
        echo '<blockquote>';

        if ($this->col_info) {

            // =====================================================
            // Results top rows

            echo '<table cellpadding=5 cellspacing=1 bgcolor=555555>';
            echo '<tr bgcolor=eeeeee><td nowrap valign=bottom><font color=555599 face=arial size=2><b>(row)</b></font></td>';

            for ($i = 0; $i < count($this->col_info); $i++) {
                /* when selecting count(*) the maxlengh is not set, size is set instead. */
                echo "<td nowrap align=left valign=top><font size=1 color=555599 face=arial>{$this->col_info[$i]->type}";
                if (!isset($this->col_info[$i]->max_length)) {
                    echo "{$this->col_info[$i]->size}";
                } else {
                    echo "{$this->col_info[$i]->max_length}";
                }
                echo "</font><br><span style='font-family: arial; font-size: 10pt; font-weight: bold;'>{$this->col_info[$i]->name}</span></td>";
            }

            echo '</tr>';

            // ======================================================
            // print main results

        if ($this->last_result) {
            $i = 0;
            foreach ($this->get_results(null, ARRAY_N) as $one_row) {
                $i++;
                echo "<tr bgcolor=ffffff><td bgcolor=eeeeee nowrap align=middle><font size=2 color=555599 face=arial>$i</font></td>";

                foreach ($one_row as $item) {
                    echo "<td nowrap><font face=arial size=2>$item</font></td>";
                }

                echo '</tr>';
            }
        } // if last result
        else {
            echo '<tr bgcolor=ffffff><td colspan='.(count($this->col_info) + 1).'><font face=arial size=2>No Results</font></td></tr>';
        }

            echo '</table>';
        } // if col_info
        else {
            echo '<font face=arial size=2>No Results</font>';
        }

        echo '</blockquote></blockquote>'.$this->donation().'<hr noshade color=dddddd size=1>';

        // Stop output buffering and capture debug HTML
        $html = ob_get_contents();
        ob_end_clean();

        // Only echo output if it is turned on
        if ($this->debug_echo_is_on && $print_to_screen) {
            echo $html;
        }

        $this->debug_called = true;

        return $html;
    }

    /**********************************************************************
    *  Naughty little function to ask for some remuniration!
    */

    public function donation()
    {
        return '<font size=1 face=arial color=000000>If ezSQL has helped <a href="https://www.paypal.com/xclick/business=justin%40justinvincent.com&item_name=ezSQL&no_note=1&tax=0" style="color: 0000CC;">make a donation!?</a> &nbsp;&nbsp;<!--[ go on! you know you want to! ]--></font>';
    }

    /**********************************************************************
    *  Timer related functions
    */

    public function timer_get_cur()
    {
        list($usec, $sec) = explode(' ', microtime());

        return (float) $usec + (float) $sec;
    }

    public function timer_start($timer_name)
    {
        $this->timers[$timer_name] = $this->timer_get_cur();
    }

    public function timer_elapsed($timer_name)
    {
        return round($this->timer_get_cur() - $this->timers[$timer_name], 2);
    }

    public function timer_update_global($timer_name)
    {
        if ($this->do_profile) {
            $this->profile_times[] = 
            [
                'query' => $this->last_query,
                'time'  => $this->timer_elapsed($timer_name),
            ];
        }

        $this->total_query_time += $this->timer_elapsed($timer_name);
    }

    /**********************************************************************
    * Creates a SET nvp sql string from an associative array (and escapes all values)
    *
    *  Usage:
    *
    *     $db_data = array('login'=>'jv','email'=>'jv@vip.ie', 'user_id' => 1, 'created' => 'NOW()');
    *
    *     $db->query("INSERT INTO users SET ".$db->get_set($db_data));
    *
    *     ...OR...
    *
    *     $db->query("UPDATE users SET ".$db->get_set($db_data)." WHERE user_id = 1");
    *
    * Output:
    *
    *     login = 'jv', email = 'jv@vip.ie', user_id = 1, created = NOW()
    */

    public function get_set($params)
    {
        if (!is_array($params)) {
            $this->register_error('get_set() parameter invalid. Expected array in '.__FILE__.' on line '.__LINE__);

            return;
        }
        $sql = [];
        foreach ($params as $field => $val) {
            if ($val === 'true' || $val === true) {
                $val = 1;
            }
            if ($val === 'false' || $val === false) {
                $val = 0;
            }

            switch ($val) {
                case 'NOW()' :
                case 'NULL' :
                  $sql[] = "$field = $val";
                    break;
                default :
                    $sql[] = "$field = '".$this->escape($val)."'";
            }
        }

        return implode(', ', $sql);
    }
}

/**********************************************************************
*  Author: Justin Vincent (jv@jvmultimedia.com)
*  Web...: http://twitter.com/justinvincent
*  Name..: ezSQL_mysql
*  Desc..: mySQL component (part of ezSQL databse abstraction library)
*
*/

/**********************************************************************
*  ezSQL error strings - mySQL
*/

global $ezsql_mysql_str;

$ezsql_mysql_str = 
[
    1 => 'Require $dbuser and $dbpassword to connect to a database server',
    2 => 'Error establishing mySQL database connection. Correct user/password? Correct hostname? Database server running?',
    3 => 'Require $dbname to select a database',
    4 => 'mySQL database connection is not active',
    5 => 'Unexpected error while trying to select database',
];

/**********************************************************************
*  ezSQL Database specific class - mySQL
*/

class schemadb_ezSQL_mysql extends schemadb_ezSQLcore
{
    public $dbuser = false;
    public $dbpassword = false;
    public $dbname = false;
    public $dbhost = false;
    public $encoding = false;
    public $rows_affected = false;

    /**********************************************************************
    *  Constructor - allow the user to perform a qucik connect at the
    *  same time as initialising the ezSQL_mysql class
    */

    public function schemadb_ezSQL_mysql($dbuser = '', $dbpassword = '', $dbname = '', $dbhost = 'localhost', $encoding = '')
    {
        $this->dbuser = $dbuser;
        $this->dbpassword = $dbpassword;
        $this->dbname = $dbname;
        $this->dbhost = $dbhost;
        $this->encoding = $encoding;
    }

    /**********************************************************************
    *  Short hand way to connect to mySQL database server
    *  and select a mySQL database at the same time
    */

    public function quick_connect($dbuser = '', $dbpassword = '', $dbname = '', $dbhost = 'localhost', $encoding = '')
    {
        $return_val = false;
        if (!$this->connect($dbuser, $dbpassword, $dbhost, true)); elseif (!$this->select($dbname, $encoding)); else {
     $return_val = true;
 }

        return $return_val;
    }

    /**********************************************************************
    *  Try to connect to mySQL database server
    */

    public function connect($dbuser = '', $dbpassword = '', $dbhost = 'localhost')
    {
        global $ezsql_mysql_str;
        $return_val = false;

        // Keep track of how long the DB takes to connect
        $this->timer_start('db_connect_time');

        // Must have a user and a password
        if (!$dbuser) {
            $this->register_error($ezsql_mysql_str[1].' in '.__FILE__.' on line '.__LINE__);
            $this->show_errors ? trigger_error($ezsql_mysql_str[1], E_USER_WARNING) : null;
        }
        // Try to establish the server database handle
        elseif (!$this->dbh = @mysql_connect($dbhost, $dbuser, $dbpassword, true, 131074)) {
            $this->register_error($ezsql_mysql_str[2].' in '.__FILE__.' on line '.__LINE__);
            $this->show_errors ? trigger_error($ezsql_mysql_str[2], E_USER_WARNING) : null;
        } else {
            $this->dbuser = $dbuser;
            $this->dbpassword = $dbpassword;
            $this->dbhost = $dbhost;
            $return_val = true;
        }

        return $return_val;
    }

    /**********************************************************************
    *  Try to select a mySQL database
    */

    public function select($dbname = '', $encoding = '')
    {
        global $ezsql_mysql_str;
        $return_val = false;

        // Must have a database name
        if (!$dbname) {
            $this->register_error($ezsql_mysql_str[3].' in '.__FILE__.' on line '.__LINE__);
            $this->show_errors ? trigger_error($ezsql_mysql_str[3], E_USER_WARNING) : null;
        }

        // Must have an active database connection
        elseif (!$this->dbh) {
            $this->register_error($ezsql_mysql_str[4].' in '.__FILE__.' on line '.__LINE__);
            $this->show_errors ? trigger_error($ezsql_mysql_str[4], E_USER_WARNING) : null;
        }

        // Try to connect to the database
        elseif (!@mysql_select_db($dbname, $this->dbh)) {
            // Try to get error supplied by mysql if not use our own
            if (!$str = @mysql_error($this->dbh)) {
                $str = $ezsql_mysql_str[5];
            }

            $this->register_error($str.' in '.__FILE__.' on line '.__LINE__);
            $this->show_errors ? trigger_error($str, E_USER_WARNING) : null;
        } else {
            $this->dbname = $dbname;
            if ($encoding == '') {
                $encoding = $this->encoding;
            }
            if ($encoding != '') {
                $encoding = strtolower(str_replace('-', '', $encoding));
                $charsets = [];
                $result = mysql_query('SHOW CHARACTER SET');
                while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                    $charsets[] = $row['Charset'];
                }
                if (in_array($encoding, $charsets)) {
                    mysql_query("SET NAMES '".$encoding."'");
                }
            }

            $return_val = true;
        }

        return $return_val;
    }

    /**********************************************************************
    *  Format a mySQL string correctly for safe mySQL insert
    *  (no mater if magic quotes are on or not)
    */

    public function escape($str)
    {
        // If there is no existing database connection then try to connect
        if (!isset($this->dbh) || !$this->dbh) {
            $this->connect($this->dbuser, $this->dbpassword, $this->dbhost);
            $this->select($this->dbname, $this->encoding);
        }

        return mysql_real_escape_string(stripslashes($str));
    }

    /**********************************************************************
    *  Return mySQL specific system date syntax
    *  i.e. Oracle: SYSDATE Mysql: NOW()
    */

    public function sysdate()
    {
        return 'NOW()';
    }

    /**********************************************************************
    *  Perform mySQL query and try to detirmin result value
    */

    public function query($query)
    {

        // This keeps the connection alive for very long running scripts
        if ($this->num_queries >= 500) {
            $this->num_queries = 0;
            $this->disconnect();
            $this->quick_connect($this->dbuser, $this->dbpassword, $this->dbname, $this->dbhost, $this->encoding);
        }

        // Initialise return
        $return_val = 0;

        // Flush cached values..
        $this->flush();

        // For reg expressions
        $query = trim($query);

        // Log how the function was called
        $this->func_call = "\$db->query(\"$query\")";

        // Keep track of the last query for debug..
        $this->last_query = $query;

        // Count how many queries there have been
        $this->num_queries++;

        // Start timer
        $this->timer_start($this->num_queries);

        // Use core file cache function
        if ($cache = $this->get_cache($query)) {
            // Keep tack of how long all queries have taken
            $this->timer_update_global($this->num_queries);

            // Trace all queries
            if ($this->use_trace_log) {
                $this->trace_log[] = $this->debug(false);
            }

            return $cache;
        }

        // If there is no existing database connection then try to connect
        if (!isset($this->dbh) || !$this->dbh) {
            $this->connect($this->dbuser, $this->dbpassword, $this->dbhost);
            $this->select($this->dbname, $this->encoding);
            // No existing connection at this point means the server is unreachable
            if (!isset($this->dbh) || !$this->dbh) {
                return false;
            }
        }

        // Perform the query via std mysql_query function..
        $this->result = @mysql_query($query, $this->dbh);

        // If there is an error then take note of it..
        if ($str = @mysql_error($this->dbh)) {
            $this->register_error($str);
            $this->show_errors ? trigger_error($str, E_USER_WARNING) : null;
            var_dump(debug_backtrace());

            return false;
        }

        // Query was an insert, delete, update, replace
        if (preg_match("/^(insert|delete|update|replace|truncate|drop|create|alter|set)\s+/i", $query)) {
            $is_insert = true;
            $this->rows_affected = @mysql_affected_rows($this->dbh);

            // Take note of the insert_id
            if (preg_match("/^(insert|replace)\s+/i", $query)) {
                $this->insert_id = @mysql_insert_id($this->dbh);
            }

            // Return number fo rows affected
            $return_val = $this->rows_affected;
        }
        // Query was a select
        else {
            $is_insert = false;

            // Take note of column info
            $i = 0;
            while ($i < @mysql_num_fields($this->result)) {
                $this->col_info[$i] = @mysql_fetch_field($this->result);
                $i++;
            }

            // Store Query Results
            $num_rows = 0;
            while ($row = @mysql_fetch_object($this->result)) {
                // Store relults as an objects within main array
                $this->last_result[$num_rows] = $row;
                $num_rows++;
            }

            @mysql_free_result($this->result);

            // Log number of rows the query returned
            $this->num_rows = $num_rows;

            // Return number of rows selected
            $return_val = $this->num_rows;
        }

        // disk caching of queries
        $this->store_cache($query, $is_insert);

        // If debug ALL queries
        $this->trace || $this->debug_all ? $this->debug() : null;

        // Keep tack of how long all queries have taken
        $this->timer_update_global($this->num_queries);

        // Trace all queries
        if ($this->use_trace_log) {
            $this->trace_log[] = $this->debug(false);
        }

        return $return_val;
    }

    /**********************************************************************
    *  Close the active mySQL connection
    */

    public function disconnect()
    {
        @mysql_close($this->dbh);
    }
}
