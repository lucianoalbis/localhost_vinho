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
 * ArrayTypeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ArrayTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testSetPrototype()
    {
        $prototype = Property::getString('foo');

        $property = Property::getArray('test');
        $property->setPrototype($prototype);

        $this->assertEquals($prototype, $property->getPrototype());
    }

    public function testGetId()
    {
        $property = Property::getArray('test');

        $this->assertEquals('d41d8cd98f00b204e9800998ecf8427e', $property->getId());

        $property = Property::getArray('test');
        $property->setPrototype(Property::getString('foo'));

        $this->assertEquals('2b356789c31243c991eae91be7ffe756', $property->getId());
    }

    public function testGetTypeName()
    {
        $this->assertEquals('array', Property::getArray('test')->getTypeName());
    }
}
