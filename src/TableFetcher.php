<?php
namespace LaraPackage\RandomId;

class TableFetcher
{
    /**
     * Returns random entries for columns using an optional where clause
     *
     * @param string $table
     * @param array  $columns
     * @param array  $where
     *
     * @return array
     */
    public function getRandomColumnEntries($table, $columns = ['id'], $where = [])
    {
        $results = \DB::table($table)->select($columns)->orderBy(\DB::raw('RAND()'));
        if (!empty($where)) {
            $results->where($where);
        }

        $results = $results->first();

        if (is_null($results)) {
            throw new \RuntimeException('Db id result is null');
        }

        return get_object_vars($results);
    }

    /**
     * Returns random ids from the `id` column of a table
     *
     * @param string $table
     * @param int    $take
     *
     * @return array
     */
    public function getRandomIds($table, $take)
    {
        $results = \DB::table($table)
            ->select('id')
            ->orderBy(\DB::raw('RAND()'))
            ->take($take)
            ->get();

        if (is_null($results)) {
            throw new \RuntimeException('Db id result is null');
        }

        $return = [];

        foreach ($results as $array) {
            $return[] = array_values(get_object_vars($array))[0];
        }

        return $return;
    }

    /**
     * Returns random ids from a table for a pivot table where those
     * ids are unique in combination with other pivot columns
     *
     * @param string $table
     * @param string $pivotTable
     * @param string $pivotJoinColumn
     * @param int    $take
     * @param array  $pivotWhereJoinColumnsValues
     * @param string $tableColumn
     *
     * @return array
     */
    public function getRandomIdsFromTableWhereNotInPivot($table, $pivotTable, $pivotJoinColumn, $take, $pivotWhereJoinColumnsValues = [], $tableColumn = 'id')
    {
        $results = \DB::table($table)
            ->select($table.'.'.$tableColumn)
            ->leftJoin($pivotTable, function ($leftJoin) use ($table, $tableColumn, $pivotTable, $pivotJoinColumn, $pivotWhereJoinColumnsValues) {
                $leftJoin = $leftJoin->on($table.'.'.$tableColumn, '=', $pivotTable.'.'.$pivotJoinColumn);
                foreach ($pivotWhereJoinColumnsValues as $column => $value) {
                    $leftJoin->where($pivotTable.'.'.$column, '=', $value);
                }
            })
            ->whereNull($pivotTable.'.'.$pivotJoinColumn)
            ->orderBy(\DB::raw('RAND()'))
            ->take($take)
            ->get();

        if (is_null($results)) {
            throw new \RuntimeException('Db id result is null');
        }

        if (count($results) !== $take) {
            throw new \RuntimeException('Db id result does not match take number');
        }

        return $this->getIdsFromCollectionOfObjects($results);
    }

    /**
     * @param array $collection
     *
     * @return array
     */
    protected function getIdsFromCollectionOfObjects(array $collection)
    {
        $out = [];

        foreach ($collection as $object) {
            $array = array_values(get_object_vars($object));
            $out = array_merge($out, $array);
        }

        return $out;
    }
}
