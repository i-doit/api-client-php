<?php

/**
 * Copyright (C) 2016-17 Benjamin Heisig
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
 * @copyright Copyright (C) 2016-17 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

namespace bheisig\idoitapi;

/**
 * Requests for API namespace 'cmdb.object_type_categories'
 */
class CMDBObjectTypeCategories extends Request {

    /**
     * Fetches assigned categories for a specific object type by its identifier
     *
     * @param int $objectTypeID Object type identifier
     *
     * @return array
     *
     * @throws \Exception on error
     */
    public function readByID($objectTypeID) {
        return $this->api->request(
            'cmdb.object_type_categories.read',
            [
                'type' => $objectTypeID
            ]
        );
    }

    /**
     * Fetches assigned categories for a specific object type by its constant
     *
     * @param string $objectTypeConst Object type constant
     *
     * @return array
     *
     * @throws \Exception on error
     */
    public function readByConst($objectTypeConst) {
        return $this->api->request(
            'cmdb.object_type_categories.read',
            [
                'type' => $objectTypeConst
            ]
        );
    }

    /**
     * Fetches assigned categories for one or more objects types at once identified by their identifiers
     *
     * @param int[] $objectTypeIDs List of object types identifiers
     *
     * @return array
     *
     * @throws \Exception on error
     */
    public function batchReadByID(array $objectTypeIDs) {
        $requests = [];

        foreach ($objectTypeIDs as $objectTypeID) {
            $requests[] = [
                'method' => 'cmdb.object_type_categories.read',
                'params' => [
                    'type' => $objectTypeID
                ]
            ];
        }

        return $this->api->batchRequest($requests);
    }

    /**
     * Fetches assigned categories for one or more objects types at once identified by their constants
     *
     * @param string[] $objectTypeConsts List of object types constants
     *
     * @return array
     *
     * @throws \Exception on error
     */
    public function batchReadByConst(array $objectTypeConsts) {
        $requests = [];

        foreach ($objectTypeConsts as $objectTypeConst) {
            $requests[] = [
                'method' => 'cmdb.object_type_categories.read',
                'params' => [
                    'type' => $objectTypeConst
                ]
            ];
        }

        return $this->api->batchRequest($requests);
    }

}
