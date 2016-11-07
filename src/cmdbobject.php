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
 * @package net\benjaminheisig\idoitapi
 * @author Benjamin Heisig <https://benjamin.heisig.name/>
 * @copyright Copyright (C) 2016 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

namespace net\benjaminheisig\idoitapi;

/**
 * Requests for API namespace 'cmdb.object'
 */
class CMDBObject extends Request {

    public function create() {
        // @todo Implement it.
    } //function

    /**
     * Reads an object
     *
     * @param int $objectID Object identifier
     *
     * @return array Associative array
     */
    public function read($objectID) {
        return $this->api->request('cmdb.object.read', [
            'id' => $objectID
        ]);
    } //function

    public function update() {
        // @todo Implement it.
    } //function

    public function delete($objectID) {
        // @todo Implement it.
    } //function

    public function batchCreate() {
        // @todo Implement it.
    } //function

    /**
     * Reads objects
     *
     * @param int[] $objectIDs List of object identifiers
     *
     * @return array Index array of associative arrays
     *
     * @throws \Exception
     */
    public function batchRead(array $objectIDs) {
        $requests = [];

        foreach ($objectIDs as $objectID) {
            $requests[] = [
                'method' => 'cmdb.object.read',
                'params' => [
                    'id' => $objectID
                ]
            ];
        } //foreach

        return $this->api->batchRequest($requests);
    } //function

    public function batchUpdate() {
        // @todo Implement it.
    } //function

    public function batchDelete($objectIDs) {
        // @todo Implement it.
    } //function

} //class
