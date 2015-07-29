<?php
namespace LaraPackage\RandomId;

class TableHelper
{
    /**
     * Returns an array of id column names from arrays of $entities and $idEntities
     *
     * @param array $entities
     * @param array $idEntities
     *
     * @return array
     */
    public function getIdColumnNames(array $entities, array $idEntities)
    {
        // if there is only one entity that needs an id and only one entity total then
        // the column is 'id'
        if ($this->thereIsOneIdEntityAndOnlyOneEntity($entities, $idEntities)) {
            return ['id'];
        }

        // for everything else column names should be created from
        // entities that need ids in the form of: item_id etc
        return array_map(
            function ($value) {
                return $this->makeIdColumnName($value);
            },
            $idEntities
        );
    }

    /**
     * Returns the last entity as an id column name
     *
     * @param array $entities
     *
     * @return string
     */
    public function getLastEntityAsIdColumnName(array $entities)
    {
        $last = array_pop($entities);

        return $this->makeIdColumnName($last);
    }

    /**
     * Returns a table name using the supplied entities
     *
     * @param array $entities
     *
     * @return string
     */
    public function getTable(array $entities)
    {
        if (count($entities) === 1) {
            // the entity should be plural coming in.
            return $entities[0];
        }

        // the table name should all be single
        $entities = array_map(['self', 'singularize'], $entities);
        sort($entities);

        return implode('_', $entities);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function makeIdColumnName($string)
    {
        return $this->singularize($string).'_id';
    }

    /**
     * Makes a given word singular
     *
     * @param string $word
     *
     * @return string
     */
    public function singularize($word)
    {
        return \Doctrine\Common\Inflector\Inflector::singularize($word);
    }

    /**
     * @param array $idEntities
     *
     * @return bool
     */
    protected function thereIsOneIdEntityAndOnlyOneEntity(array $entities, array $idEntities)
    {
        return (count($idEntities) === 1) && (count($entities) === 1);
    }
}
