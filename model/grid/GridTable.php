<?php

##
class GridTable
{
    private $columns = [];

    private $service;

    public function __construct()
    {
    }

    public function addColumn($column)
    {
        $this->columns[] = $column;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    ##

    public function setService($service)
    {
        $this->service = $service;
    }

    ##

    public function getService()
    {
        return $this->service;
    }

    ##

    public function getDefaultSortOrder()
    {
        $o = [];
        foreach ($this->getColumns() as $i => $c) {
            $s = $c->getDefaultSort();
            if ($s) {
                $o[] = [$i, $s];
            }
        }

        return json_encode($o);
    }

    ##

    public function getColumnsDefinition()
    {
        $d = [];
        foreach ($this->getColumns() as $i => $c) {
            $d[] = [
                'bVisible'    => $c->isVisible(),
                'bSortable'   => $c->isSortable(),
                'sWidth'      => $c->getWidth(),
            ];
        }

        return json_encode($d);
    }
}
