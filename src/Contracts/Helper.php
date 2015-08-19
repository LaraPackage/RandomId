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
     * @param string   $uri
     * @param \Closure $idOverride
     * @param string   $idPlaceholder
     *
     * @return array|false
     */
    public function getRandomIdsForUri($uri, \Closure $idOverride = null, $idPlaceholder = '{random_id}');

    /**
     * Replaces placeholders in query string with values from data
     *
     * @param string $queryString
     * @param array $data assoc array; the placeholders are used to pull values from here
     *
     * @return string  the new query string
     */
    public function putDataInQueryString($queryString, array $data);

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
