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
 * Requests for API namespace 'cmdb.logbook'
 */
class CMDBLogbook extends Request {

    /**
     * Creates a new logbook entry
     *
     * @param int $objectID Object identifier
     * @param string $message Message
     * @param string $description (optional) Description
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function create($objectID, $message, $description = null) {
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

        if (!array_key_exists('success', $result) ||
            $result['success'] !== true) {
            throw new \Exception('Bad result');
        }

        return $this;
    }

    /**
     * Creates one or more logbook entries for a specific object
     *
     * @param int $objectID Object identifier
     * @param string[] $messages List of messages
     *
     * @return self Returns itself
     *
     * @throws \Exception on error
     */
    public function batchCreate($objectID, array $messages) {
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
     * Fetches all logbook entries
     *
     * @param string $since (optional) List only entries since a specific date; supports everything which can be parsed by strtotime()
     *
     * @return array Indexed array of associative arrays
     *
     * @throws \Exception on error
     */
    public function read($since = null) {
        $params = [];

        if (isset($since)) {
            $params['since'] = $since;
        }

        return $this->api->request(
            'cmdb.logbook.read',
            $params
        );
    }

    /**
     * Fetches all logbook entries for a specific object
     *
     * @param int $objectID Object identifier
     * @param string $since (optional) List only entries since a specific date; supports everything which can be parsed by strtotime()
     *
     * @return array Indexed array of associative arrays
     *
     * @throws \Exception on error
     */
    public function readByObject($objectID, $since = null) {
        $params = [
            'object_id' => $objectID
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
