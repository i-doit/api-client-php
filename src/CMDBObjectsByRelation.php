<?php

/**
 * Copyright (C) 2016 Benjamin Heisig
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
 * @copyright Copyright (C) 2016 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

namespace bheisig\idoitapi;

/**
 * Requests for API namespace 'cmdb.objects_by_relation'
 */
class CMDBObjectsByRelation extends Request {

    /**
     * Reads object relations by their type identifier
     *
     * @param int $objectID Object identifier
     * @param int $relationType Relation type identifier
     *
     * @return array
     *
     * @throws \Exception on error
     */
    public function readByID($objectID, $relationType) {
        return $this->api->request(
            'cmdb.objects_by_relation.read',
            [
                'id' => $objectID,
                'relation_type' => $relationType
            ]
        );
    }

    /**
     * Reads object relations by their type constant
     *
     * @param int $objectID Object identifier
     * @param string $relationType Relation type constant
     *
     * @return array
     *
     * @throws \Exception on error
     */
    public function readByConst($objectID, $relationType) {
        return $this->api->request(
            'cmdb.objects_by_relation.read',
            [
                'id' => $objectID,
                'relation_type' => $relationType
            ]
        );
    }

}
