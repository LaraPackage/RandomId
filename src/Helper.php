<?php
namespace LaraPackage\RandomId;

use PrometheusApi\Utilities\Contracts\Uri\Parser as UriParser;

class Helper implements Contracts\Helper
{
    /**
     * @var UriParser
     */
    protected $uriParser;

    /**
     * @var Retriever
     */
    protected $idRetriever;

    /**
     * @param UriParser $uriParser
     * @param \LaraPackage\RandomId\Contracts\Retriever $idRetriever
     */
    public function __construct(
        UriParser $uriParser,
        Contracts\Retriever $idRetriever
    )
    {
        $this->uriParser = $uriParser;
        $this->idRetriever = $idRetriever;
    }

    /**
     * @inheritdoc
     */
    public function getRandomIdsForLastEntity($uri, $payload, $idPlaceholder = '{random_id}')
    {
        $take = substr_count($payload, $idPlaceholder);
        $entities = $this->uriParser->entities($uri);
        $table = last($entities);

        return $this->idRetriever->getRandomIdsFromTable($table, $take);
    }

    /**
     * @inheritdoc
     */
    public function getRandomIdsForLastEntityNotInPivot($uri, $take)
    {
        $entities = $this->uriParser->entities($uri);
        $idEntities = $this->uriParser->idEntities($uri);

        // ids for the where clause on the pivot table
        $ids = $this->uriParser->ids($uri);

        return $this->idRetriever->getRandomIdsNotInPivot($entities, $idEntities, $ids, $take);
    }

    /**
     * @inheritdoc
     */
    public function getRandomIdsForPayload(array $payload, $idPlaceholder = '{random_id}', $idColumnNameEnding = '_id')
    {

        $keys = array_keys($payload, $idPlaceholder);

        $randomIdsColumns = array_filter(array_map(function ($value) use ($idColumnNameEnding) {
            if (strpos($value, $idColumnNameEnding) !== false) {
                return $value;
            }
        }, $keys));

        if (count($randomIdsColumns) <= 0) {
            return false;
        }

        //  Ids are sequential and that is how they will be eventually put back into the payload.
        $ids = [];
        foreach ($randomIdsColumns as $columnName) {
            $singleName = explode($idColumnNameEnding, $columnName)[0];
            $table = \Doctrine\Common\Inflector\Inflector::pluralize($singleName);
            $ids[] = $this->idRetriever->getRandomIdsFromTable($table, 1)[0];
        }

        return $ids;
    }

    /**
     * @inheritdoc
     */
    public function getRandomIdsForUri($uri, \Closure $idOverride = null, $idPlaceholder = '{random_id}')
    {
        if ($idOverride) {
            $idMap = $idOverride($uri, $idPlaceholder);
            if ($idMap) {
                return $idMap;
            }
        }

        return $this->idRetriever->getRandomIds(
            $this->uriParser->entities($uri, $idPlaceholder),
            $this->uriParser->idEntities($uri, $idPlaceholder)
        );
    }

    /**
     * @inheritdoc
     */
    public function putDataInQueryString($queryString, array $data)
    {
        $returnQueries = [];

        $queries = explode('&', $queryString);

        foreach ($queries as $query) {
            $queryArray = explode('=', $query);
            $key = $queryArray[0];
            $placeHolder = $queryArray[1];

            $valueKey = str_replace(['{', '}'], ['', ''], $placeHolder);
            $value = $data[$valueKey];

            $returnQueries[] = $key.'='.$value;
        }

        return implode('&', $returnQueries);
    }

    /**
     * @inheritdoc
     */
    public function putIdsInPayload($payload, array $ids, $randomIdString = '"{random_id}"')
    {
        return str_replace_array($randomIdString, $ids, $payload);
    }

    /**
     * @inheritdoc
     */
    public function putIdsInUri($uri, array $ids, $idPlaceholder = '{random_id}')
    {
        $entities = $this->uriParser->entities($uri, $idPlaceholder);
        if (abs(count($entities) - count($ids)) > 1) {
            throw new \RuntimeException('Ids and entities count must be off by no more than 1');
        }

        return array_reduce($entities, function ($carry, $item) use (&$ids) {
            $id = array_shift($ids);
            if ($id) {
                return $carry.$item.'/'.$id.'/';
            }

            return $carry.$item;

        }, '/');
    }
}
