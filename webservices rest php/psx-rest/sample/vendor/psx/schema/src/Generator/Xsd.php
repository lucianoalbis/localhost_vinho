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

namespace PSX\Schema\Generator;

use PSX\Schema\GeneratorInterface;
use PSX\Schema\Property;
use PSX\Schema\PropertyAbstract;
use PSX\Schema\PropertyInterface;
use PSX\Schema\PropertySimpleAbstract;
use PSX\Schema\SchemaInterface;

/**
 * Xsd
 *
 * @see     http://www.w3.org/XML/Schema
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Xsd implements GeneratorInterface
{
    protected $writer;
    protected $targetNamespace;

    private $_types = array();

    public function __construct($targetNamespace)
    {
        $this->writer = new \XMLWriter();
        $this->writer->openMemory();

        $this->targetNamespace = $targetNamespace;
    }

    public function generate(SchemaInterface $schema)
    {
        $this->writer->startDocument('1.0', 'UTF-8');
        $this->writer->startElement('xs:schema');
        $this->writer->writeAttribute('xmlns:xs', 'http://www.w3.org/2001/XMLSchema');
        $this->writer->writeAttribute('xmlns:tns', $this->targetNamespace);
        $this->writer->writeAttribute('targetNamespace', $this->targetNamespace);
        $this->writer->writeAttribute('elementFormDefault', 'qualified');

        // generate elements
        $this->generateRootElement($schema->getDefinition());

        $this->writer->endElement();
        $this->writer->endDocument();

        return $this->writer->outputMemory(true);
    }

    protected function generateRootElement(Property\ComplexType $type)
    {
        $this->writer->startElement('xs:element');
        $this->writer->writeAttribute('name', $type->getName());
        $this->writer->startElement('xs:complexType');

        $documentation = $type->getDescription();
        if (!empty($documentation)) {
            $this->writer->startElement('xs:annotation');
            $this->writer->writeElement('xs:documentation', $documentation);
            $this->writer->endElement();
        }

        $this->writer->startElement('xs:sequence');

        $properties = $type->getProperties();
        foreach ($properties as $name => $property) {
            $this->generateProperty($name, $property);
        }

        $this->writer->endElement();
        $this->writer->endElement();
        $this->writer->endElement();

        $this->generateTypes($type->getProperties());
    }

    protected function generateTypes(array $properties)
    {
        foreach ($properties as $property) {
            if ($property instanceof Property\ArrayType) {
                $property = $property->getPrototype();
            }

            if ($this->hasConstraints($property)) {
                $this->generateType($property);
            }
        }
    }

    protected function generateType(PropertyInterface $type)
    {
        $typeName = $this->getPropertyTypeName($type, false);

        if (in_array($typeName, $this->_types)) {
            return;
        }

        $this->_types[] = $typeName;

        if ($type instanceof Property\ChoiceType) {
            $this->writer->startElement('xs:complexType');
            $this->writer->writeAttribute('name', $typeName);

            $documentation = $type->getDescription();
            if (!empty($documentation)) {
                $this->writer->startElement('xs:annotation');
                $this->writer->writeElement('xs:documentation', $documentation);
                $this->writer->endElement();
            }

            $this->writer->startElement('xs:choice');

            $choices = $type->getChoices();
            foreach ($choices as $property) {
                $name = $property->getName();
                if (empty($name)) {
                    $name = substr($property->getId(), 0, 8);
                }

                $this->generateProperty($name, $property);
            }

            $this->writer->endElement();
            $this->writer->endElement();

            $this->generateTypes($type->getChoices());
        } elseif ($type instanceof Property\ComplexType) {
            $this->writer->startElement('xs:complexType');
            $this->writer->writeAttribute('name', $typeName);

            $documentation = $type->getDescription();
            if (!empty($documentation)) {
                $this->writer->startElement('xs:annotation');
                $this->writer->writeElement('xs:documentation', $documentation);
                $this->writer->endElement();
            }

            $this->writer->startElement('xs:sequence');

            $properties = $type->getProperties();
            foreach ($properties as $name => $property) {
                $this->generateProperty($name, $property);
            }

            // if we have additional or pattern properties we allow any
            // additional elements. Unfortunately there is afaik no way in
            // XSD to specify a schema for element names which matche a
            // specific regexp
            $patternProperties    = $type->getPatternProperties();
            $additionalProperties = $type->getAdditionalProperties();

            if (!empty($patternProperties) || !empty($additionalProperties)) {
                $minProperties = $type->getMinProperties();
                $minProperties = $minProperties === null ? 0 : $minProperties;
                $maxProperties = $type->getMaxProperties();
                $maxProperties = $maxProperties === null ? 'unbounded' : $maxProperties;

                $this->writer->startElement('xs:any');
                $this->writer->writeAttribute('processContents', 'lax');
                $this->writer->writeAttribute('minOccurs', $minProperties);
                $this->writer->writeAttribute('maxOccurs', $maxProperties);
                $this->writer->endElement();
            }

            $this->writer->endElement();
            $this->writer->endElement();

            $this->generateTypes($type->getProperties());
        } else {
            $this->writer->startElement('xs:simpleType');
            $this->writer->writeAttribute('name', $typeName);

            $documentation = $type->getDescription();
            if (!empty($documentation)) {
                $this->writer->startElement('xs:annotation');
                $this->writer->writeElement('xs:documentation', $documentation);
                $this->writer->endElement();
            }

            $this->writer->startElement('xs:restriction');
            $this->writer->writeAttribute('base', $this->getBasicType($type));

            if ($type instanceof Property\StringType) {
                $this->generateTypeString($type);
            } elseif ($type instanceof Property\DecimalType) {
                $this->generateTypeDecimal($type);
            }

            $pattern = $type->getPattern();
            if ($pattern) {
                $this->writer->startElement('xs:pattern');
                $this->writer->writeAttribute('value', $pattern);
                $this->writer->endElement();
            }

            $enumeration = $type->getEnumeration();
            if ($enumeration) {
                foreach ($enumeration as $value) {
                    $this->writer->startElement('xs:enumeration');
                    $this->writer->writeAttribute('value', $value);
                    $this->writer->endElement();
                }
            }

            $this->writer->endElement();
            $this->writer->endElement();
        }
    }

    protected function generateTypeDecimal(Property\DecimalType $type)
    {
        $max = $type->getMax();
        if ($max) {
            $this->writer->startElement('xs:maxInclusive');
            $this->writer->writeAttribute('value', $max);
            $this->writer->endElement();
        }

        $min = $type->getMin();
        if ($min) {
            $this->writer->startElement('xs:minInclusive');
            $this->writer->writeAttribute('value', $min);
            $this->writer->endElement();
        }
    }

    protected function generateTypeString(Property\StringType $type)
    {
        $minLength = $type->getMinLength();
        if ($minLength) {
            $this->writer->startElement('xs:minLength');
            $this->writer->writeAttribute('value', $minLength);
            $this->writer->endElement();
        }

        $maxLength = $type->getMaxLength();
        if ($maxLength) {
            $this->writer->startElement('xs:maxLength');
            $this->writer->writeAttribute('value', $maxLength);
            $this->writer->endElement();
        }
    }

    protected function generateProperty($name, PropertyInterface $property)
    {
        if ($property instanceof Property\ArrayType) {
            $this->writer->startElement('xs:element');
            $this->writer->writeAttribute('name', $name);
            $this->writer->writeAttribute('type', $this->getPropertyTypeName($property->getPrototype(), true));

            $minOccurs = $property->getMinLength();
            $maxOccurs = $property->getMaxLength();

            if ($minOccurs !== null && $maxOccurs !== null) {
                $this->writer->writeAttribute('minOccurs', $minOccurs);
                $this->writer->writeAttribute('maxOccurs', $maxOccurs);
            } elseif ($minOccurs !== null) {
                $this->writer->writeAttribute('minOccurs', $minOccurs);
                $this->writer->writeAttribute('maxOccurs', 'unbounded');
            } elseif ($maxOccurs !== null) {
                $this->writer->writeAttribute('minOccurs', 0);
                $this->writer->writeAttribute('maxOccurs', $maxOccurs);
            } else {
                $this->writer->writeAttribute('minOccurs', 0);
                $this->writer->writeAttribute('maxOccurs', 'unbounded');
            }

            $this->writer->endElement();
        } else {
            $this->writer->startElement('xs:element');
            $this->writer->writeAttribute('name', $name);
            $this->writer->writeAttribute('type', $this->getPropertyTypeName($property, true));
            $this->writer->writeAttribute('minOccurs', $property->isRequired() ? 1 : 0);
            $this->writer->writeAttribute('maxOccurs', 1);
            $this->writer->endElement();
        }
    }

    protected function getPropertyTypeName(PropertyAbstract $type, $withNamespace)
    {
        if ($this->hasConstraints($type)) {
            return ($withNamespace === false ? '' : 'tns:') . 'type' . $type->getId();
        } else {
            return $this->getBasicType($type);
        }
    }

    protected function getBasicType(PropertyAbstract $type)
    {
        $typeName = $type->getTypeName();

        switch ($typeName) {
            case 'binary':
                return 'xs:base64Binary';
                break;

            case 'uri':
                return 'xs:anyURI';
                break;

            case 'boolean':
            case 'dateTime':
            case 'date':
            case 'duration':
            case 'float':
            case 'integer':
            case 'string':
            case 'time':
            default:
                return 'xs:' . $typeName;
        }
    }

    protected function hasConstraints(PropertyInterface $type)
    {
        if ($type instanceof Property\ArrayType || $type instanceof Property\ChoiceType || $type instanceof Property\ComplexType) {
            return true;
        } elseif ($type instanceof PropertySimpleAbstract) {
            if ($type instanceof Property\DecimalType) {
                if ($type->getMin() !== null || $type->getMax() !== null) {
                    return true;
                }
            } elseif ($type instanceof Property\StringType) {
                if ($type->getMinLength() !== null || $type->getMaxLength() !== null) {
                    return true;
                }
            }

            if ($type->getPattern() !== null || $type->getEnumeration() !== null) {
                return true;
            }
        }

        return false;
    }
}
