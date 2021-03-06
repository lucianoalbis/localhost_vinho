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

namespace PSX\Http\Tests;

use PSX\Http\Options;

/**
 * OptionsTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class OptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testOptions()
    {
        $callback = function () {};
        $options  = new Options();

        $options->setCallback($callback);
        $options->setTimeout(3);
        $options->setFollowLocation(true, 4);
        $options->setSsl(true, '/foo/bar.pem');
        $options->setProxy('127.0.0.1:8080');

        $this->assertEquals($callback, $options->getCallback());
        $this->assertEquals(3, $options->getTimeout());
        $this->assertEquals(true, $options->getFollowLocation());
        $this->assertEquals(4, $options->getMaxRedirects());
        $this->assertEquals(true, $options->getSsl());
        $this->assertEquals('/foo/bar.pem', $options->getCaPath());
        $this->assertEquals('127.0.0.1:8080', $options->getProxy());
    }

    public function testOptionsDefault()
    {
        $options = new Options();

        $this->assertEquals(null, $options->getCallback());
        $this->assertEquals(null, $options->getTimeout());
        $this->assertEquals(false, $options->getFollowLocation());
        $this->assertEquals(8, $options->getMaxRedirects());
        $this->assertEquals(false, $options->getSsl());
        $this->assertEquals(null, $options->getCaPath());
        $this->assertEquals(null, $options->getProxy());
    }
}
