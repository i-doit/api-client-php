<?php

/**
 * Copyright (C) 2022 synetics GmbH
 * Copyright (C) 2016-2022 Benjamin Heisig
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Benjamin Heisig <https://benjamin.heisig.name/>
 * @copyright Copyright (C) 2022 synetics GmbH
 * @copyright Copyright (C) 2016-2022 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/i-doit/api-client-php
 */

declare(strict_types=1);

namespace Idoit\APIClient;

use \Exception;

/**
 * Requests for API namespace 'cmdb.object_types'
 */
class CMDBObjectTypes extends Request {

    /**
     * Fetches information about all object types
     *
     * @return array Indexed array of associative arrays
     *
     * @throws Exception on error
     */
    public function read(): array {
        return $this->api->request(
            'cmdb.object_types',
            [
                'countobjects' => true
            ]
        );
    }

    /**
     * Fetches information about an object type by its constant
     *
     * @param string $objectType Object type constant
     *
     * @return array Associative array
     *
     * @throws Exception on error
     */
    public function readOne(string $objectType): array {
        $result = $this->api->request(
            'cmdb.object_types',
            [
                'filter' => [
                    'id' => $objectType
                ],
                'countobjects' => true
            ]
        );

        return end($result);
    }

    /**
     * Fetches information about one or more object types by their constants
     *
     * @param array $objectTypes List of object type constants as strings
     *
     * @return array Indexed array of associative arrays
     *
     * @throws Exception on error
     */
    public function batchRead(array $objectTypes): array {
        return $this->api->request(
            'cmdb.object_types',
            [
                'filter' => [
                    'ids' => $objectTypes
                ],
                'countobjects' => true
            ]
        );
    }

    /**
     * Fetches information about an object type by its title (which could be a "language constant")
     *
     * @param string $title Object title
     *
     * @return array Associative array
     *
     * @throws Exception on error
     */
    public function readByTitle(string $title): array {
        $result = $this->api->request(
            'cmdb.object_types',
            [
                'filter' => [
                    'title' => $title
                ],
                'countobjects' => true
            ]
        );

        return end($result);
    }

}
