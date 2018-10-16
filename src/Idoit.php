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
 * Requests for API namespace 'idoit'
 */
class Idoit extends Request {

    /**
     * Read information about i-doit
     *
     * @return array Associative array
     *
     * @throws \Exception on error
     */
    public function readVersion() {
        return $this->api->request('idoit.version');
    }

    /**
     * Read information about installed add-ons
     *
     * @return array Associative array
     *
     * @throws \Exception on error
     */
    public function getAddOns() {
        return $this->api->request('idoit.addons.read');
    }

    /**
     * Read list of defined constants
     *
     * @return array Associative array
     *
     * @throws \Exception on error
     */
    public function readConstants() {
        return $this->api->request('idoit.constants');
    }

    /**
     * Search i-doit's database
     *
     * @param string $query Query
     *
     * @return array Search results
     *
     * @throws \Exception on error
     */
    public function search($query) {
        return $this->api->request(
            'idoit.search',
            ['q' => $query]
        );
    }

    /**
     * Perform one or more searches at once
     *
     * @param array $queries Queries as strings
     *
     * @return array Search results
     *
     * @throws \Exception on error
     */
    public function batchSearch(array $queries) {
        $requests = [];

        foreach ($queries as $query) {
            $requests[] = [
                'method' => 'idoit.search',
                'params' => [
                    'q' => $query
                ]
            ];
        }

        return $this->api->batchRequest($requests);
    }

}
