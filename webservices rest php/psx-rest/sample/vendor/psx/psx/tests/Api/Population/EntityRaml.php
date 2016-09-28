<?php

namespace PSX\Project\Tests\Api\Population;

use PSX\Api\Parser\Raml;
use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Framework\Loader\Context;
use PSX\Record\RecordInterface;

class EntityRaml extends SchemaApiAbstract
{
    /**
     * @Inject
     * @var \PSX\Project\Tests\Service\Population
     */
    protected $populationService;

    public function getDocumentation($version = null)
    {
        return Raml::fromFile(__DIR__ . '/../../Resource/population.raml', $this->context->get(Context::KEY_PATH));
    }

    protected function doGet()
    {
        return $this->populationService->get(
            $this->pathParameters['id']
        );
    }

    protected function doPut($record)
    {
        $this->populationService->update(
            $this->pathParameters['id'],
            $record['place'],
            $record['region'],
            $record['population'],
            $record['users'],
            $record['worldUsers']
        );

        return [
            'success' => true,
            'message' => 'Update successful',
        ];
    }

    protected function doDelete($record)
    {
        $this->populationService->delete(
            $this->pathParameters['id']
        );

        return [
            'success' => true,
            'message' => 'Delete successful',
        ];
    }
}
