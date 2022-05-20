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
 * Requests for API namespace 'cmdb.impact'
 */
class CMDBImpact extends Request {

    /**
     * Perform an impact analysis for a specific object by its relation type identifier
     *
     * @param int $objectID Object identifier
     * @param int $relationType Relation type identifier
     * @param int $status Filter relations by status: 2 = normal, 3 = archived, 4 = deleted
     *
     * @return array
     *
     * @throws Exception on error
     */
    public function readByID(int $objectID, int $relationType, int $status = null): array {
        $params = [
            'id' => $objectID,
            'relation_type' => $relationType
        ];

        if (isset($status)) {
            $params['status'] = $status;
        }

        return $this->api->request(
            'cmdb.impact.read',
            $params
        );
    }

    /**
     * Perform an impact analysis for a specific object by its relation type constant
     *
     * @param int $objectID Object identifier
     * @param string $relationType Relation type constant
     * @param int $status Filter relations by status: 2 = normal, 3 = archived, 4 = deleted
     *
     * @return array
     *
     * @throws Exception on error
     */
    public function readByConst(int $objectID, string $relationType, int $status = null): array {
        $params = [
            'id' => $objectID,
            'relation_type' => $relationType
        ];

        if (isset($status)) {
            $params['status'] = $status;
        }

        return $this->api->request(
            'cmdb.impact.read',
            $params
        );
    }

    /**
     * Perform an impact analysis for a specific object by one ore more relation type constant or identifiers
     *
     * @param int $objectID Object identifier
     * @param array $relationTypes List of relation type constants as strings or identifiers as integers
     * @param int $status Filter relations by status: 2 = normal, 3 = archived, 4 = deleted
     *
     * @return array
     *
     * @throws Exception on error
     */
    public function readByTypes(int $objectID, array $relationTypes, int $status = null): array {
        $params = [
            'id' => $objectID,
            'relation_type' => $relationTypes
        ];

        if (isset($status)) {
            $params['status'] = $status;
        }

        return $this->api->request(
            'cmdb.impact.read',
            $params
        );
    }

}
