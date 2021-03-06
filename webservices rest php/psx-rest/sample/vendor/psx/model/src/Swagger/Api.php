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

namespace PSX\Model\Swagger;

use PSX\Record\Record;

/**
 * Api
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Api
{
    /**
     * @Type("string")
     * @Required
     */
    protected $path;

    /**
     * @Type("string")
     */
    protected $description;

    /**
     * @Type("array<PSX\Model\Swagger\Operation>")
     * @Required
     */
    protected $operations = array();

    public function __construct($path = null, $description = null)
    {
        $this->path        = $path;
        $this->description = $description;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setOperations(array $operations)
    {
        $this->operations = $operations;
    }

    public function getOperations()
    {
        return $this->operations;
    }

    public function addOperation(Operation $operation)
    {
        $this->operations[] = $operation;
    }
}
