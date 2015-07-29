<?php

namespace spec\LaraPackage\RandomId;

use LaraPackage\RandomId\Contracts\Retriever;
use App\Contracts\Uri\Parser;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class HelperSpec extends ObjectBehavior
{
    function it_gets_random_id_using_config(\App\Contracts\Config\ApiVersion $config)
    {
        $resource = '/attributes/{random_id}';
        $version = 4;
        $return = [8];
        $this->configExpectation($config, $resource, 4, $return);
        $this->getRandomIdsForUri($resource, $version)->shouldReturn($return);
    }

    function it_gets_random_ids_for_a_resource(Parser $parser, \LaraPackage\RandomId\Contracts\Retriever $idRetriever)
    {
        $resource = '/sites/{random_id}/products/{random_id}/images';
        $expected = [1, 2];

        $entities = ['sites', 'products', 'images'];
        $idEntities = ['sites', 'products'];
        $idPlaceholder = '{random_id}';

        $parser->idEntities($resource, $idPlaceholder)->shouldBeCalled()->willReturn($idEntities);
        $parser->entities($resource, $idPlaceholder)->shouldBeCalled()->willReturn($entities);
        $idRetriever->getRandomIds($entities, $idEntities)->shouldBeCalled()->willReturn($expected);
        $this->getRandomIdsForUri($resource, 4)->shouldReturn($expected);
    }

    function it_gets_random_ids_for_the_last_entity(Parser $parser, \LaraPackage\RandomId\Contracts\Retriever $idRetriever)
    {
        $resource = '/sites/1/products/2/images';
        $entities = ['sites', 'products', 'images'];
        $payload = '[{"id": {random_id} },{"id": {random_id}}]';
        $take = 2;
        $randomIds = [5, 4];
        $table = 'images';

        $parser->entities($resource)->shouldBeCalled()->willReturn($entities);
        $idRetriever->getRandomIdsFromTable($table, $take)->shouldBeCalled()->willReturn($randomIds);

        $this->getRandomIdsForLastEntity($resource, $payload)->shouldReturn($randomIds);
    }

    function it_gets_random_ids_for_the_last_entity_not_in_pivot(Parser $parser, \LaraPackage\RandomId\Contracts\Retriever $idRetriever)
    {
        $resource = '/sites/1/products/2/images';
        $entities = ['sites', 'products', 'images'];
        $idEntities = ['sites', 'products'];
        $ids = [1, 2];
        $randomIdNotInPivot = ['id' => 56];
        $count = 2;

        $parser->entities($resource)->shouldBeCalled()->willReturn($entities);
        $parser->idEntities($resource)->shouldBeCalled()->willReturn($idEntities);
        $parser->ids($resource)->shouldBeCalled()->willReturn($ids);

        $idRetriever->getRandomIdsNotInPivot($entities, $idEntities, $ids, $count)->shouldBeCalled()->willReturn($randomIdNotInPivot);

        $this->getRandomIdsForLastEntityNotInPivot($resource, $count)->shouldReturn($randomIdNotInPivot);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('LaraPackage\IdRetriever\Helper');
    }

    function it_puts_random_ids_into_the_payload()
    {
        $ids = [1, 2];
        $payload = '[{"id": "{random_id}" },{"id": "{random_id}"}]';

        $replacedPayload = '[{"id": 1 },{"id": 2}]';
        $this->putIdsInPayload($payload, $ids)->shouldReturn($replacedPayload);
    }

    function it_puts_the_supplied_ids_into_a_resource(Parser $parser)
    {
        $resource = '/sites/{random_id}/products/{random_id}/images/{random_id}';
        $ids = [1, 2, 3];
        $expected = '/sites/1/products/2/images/3/';

        $parser->entities($resource, '{random_id}')->shouldBeCalled()->willReturn(['sites', 'products', 'images']);
        $this->putIdsInUri($resource, $ids)->shouldReturn($expected);
    }

    function let(Parser $parser, \LaraPackage\RandomId\Contracts\Retriever $idRetriever, \App\Contracts\Config\ApiVersion $config)
    {
        $this->beConstructedWith($parser, $idRetriever, $config);
    }

    protected function configExpectation(\App\Contracts\Config\ApiVersion $config, $resource, $version, $return)
    {
        $config->resourceIdMap($resource, $version)->shouldBeCalled()->willReturn($return);
    }
}
