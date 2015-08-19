<?php

namespace spec\LaraPackage\RandomId;

use LaraPackage\RandomId\TableFetcher;
use LaraPackage\RandomId\TableHelper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RetrieverSpec extends ObjectBehavior
{
    function it_gets_random_ids_for_an_entity_that_are_not_in_a_pivot(TableFetcher $tableFetcher, TableHelper $tableHelper)
    {
        $entities = ['sites', 'products', 'images'];
        $idEntities = ['sites', 'products'];
        $table = 'images';
        $pivotColumn = 'image_id';
        $pivot = 'image_product_site';
        $ids = [1, 2];
        $imageId = [5];
        $columns = ['site_id', 'product_id'];
        $count = 2;

        $tableHelper->getLastEntityAsIdColumnName($entities)->shouldBeCalled()->willReturn($pivotColumn);
        $tableHelper->getTable($entities)->shouldBeCalled()->willReturn($pivot);
        $tableHelper->getIdColumnNames($entities, $idEntities)->shouldBeCalled()->willReturn($columns);

        $tableFetcher->getRandomIdsFromTableWhereNotInPivot($table, $pivot, $pivotColumn, $count, array_combine($columns, $ids))->shouldBeCalled()->willReturn($imageId);
        $this->getRandomIdsNotInPivot($entities, $idEntities, $ids, $count);
    }

    function it_gets_random_ids_for_supplied_entities(TableFetcher $tableFetcher, TableHelper $tableHelper)
    {
        $entities = ['sites', 'products', 'images'];
        $idEntities = ['sites', 'products'];
        $columns = ['site_id', 'product_id'];
        $table = 'image_product_site';
        $result = [1, 2];

        $tableFetcher->getRandomColumnEntries($table, $columns)
            ->shouldBeCalled()->willReturn($result);

        $tableHelper->getTable($entities)->shouldBeCalled()->willReturn($table);
        $tableHelper->getIdColumnNames($entities, $idEntities)->willReturn($columns);
        $this->getRandomIds($entities, $idEntities)->shouldReturn($result);
    }

    function it_gets_random_ids_from_a_table(TableFetcher $tableFetcher)
    {
        $take = 4;
        $randomIds = [3, 6, 7, 2];
        $table = 'images';

        $tableFetcher->getRandomIds($table, $take)->shouldBeCalled()->willReturn($randomIds);
        $this->getRandomIdsFromTable($table, $take)->shouldReturn($randomIds);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('LaraPackage\RandomId\Retriever');
    }

    function it_returns_an_empty_array_if_no_entities_needed_ids()
    {
        $this->getRandomIds(['foobar'], []);
    }

    function let(TableFetcher $tableFetcher, TableHelper $tableHelper)
    {
        $this->beConstructedWith($tableFetcher, $tableHelper);
    }
}
