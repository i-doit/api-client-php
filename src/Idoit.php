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
use \RuntimeException;

/**
 * Requests for API namespace 'idoit'
 */
class Idoit extends Request {

    const NORMAL_SEARCH = 'normal';
    const DEEP_SEARCH = 'deep';
    const AUTO_DEEP_SEARCH = 'auto-deep';

    /**
     * Read information about i-doit
     *
     * @return array Associative array
     *
     * @throws Exception on error
     */
    public function readVersion(): array {
        return $this->api->request('idoit.version');
    }

    /**
     * Read information about installed add-ons
     *
     * @return array Associative array
     *
     * @throws Exception on error
     */
    public function getAddOns(): array {
        $response = $this->api->request('idoit.addons.read');

        if (!array_key_exists('result', $response) || !is_array($response['result'])) {
            throw new RuntimeException('Bad result');
        }

        return $response['result'];
    }

    /**
     * Read license information
     *
     * @return array Associative array
     *
     * @throws Exception on error
     */
    public function getLicense(): array {
        return $this->api->request('idoit.license.read');
    }

    /**
     * Read list of defined constants
     *
     * @return array Associative array
     *
     * @throws Exception on error
     */
    public function readConstants(): array {
        return $this->api->request('idoit.constants');
    }

    /**
     * Search i-doit's database
     *
     * @param string $query Query
     * @param string $mode Search mode: "normal" (default), "deep" and "auto-deep"
     *
     * @return array Search results
     *
     * @throws Exception on error
     */
    public function search(string $query, string $mode = self::NORMAL_SEARCH): array {
        return $this->api->request(
            'idoit.search',
            [
                'q' => $query,
                'mode' => $mode
            ]
        );
    }

    /**
     * Perform one or more searches at once
     *
     * @param array $queries Queries as strings
     * @param string $mode Search mode: "normal" (default), "deep" and "auto-deep"
     *
     * @return array Search results
     *
     * @throws Exception on error
     */
    public function batchSearch(array $queries, string $mode = self::NORMAL_SEARCH): array {
        $requests = [];

        foreach ($queries as $query) {
            $requests[] = [
                'method' => 'idoit.search',
                'params' => [
                    'q' => $query,
                    'mode' => $mode
                ]
            ];
        }

        return $this->api->batchRequest($requests);
    }

}
