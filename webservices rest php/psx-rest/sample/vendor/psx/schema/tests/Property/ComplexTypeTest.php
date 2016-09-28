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
 * ComplexTypeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ComplexTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetId()
    {
        $property = Property::getComplex('test')
            ->add('foo', Property::getString())
            ->add('bar', Property::getString());

        $this->assertEquals('cd6b6757bad65f214ca0928a3db48c27', $property->getId());
    }

    public function testProperties()
    {
        $property = Property::getComplex('test')
            ->add('foo', Property::getString())
            ->add('bar', Property::getString());

        $this->assertInstanceOf('PSX\Schema\Property\StringType', $property->get('foo'));
        $this->assertTrue($property->has('foo'));

        $property->remove('foo');
        $property->remove('foo'); // should not produce an error

        $this->assertFalse($property->has('foo'));
    }

    public function testGetTypeName()
    {
        $this->assertEquals('complex', Property::getComplex('test')->getTypeName());
    }
}
