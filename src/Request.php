<?php

/**
 * Copyright (C) 2016-2020 Benjamin Heisig
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
 * @copyright Copyright (C) 2016-2020 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

declare(strict_types=1);

namespace bheisig\idoitapi;

use RuntimeException;

/**
 * Request
 */
abstract class Request implements Calls {

    /**
     * API client
     *
     * @var API
     */
    protected $api;

    /**
     * Constructor
     *
     * @param API $api API client
     */
    public function __construct(API $api) {
        $this->api = $api;
    }

    /**
     * Check for success and return identifier
     *
     * @param array $result Response from API request
     *
     * @return int Identifier
     *
     * @throws RuntimeException on error
     */
    protected function requireSuccessFor(array $result): int {
        if (!array_key_exists('id', $result) ||
            !is_numeric($result['id']) ||
            !array_key_exists('success', $result) ||
            $result['success'] !== true) {
            if (array_key_exists('message', $result)) {
                throw new RuntimeException(sprintf('Bad result: %s', $result['message']));
            } else {
                throw new RuntimeException('Bad result');
            }
        }

        return (int) $result['id'];
    }

    /**
     * Check for success but ignore identifier
     *
     * @param array $result Result
     *
     * @throws RuntimeException on error
     */
    protected function requireSuccessWithoutIdentifier(array $result) {
        if (!array_key_exists('success', $result) ||
            $result['success'] !== true) {
            if (array_key_exists('message', $result)) {
                throw new RuntimeException(sprintf('Bad result: %s', $result['message']));
            } else {
                throw new RuntimeException('Bad result');
            }
        }
    }

    /**
     * Check whether each request in a batch was successful
     *
     * @param array $results Results
     *
     * @throws RuntimeException on error
     */
    protected function requireSuccessforAll(array $results) {
        foreach ($results as $result) {
            // Do not check 'id' because in a batch request it is always NULL:
            $this->requireSuccessWithoutIdentifier($result);
        }
    }

}
