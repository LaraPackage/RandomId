<?php
namespace LaraPackage\RandomId\Contracts;

interface Helper
{
    /**
     * @param string $uri
     * @param string $payload
     * @param string $idPlaceholder
     *
     * @return array
     */
    public function getRandomIdsForLastEntity($uri, $payload, $idPlaceholder = '{random_id}');

    /**
     * @param string $uri
     * @param int    $take number of ids to return
     *
     * @return array
     */
    public function getRandomIdsForLastEntityNotInPivot($uri, $take);

    /**
     * Finds the $idPlaceholder in the supplied payload and replaces them with random ids.
     * The column names are found using $idColumnNameEnding.
     * The table name is found using the column names.
     *
     * @param array  $payload
     * @param string $idPlaceholder
     * @param string $idColumnNameEnding
     *
     * @return array|false
     */
    public function getRandomIdsForPayload(array $payload, $idPlaceholder = '{random_id}', $idColumnNameEnding = '_id');

    /**
     * Gets random ids for $uri using $idPlaceholder; random ids derived from the URI entities
     *
     * @param string $uri
     * @param int    $version
     * @param string $idPlaceholder
     *
     * @return array|false
     */
    public function getRandomIdsForUri($uri, $version, $idPlaceholder = '{random_id}');

    /**
     * Takes $ids and replaces $randomIdString sequentially with them in the $payload
     *
     * @param string $payload
     * @param array  $ids
     * @param string $randomIdString
     *
     * @return string
     */
    public function putIdsInPayload($payload, array $ids, $randomIdString = '"{random_id}"');

    /**
     * Replaces the $idPlaceholder in $uri with the supplied $ids
     *
     * @param string $uri
     * @param array  $ids
     * @param string $idPlaceholder
     *
     * @return string
     */
    public function putIdsInUri($uri, array $ids, $idPlaceholder = '{random_id}');
}
