<?php

namespace spec\LaraPackage\RandomId;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TableHelperSpec extends ObjectBehavior
{
    function it_creates_a_table_name_from_entities()
    {
        $this->getTable(['sites', 'products', 'images'])
            ->shouldReturn('image_product_site');
    }

    function it_gets_column_names_from_entities()
    {
        $this->getIdColumnNames(['sites', 'products', 'images'], ['sites', 'products', 'images'])
            ->shouldReturn(['site_id', 'product_id', 'image_id']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('LaraPackage\IdRetriever\TableHelper');
    }

    function it_makes_a_column_name()
    {
        $this->makeIdColumnName('images')->shouldReturn('image_id');
    }

    function it_returns_a_entity_column_id_if_using_a_pivot_table()
    {
        $entities = ['cataloggroups', 'catalogtabs'];
        $idsNeeded = ['cataloggroups'];
        $this->getIdColumnNames($entities, $idsNeeded)->shouldReturn($columns = ['cataloggroup_id']);
    }

    function it_returns_a_plural_table_name_if_not_a_pivot()
    {
        $this->getTable(['sites'])->shouldReturn('sites');
    }

    public function it_returns_the_last_entity_as_a_column_name()
    {
        $this->getLastEntityAsIdColumnName(['sites', 'products', 'images'])->shouldReturn('image_id');
    }
}
