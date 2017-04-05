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
 * Requests for API namespace 'cmdb.workstation_components'
 */
class CMDBWorkstationComponents extends Request {

    /**
     * Reads workplace components for a specific object, for example a person
     *
     * @param int $objectID Object identifier
     *
     * @return array
     *
     * @throws \Exception on error
     */
    public function read($objectID) {
        return $this->api->request(
            'cmdb.workstation_components',
            [
                'filter' => [
                    'id' => $objectID
                ]
            ]
        );
    }

    /**
     * Reads workplace components for one or more objects, for example persons
     *
     * @param int[] $objectIDs List of object identifiers
     *
     * @return array
     *
     * @throws \Exception on error
     */
    public function batchRead(array $objectIDs) {
        return $this->api->request(
            'cmdb.workstation_components',
            [
                'filter' => [
                    'ids' => $objectIDs
                ]
            ]
        );
    }

    /**
     * Reads workplace components for a specific object by its email address, for example a person
     *
     * @param string $email Email address
     *
     * @return array
     *
     * @throws \Exception on error
     */
    public function readByEMail($email) {
        return $this->api->request(
            'cmdb.workstation_components',
            [
                'filter' => [
                    'email' => $email
                ]
            ]
        );
    }

    /**
     * Reads workplace components for one or more objects by their email addresses, for example persons
     *
     * @param string[] $emails List of email addresses
     *
     * @return array
     *
     * @throws \Exception on error
     */
    public function readByEMails(array $emails) {
        return $this->api->request(
            'cmdb.workstation_components',
            [
                'filter' => [
                    'emails' => $emails
                ]
            ]
        );
    }

}
