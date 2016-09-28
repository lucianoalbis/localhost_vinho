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

namespace PSX\Schema\Tests\Property;

use PSX\Schema\Property;

/**
 * ChoiceTypeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ChoiceTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testAddElement()
    {
        $complexFoo = Property::getComplex('foo')
            ->add('foo', Property::getInteger()->setRequired(true))
            ->add('bar', Property::getInteger()->setRequired(true));

        $property = Property::getChoice('foo');
        $property->add($complexFoo);

        $this->assertEquals([0 => $complexFoo], $property->getChoices());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddElementInvalid()
    {
        Property::getChoice('foo')
            ->add(Property::getInteger('foo'));
    }

    public function testGetId()
    {
        $property = Property::getChoice('test');

        $this->assertEquals('19d10a66f9b86790312047232da1ccd2', $property->getId());

        $complexFoo = Property::getComplex('foo')
            ->add('foo', Property::getInteger()->setRequired(true))
            ->add('bar', Property::getInteger()->setRequired(true));
        $complexBar = Property::getComplex('bar')
            ->add('foo', Property::getInteger()->setRequired(true))
            ->add('baz', Property::getInteger()->setRequired(true));

        $property = Property::getChoice('test')->add($complexFoo)->add($complexBar);

        $this->assertEquals('99b2fdc6f3bbeed0cd5eb73a52429c18', $property->getId());
    }

    public function testGetTypeName()
    {
        $this->assertEquals('choice', Property::getChoice('test')->getTypeName());
    }
    
    public function testGetChoice()
    {
        $complexFoo = Property::getComplex('foo')
            ->setReference('Foo\Bar')
            ->add('foo', Property::getInteger()->setRequired(true))
            ->add('bar', Property::getInteger()->setRequired(true));
        $complexBar = Property::getComplex('bar')
            ->setReference('Foo\Baz')
            ->add('foo', Property::getInteger()->setRequired(true))
            ->add('baz', Property::getInteger()->setRequired(true));

        $property = Property::getChoice('test')->add($complexFoo)->add($complexBar);

        $this->assertEquals($complexFoo, $property->getChoice(['foo' => 'test', 'bar' => 'test'], '/'));
        $this->assertEquals(['foo' => 'Foo\Bar', 'bar' => 'Foo\Baz'], $property->getChoiceTypes());
    }

    public function testGetChoiceInvalid()
    {
        $complexFoo = Property::getComplex('foo')
            ->setReference('Foo\Bar')
            ->add('foo', Property::getInteger()->setRequired(true))
            ->add('bar', Property::getInteger()->setRequired(true));
        $complexBar = Property::getComplex('bar')
            ->setReference('Foo\Baz')
            ->add('foo', Property::getInteger()->setRequired(true))
            ->add('baz', Property::getInteger()->setRequired(true));

        $property = Property::getChoice('test')->add($complexFoo)->add($complexBar);

        $this->assertEquals(null, $property->getChoice(['foo' => 'test', 'test' => 'test'], '/'));
        $this->assertEquals(['foo' => 'Foo\Bar', 'bar' => 'Foo\Baz'], $property->getChoiceTypes());
    }
}
