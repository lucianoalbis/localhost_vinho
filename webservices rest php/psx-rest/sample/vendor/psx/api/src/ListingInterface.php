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

namespace PSX\Api;

/**
 * A listing knows all API endpoints in a system and can be used to get resource
 * definitions for specific endpoints or to get an index of all available
 * endpoints
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link	http://phpsx.org
 */
interface ListingInterface
{
    /**
     * Returns all available resources. Note the index does not contain any
     * documentation it contains only the path and the available request methods
     *
     * @return \PSX\Api\Resource[]
     */
    public function getResourceIndex();

    /**
     * Returns a specific resource with complete documentation or null if the
     * resource was not found
     *
     * @param string $sourcePath
     * @param string $version
     * @return \PSX\Api\Resource
     */
    public function getResource($sourcePath, $version = null);
}
