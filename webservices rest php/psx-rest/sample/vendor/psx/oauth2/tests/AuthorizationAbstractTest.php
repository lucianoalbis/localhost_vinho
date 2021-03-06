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

namespace PSX\Oauth2\Tests;

use PSX\Oauth2\AuthorizationAbstract;

/**
 * AuthorizationAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class AuthorizationAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \PSX\Oauth2\Authorization\Exception\InvalidRequestException
     */
    public function testNormalErrorException()
    {
        AuthorizationAbstract::throwErrorException(array(
            'error' => 'invalid_request',
            'error_description' => 'Foobar',
            'error_uri' => 'http://foo.bar'
        ));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testEmptyErrorException()
    {
        AuthorizationAbstract::throwErrorException('');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testUnknownErrorException()
    {
        AuthorizationAbstract::throwErrorException(array(
            'error' => 'foobar',
        ));
    }

    /**
     *
     * @expectedException \PSX\Oauth2\Authorization\Exception\InvalidRequestException
     */
    public function testFacebookErrorException()
    {
        AuthorizationAbstract::throwErrorException(array(
            'error' => array(
                'message' => 'Message describing the error',
                'type' => 'OAuthException',
                'code' => 190,
                'error_subcode' => 460
            )
        ));
    }
}
