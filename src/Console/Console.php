<?php

/**
 * Copyright (C) 2016-19 Benjamin Heisig
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
 * @copyright Copyright (C) 2016-19 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

namespace bheisig\idoitapi\Console;

use \Exception;
use \RuntimeException;
use \bheisig\idoitapi\Request;

/**
 * Requests for API namespace 'console'
 */
class Console extends Request {

    /**
     * Execute command
     *
     * @param string $method Method name
     * @param array $options List of options as key-value store
     * @param array $arguments List of arguments as strings
     *
     * @return array Output (one value per line)
     *
     * @throws Exception on error
     */
    public function execute($method, array $options = [], array $arguments = []) {
        $params = [];

        if (count($options) > 0) {
            $params['options'] = $options;
        }

        if (count($arguments) > 0) {
            $params['arguments'] = $arguments;
        }

        $result = $this->api->request(
            $method,
            $params
        );

        if (!array_key_exists('success', $result)) {
            throw new RuntimeException('Missing success status');
        }

        if (!is_bool($result['success'])) {
            throw new RuntimeException('Command failed');
        }

        if ($result['success'] === false) {
            if((str_contains($method, 'console.ldap.sync') || str_contains($method, 'console.import.csv')
                || str_contains($method, 'console.import.syslog')) === false)
            {
                throw new RuntimeException('Command failed');
            }
        }

        if (!array_key_exists('output', $result)) {
            throw new RuntimeException('Missing output');
        }

        if (!is_array($result['output'])) {
            throw new RuntimeException('Invalid output');
        }

        return $result['output'];
    }

}
