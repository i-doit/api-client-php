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
 * Requests for API namespace 'cmdb.logbook'
 */
class CMDBLogbook extends Request {

    /**
     * Create a new logbook entry
     *
     * @param int $objectID Object identifier
     * @param string $message Message
     * @param string $description (optional) Description
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function create(int $objectID, string $message, string $description = null): self {
        $params = [
            'object_id' => $objectID,
            'message' => $message
        ];

        if (isset($description)) {
            $params['description'] = $description;
        }

        $result = $this->api->request(
            'cmdb.logbook.create',
            $params
        );

        $this->requireSuccessWithoutIdentifier($result);

        return $this;
    }

    /**
     * Create one or more logbook entries for a specific object
     *
     * @param int $objectID Object identifier
     * @param array $messages List of messages as strings
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function batchCreate(int $objectID, array $messages): self {
        $requests = [];

        foreach ($messages as $message) {
            $requests[] = [
                'method' => 'cmdb.logbook.create',
                'params' => [
                    'object_id' => $objectID,
                    'message' => $message
                ]
            ];
        }

        $this->api->batchRequest($requests);

        return $this;
    }

    /**
     * Fetch all logbook entries
     *
     * @param string $since Optional list only entries since a specific date; supports everything which can be parsed
     * by strtotime()
     * @param int $limit Limit number of entries; defaults to 1000
     *
     * @return array Indexed array of associative arrays
     *
     * @throws Exception on error
     */
    public function read(string $since = null, int $limit = 1000): array {
        $params = [
            'limit' => $limit
        ];

        if (isset($since)) {
            $params['since'] = $since;
        }

        return $this->api->request(
            'cmdb.logbook.read',
            $params
        );
    }

    /**
     * Fetch all logbook entries for a specific object
     *
     * @param int $objectID Object identifier
     * @param string $since Optional list only entries since a specific date; supports everything which can be parsed by
     * strtotime()
     * @param int $limit Limit number of entries; defaults to 1000
     *
     * @return array Indexed array of associative arrays
     *
     * @throws Exception on error
     */
    public function readByObject(int $objectID, string $since = null, int $limit = 1000): array {
        $params = [
            'object_id' => $objectID,
            'limit' => $limit
        ];

        if (isset($since)) {
            $params['since'] = $since;
        }

        return $this->api->request(
            'cmdb.logbook.read',
            $params
        );
    }

}
