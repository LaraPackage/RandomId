<?php

namespace LaraPackage\RandomId;

class Retriever implements \LaraPackage\RandomId\Contracts\Retriever
{
    /**
     * @var \LaraPackage\RandomId\TableFetcher
     */
    private $db;

    /**
     * @var TableHelper
     */
    private $tableHelper;

    /**
     * @param \LaraPackage\RandomId\TableFetcher $db
     * @param TableHelper                $tableHelper
     */
    public function __construct(\LaraPackage\RandomId\TableFetcher $db, TableHelper $tableHelper)
    {
        $this->db = $db;
        $this->tableHelper = $tableHelper;
    }

    /**
     * @inheritdoc
     */
    public function getRandomIds(array $entities, array $idEntities)
    {
        if (count($idEntities) === 0) {
            return [];
        }

        return $this->db->getRandomColumnEntries(
            $this->tableHelper->getTable($entities), $this->tableHelper->getIdColumnNames($entities, $idEntities)
        );
    }

    /**
     * @inheritdoc
     */
    public function getRandomIdsFromTable($table, $take)
    {
        return $this->db->getRandomIds($table, $take);
    }

    /**
     * @inheritdoc
     */
    public function getRandomIdsNotInPivot(array $entities, array $idEntities, array $ids, $count)
    {
        // the table to retrieve ids from
        $table = last($entities);

        $pivotTable = $this->tableHelper->getTable($entities);
        $pivotColumn = $this->tableHelper->getLastEntityAsIdColumnName($entities);

        $columnNames = $this->tableHelper->getIdColumnNames($entities, $idEntities);

        if (count($entities) === count($ids)) {
            array_pop($ids);
        }

        $pivotWhereJoinColumns = array_combine($columnNames, $ids);

        return $this->db->getRandomIdsFromTableWhereNotInPivot($table, $pivotTable, $pivotColumn, $count, $pivotWhereJoinColumns);
    }
}
