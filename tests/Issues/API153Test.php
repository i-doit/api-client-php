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

declare(strict_types=1);

namespace bheisig\idoitapi\tests\Issues;

use bheisig\idoitapi\tests\BaseTest;
use bheisig\idoitapi\API;

/**
 * @group issues
 * @group API-153
 * @group open
 * @group unreleased
 * @see https://i-doit.atlassian.net/browse/API-153
 */
class API153Test extends BaseTest {

    /**
     * Reset setup to manipulate username/password
     *
     * @throws \Exception on error
     */
    public function setUp() {
        // Do nothing.
    }

    /**
     * @return array
     */
    public function provideCredentials(): array {
        return [
            'SQL injection #1 in username' => [
                "\' abc",
                ""
            ],
            'SQL injection #2 in username' => [
                "' abc",
                ""
            ],
            'SQL injection #3 in username' => [
                "\" abc",
                ""
            ],
            'SQL injection #1 in password' => [
                "",
                "\' abc"
            ],
            'SQL injection #2 in password' => [
                "",
                "' abc"
            ],
            'SQL injection #3 in password' => [
                "",
                "\" abc"
            ]
        ];
    }

    /**
     * @return array
     */
    public function provideAPIKeys(): array {
        return [
            'SQL injection #1 in API key' => [
                "\' abc"
            ],
            'SQL injection #2 in API key' => [
                "' abc"
            ],
            'SQL injection #3 in API key' => [
                "\" abc"
            ]
        ];
    }

    /**
     * @param string $username Username
     * @param string $password Password
     * @dataProvider provideCredentials
     * @throws \Exception on error
     */
    public function testFaultyCredentials(string $username, string $password) {
        if (empty($username)) {
            $username = getenv('USERNAME');
        }

        if (empty($password)) {
            $password = getenv('PASSWORD');
        }

        $config = [
            API::USERNAME => $username,
            API::PASSWORD => $password,
            API::KEY => getenv('KEY'),
            API::URL => getenv('URL'),
            API::LANGUAGE => getenv('IDOIT_LANGUAGE')
        ];

        $this->api = new API($config);

        $request = [
            'jsonrpc' => '2.0',
            'method' => 'idoit.version',
            'params' => [
                'apikey' => getenv('KEY')
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
        $this->isError($response);
        $this->hasValidJSONRPCIdentifier($request, $response);
        $this->assertSame(-32604, $response['error']['code']);
    }

    /**
     * @param string $apiKey API key
     * @dataProvider provideAPIKeys
     * @throws \Exception on error
     */
    public function testFaultyKey(string $apiKey) {
        $config = [
            API::KEY => $apiKey,
            API::USERNAME => getenv('USERNAME'),
            API::PASSWORD => getenv('PASSWORD'),
            API::URL => getenv('URL'),
            API::LANGUAGE => getenv('IDOIT_LANGUAGE')
        ];

        $this->api = new API($config);

        $request = [
            'jsonrpc' => '2.0',
            'method' => 'idoit.version',
            'params' => [
                'apikey' => $apiKey
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
        $this->isError($response);
        $this->hasValidJSONRPCIdentifier($request, $response);
        $this->assertSame(-32099, $response['error']['code']);
    }

}
