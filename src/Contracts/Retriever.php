<?php
namespace LaraPackage\RandomId\Contracts;

interface Retriever
{
    /**
     * Returns random ids from a table using the provided entities and ID entities.
     * The id columns depend on the $entities and $idEntities provided.
     * Can be used with Pivot Tables.
     *
     * @param array $entities
     * @param array $idEntities
     *
     * @return array
     */
    public function getRandomIds(array $entities, array $idEntities);

    /**
     * Returns random ids from an entity table
     *
     * @param string $table
     * @param int    $take
     *
     * @return array
     */
    public function getRandomIdsFromTable($table, $take);

    /**
     * Returns random IDs for the last $entity that are not in the pivot table
     *
     * @param array $entities   last entity is the one that ids will be retrieved for
     * @param array $idEntities entities that have ids in the URI
     * @param array $ids        ids for the where clause on the pivot table
     * @param int   $count      the number of ids to get
     *
     * @return array
     */
    public function getRandomIdsNotInPivot(array $entities, array $idEntities, array $ids, $count);
}
