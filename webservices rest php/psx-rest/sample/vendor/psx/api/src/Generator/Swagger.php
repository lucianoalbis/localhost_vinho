<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Api\Generator;

use PSX\Api\GeneratorAbstract;
use PSX\Api\Resource;
use PSX\Api\Util\Inflection;
use PSX\Data\ExporterInterface;
use PSX\Model\Swagger\Items;
use PSX\Record\Record;
use PSX\Json\Parser;
use PSX\Model\Swagger\Api;
use PSX\Model\Swagger\Declaration;
use PSX\Model\Swagger\Model;
use PSX\Model\Swagger\Models;
use PSX\Model\Swagger\Operation;
use PSX\Model\Swagger\Parameter;
use PSX\Model\Swagger\Properties;
use PSX\Model\Swagger\ResponseMessage;
use PSX\Schema\Property;
use PSX\Schema\PropertyInterface;
use PSX\Schema\SchemaInterface;

/**
 * Generates an Swagger 1.2 representation of an API resource. Note this does
 * not generate a resource listing only the documentation of an single resource
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Swagger extends GeneratorAbstract
{
    /**
     * @var \PSX\Data\ExporterInterface
     */
    protected $exporter;

    /**
     * @var string
     */
    protected $apiVersion;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var string
     */
    protected $targetNamespace;

    public function __construct(ExporterInterface $exporter, $apiVersion, $basePath, $targetNamespace)
    {
        $this->exporter        = $exporter;
        $this->apiVersion      = $apiVersion;
        $this->basePath        = $basePath;
        $this->targetNamespace = $targetNamespace;
    }

    public function generate(Resource $resource)
    {
        $declaration = new Declaration($this->apiVersion);
        $declaration->setBasePath($this->basePath);
        $declaration->setApis($this->getApis($resource));
        $declaration->setModels($this->getModels($resource));
        $declaration->setResourcePath(Inflection::transformRoutePlaceholder($resource->getPath()));

        $swagger = $this->exporter->export($declaration);
        $swagger = Parser::encode($swagger, JSON_PRETTY_PRINT);

        // since swagger does not fully support the json schema spec we must
        // remove the $ref fragments
        $swagger = str_replace('#\/definitions\/', '', $swagger);

        return $swagger;
    }

    protected function getApis(Resource $resource)
    {
        $api         = new Api(Inflection::transformRoutePlaceholder($resource->getPath()));
        $description = $resource->getDescription();
        $methods     = $resource->getMethods();

        if (!empty($description)) {
            $api->setDescription($description);
        }

        foreach ($methods as $method) {
            // get operation name
            $request     = $method->getRequest();
            $response    = $this->getSuccessfulResponse($method);
            $description = $method->getDescription();
            $entityName  = '';

            if ($request instanceof SchemaInterface) {
                $entityName = $request->getDefinition()->getName();
            } elseif ($response instanceof SchemaInterface) {
                $entityName = $response->getDefinition()->getName();
            }

            // create new operation
            $operation = new Operation($method->getName(), strtolower($method->getName()) . ucfirst($entityName));

            if (!empty($description)) {
                $operation->setSummary($description);
            }

            // path parameter
            $parameters = $resource->getPathParameters()->getDefinition();

            foreach ($parameters as $name => $parameter) {
                $param = new Parameter('path', $name, $parameter->getDescription(), $parameter->isRequired());

                $this->setParameterType($parameter, $param);

                $operation->addParameter($param);
            }

            // query parameter
            $parameters = $method->getQueryParameters()->getDefinition();

            foreach ($parameters as $name => $parameter) {
                $param = new Parameter('query', $name, $parameter->getDescription(), $parameter->isRequired());

                $this->setParameterType($parameter, $param);

                $operation->addParameter($param);
            }

            // request body
            if ($request instanceof SchemaInterface) {
                $description = $request->getDefinition()->getDescription();
                $type        = $method->getName() . '-request';
                $parameter   = new Parameter('body', 'body', $description, true);
                $parameter->setType($type);

                $operation->addParameter($parameter);
            }

            // response body
            $responses = $method->getResponses();

            foreach ($responses as $statusCode => $response) {
                $type    = $method->getName() . '-' . $statusCode . '-response';
                $message = $response->getDefinition()->getDescription() ?: $statusCode . ' response';

                $operation->addResponseMessage(new ResponseMessage($statusCode, $message, $type));
            }

            $api->addOperation($operation);
        }

        return array($api);
    }

    protected function getModels(Resource $resource)
    {
        $generator = new JsonSchema($this->targetNamespace);
        $data      = $generator->toArray($resource);
        $models    = new Models();

        if (isset($data['definitions']) && is_array($data['definitions'])) {
            foreach ($data['definitions'] as $name => $definition) {
                $method = strstr($name, '-', true);
                if (in_array($method, ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'])) {
                    $ref = str_replace('#/definitions/', '', $definition['$ref']);
                    if (isset($data['definitions'][$ref])) {
                        $definition = $data['definitions'][$ref];

                        if (isset($models[$name])) {
                            unset($models[$name]);
                        }
                    }
                }

                $description = isset($definition['description']) ? $definition['description'] : null;
                $required    = isset($definition['required'])    ? $definition['required']    : null;

                $model = new Model($name, $description, $required);
                $props = isset($definition['properties']) ? $definition['properties'] : [];

                foreach ($props as $key => $value) {
                    $model->addProperty($key, $this->parseProperty($value));
                }

                if (!empty($props)) {
                    $models[$name] = $model;
                }
            }
        }

        return $models;
    }

    protected function parseProperty(array $data)
    {
        $property = new \PSX\Model\Swagger\Property();

        if (isset($data['$ref'])) {
            $property->setRef($data['$ref']);
        }

        if (isset($data['type'])) {
            $property->setType($data['type']);
        }

        if (isset($data['format'])) {
            $property->setFormat($data['format']);
        }

        if (isset($data['description'])) {
            $property->setDescription($data['description']);
        }

        if (isset($data['minimum'])) {
            $property->setMinimum($data['minimum']);
        }

        if (isset($data['minLength'])) {
            $property->setMinimum($data['minLength']);
        }

        if (isset($data['maximum'])) {
            $property->setMaximum($data['maximum']);
        }

        if (isset($data['maxLength'])) {
            $property->setMaximum($data['maxLength']);
        }

        if (isset($data['enum'])) {
            $property->setEnum($data['enum']);
        }

        if (isset($data['items'])) {
            if (isset($data['items']['$ref'])) {
                $items = new Items();
                $items->setRef($data['items']['$ref']);
                $property->setItems($items);
            } elseif (isset($data['items']['type'])) {
                $items = new Items();
                $items->setType($data['items']['type']);
                if (isset($data['items']['format'])) {
                    $items->setFormat($data['items']['format']);
                }
                $property->setItems($items);
            }
        }

        return $property;
    }
    
    protected function setParameterType(PropertyInterface $parameter, Parameter $param)
    {
        switch (true) {
            case $parameter instanceof Property\IntegerType:
                $param->setType('integer');
                break;

            case $parameter instanceof Property\FloatType:
                $param->setType('number');
                break;

            case $parameter instanceof Property\BooleanType:
                $param->setType('boolean');
                break;

            case $parameter instanceof Property\DateType:
                $param->setType('string');
                $param->setFormat('date');
                break;

            case $parameter instanceof Property\DateTimeType:
                $param->setType('string');
                $param->setFormat('date-time');
                break;

            default:
                $param->setType('string');
                break;
        }

        $param->setDescription($parameter->getDescription());
        $param->setRequired($parameter->isRequired());

        if ($parameter instanceof Property\DecimalType) {
            $param->setMinimum($parameter->getMin());
            $param->setMaximum($parameter->getMax());
        } elseif ($parameter instanceof Property\StringType) {
            $param->setMinimum($parameter->getMinLength());
            $param->setMaximum($parameter->getMaxLength());
            $param->setEnum($parameter->getEnumeration());
        }
    }
}
