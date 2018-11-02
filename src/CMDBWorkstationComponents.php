<?php

/**
 * Copyright (C) 2016-18 Benjamin Heisig
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
 * @copyright Copyright (C) 2016-18 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

namespace bheisig\idoitapi;

/**
 * Requests for API namespace 'cmdb.workstation_components'
 */
class CMDBWorkstationComponents extends Request {

    /**
     * Reads workplace components for a specific object, for example a person
     *
     * @param int $objectID Object identifier
     * @param int $status Filter relations by status: 2 = normal, 3 = archived, 4 = deleted
     *
     * @return array
     *
     * @throws \Exception on error
     */
    public function read($objectID, $status = null) {
        $params = [
            'filter' => [
                'id' => $objectID
            ]
        ];

        if (isset($status)) {
            $params['filter']['status'] = $status;
        }

        return $this->api->request(
            'cmdb.workstation_components',
            $params
        );
    }

    /**
     * Reads workplace components for one or more objects, for example persons
     *
     * @param array $objectIDs List of object identifiers as integers
     * @param int $status Filter relations by status: 2 = normal, 3 = archived, 4 = deleted
     *
     * @return array Result
     *
     * @throws \Exception on error
     */
    public function batchRead(array $objectIDs, $status = null) {
        $params = [
            'filter' => [
                'ids' => $objectIDs
            ]
        ];

        if (isset($status)) {
            $params['filter']['status'] = $status;
        }

        return $this->api->request(
            'cmdb.workstation_components',
            $params
        );
    }

    /**
     * Reads workplace components for a specific object by its e-mail address, for example a person
     *
     * @param string $email E-mail address
     * @param int $status Filter relations by status: 2 = normal, 3 = archived, 4 = deleted
     *
     * @return array
     *
     * @throws \Exception on error
     */
    public function readByEmail($email, $status = null) {
        $params = [
            'filter' => [
                'email' => $email
            ]
        ];

        if (isset($status)) {
            $params['filter']['status'] = $status;
        }

        return $this->api->request(
            'cmdb.workstation_components',
            $params
        );
    }

    /**
     * Reads workplace components for one or more objects by their e-mail addresses, for example persons
     *
     * @param array $emails List of e-mail addresses as strings
     * @param int $status Filter relations by status: 2 = normal, 3 = archived, 4 = deleted
     *
     * @return array
     *
     * @throws \Exception on error
     */
    public function readByEmails(array $emails, $status = null) {
        $params = [
            'filter' => [
                'emails' => $emails
            ]
        ];

        if (isset($status)) {
            $params['filter']['status'] = $status;
        }

        return $this->api->request(
            'cmdb.workstation_components',
            $params
        );
    }

}
