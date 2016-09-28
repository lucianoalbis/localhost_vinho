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

namespace PSX\Schema\Tests;

use PSX\Schema\Property;
use PSX\Schema\SchemaAbstract;

/**
 * TestSchema
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TestSchema extends SchemaAbstract
{
    public function getDefinition()
    {
        $sb = $this->getSchemaBuilder('location')
            ->setAdditionalProperties(true)
            ->setDescription('Location of the person');
        $sb->float('lat');
        $sb->float('long');
        $location = $sb->getProperty();

        $sb = $this->getSchemaBuilder('web')
            ->setAdditionalProperties(Property::getString('value'))
            ->setMinProperties(2)
            ->setMaxProperties(8)
            ->setDescription('An application');
        $sb->string('name');
        $sb->string('url');
        $web = $sb->getProperty();

        $sb = $this->getSchemaBuilder('author')
            ->setDescription('An simple author element with some description');
        $sb->string('title')
            ->setPattern('[A-z]{3,16}')
            ->setRequired(true);
        $sb->string('email')
            ->setDescription('We will send no spam to this addresss');
        $sb->arrayType('categories')
            ->setPrototype(Property::getString('category'))
            ->setMaxItems(8);
        $sb->arrayType('locations')
            ->setPrototype($location)
            ->setDescription('Array of locations');
        $sb->complexType('origin', $location);
        $author = $sb->getProperty();

        $sb = $this->getSchemaBuilder('meta')
            ->setDescription('Some meta data')
            ->addPatternProperty('^tags_\d$', Property::getString('tag'))
            ->addPatternProperty('^location_\d$', $location);
        $sb->dateTime('createDate');
        $meta = $sb->getProperty();

        $sb = $this->getSchemaBuilder('news')
            ->setDescription('An general news entry');
        $sb->complexType('config')
            ->setAdditionalProperties(Property::getString('value'));
        $sb->arrayType('tags')
            ->setPrototype(Property::getString('tag'))
            ->setMinItems(1)
            ->setMaxItems(6);
        $sb->arrayType('receiver')
            ->setPrototype($author)
            ->setMinItems(1)
            ->setRequired(true);
        $sb->arrayType('resources') 
            ->setPrototype(Property::getChoice('resource')
                ->add($location)
                ->add($web)
            );
        $sb->binary('profileImage');
        $sb->boolean('read');
        $sb->choiceType('source')
            ->add($author)
            ->add($web);
        $sb->complexType('author', $author);
        $sb->complexType('meta', $meta);
        $sb->date('sendDate');
        $sb->dateTime('readDate');
        $sb->duration('expires');
        $sb->float('price')
            ->setMin(1)
            ->setMax(100)
            ->setRequired(true);
        $sb->integer('rating')
            ->setMin(1)
            ->setMax(5);
        $sb->string('content')
            ->setDescription('Contains the main content of the news entry')
            ->setMinLength(3)
            ->setMaxLength(512)
            ->setRequired(true);
        $sb->string('question')
            ->setEnumeration(['foo', 'bar']);
        $sb->time('coffeeTime');
        $sb->uri('profileUri');

        return $sb->getProperty();
    }
}
