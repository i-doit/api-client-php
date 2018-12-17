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

declare(strict_types=1);

namespace bheisig\idoitapi\tests;

use bheisig\idoitapi\API;

class APITest extends BaseTest {

    /**
     * @covers \bheisig\idoitapi\API::__construct
     * @covers \bheisig\idoitapi\API::testConfig
     * @throws \Exception
     */
    public function testConstructor() {
        // Minimal config:
        $api = new API([
            API::URL => 'https://example.com/src/json.rpc',
            API::KEY => $this->generateRandomString()
        ]);

        $this->assertInstanceOf(API::class, $api);
    }

    /**
     * @covers \bheisig\idoitapi\API::__construct
     * @covers \bheisig\idoitapi\API::testConfig
     * @covers \bheisig\idoitapi\API::__destruct
     * @throws \Exception
     * @doesNotPerformAssertions
     */
    public function testDestructor() {
        // Minimal config:
        $api = new API([
            API::URL => 'https://example.com/src/json.rpc',
            API::KEY => $this->generateRandomString()
        ]);

        unset($api);
    }

    /**
     * @covers \bheisig\idoitapi\API::connect
     * @throws \Exception
     */
    public function testConnect() {
        $this->assertInstanceOf(API::class, $this->api->connect());
    }

    /**
     * @covers \bheisig\idoitapi\API::connect
     * @covers \bheisig\idoitapi\API::disconnect
     * @throws \Exception
     */
    public function testDisconnect() {
        $this->api->connect();

        $this->assertInstanceOf(API::class, $this->api->disconnect());
    }

    /**
     * @covers \bheisig\idoitapi\API::isConnected
     * @covers \bheisig\idoitapi\API::connect
     * @covers \bheisig\idoitapi\API::disconnect
     * @throws \Exception
     */
    public function testIsConnected() {
        $this->assertFalse($this->api->isConnected());

        $this->api->connect();

        $this->assertTrue($this->api->isConnected());

        $this->api->disconnect();

        $this->assertFalse($this->api->isConnected());
    }

    /**
     * @covers \bheisig\idoitapi\API::login
     * @throws \Exception
     */
    public function testLogin() {
        $this->assertInstanceOf(API::class, $this->api->login());
    }

    /**
     * @covers \bheisig\idoitapi\API::login
     * @covers \bheisig\idoitapi\API::logout
     * @throws \Exception
     */
    public function testLogout() {
        $this->api->login();

        $this->assertInstanceOf(API::class, $this->api->logout());
    }

    /**
     * @covers \bheisig\idoitapi\API::isLoggedIn
     * @covers \bheisig\idoitapi\API::login
     * @covers \bheisig\idoitapi\API::logout
     * @throws \Exception
     */
    public function testIsLoggedIn() {
        $this->assertFalse($this->api->isLoggedIn());

        $this->api->login();

        $this->assertTrue($this->api->isLoggedIn());

        $this->api->logout();

        $this->assertFalse($this->api->isLoggedIn());
    }

    /**
     * @covers \bheisig\idoitapi\API::request
     * @covers \bheisig\idoitapi\API::setCURLOptions
     * @covers \bheisig\idoitapi\API::evaluateResponse
     * @throws \Exception
     */
    public function testRequest() {
        $result = $this->api->request('idoit.version');

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
    }

    /**
     * @covers \bheisig\idoitapi\API::request
     * @covers \bheisig\idoitapi\API::countRequests
     * @covers \bheisig\idoitapi\API::genID
     * @throws \Exception
     */
    public function testCountRequests() {
        $this->api->request('idoit.version');

        $count = $this->api->countRequests();

        $this->assertInternalType('integer', $count);
        $this->assertSame(1, $count);

        $this->api->request('idoit.version');

        $count = $this->api->countRequests();

        $this->assertInternalType('integer', $count);
        $this->assertSame(2, $count);
    }

    /**
     * @covers \bheisig\idoitapi\API::batchRequest
     * @throws \Exception
     */
    public function testBatchRequest() {
        $objectID = $this->createServer();

        $results = $this->api->batchRequest([
            [
                'method' => 'idoit.version'
            ],
            [
                'method' => 'cmdb.object.read',
                'params' => ['id' => $objectID]
            ]
        ]);

        $this->assertInternalType('array', $results);
        $this->assertCount(2, $results);

        foreach ($results as $result) {
            $this->assertInternalType('array', $result);
            $this->assertNotCount(0, $result);
        }
    }

    /**
     * @covers \bheisig\idoitapi\API::batchRequest
     * @throws \Exception
     */
    public function testOneRequestInABatch() {
        $results = $this->api->batchRequest([
            [
                'method' => 'idoit.version'
            ]
        ]);

        $this->assertInternalType('array', $results);
        $this->assertCount(1, $results);
        $this->assertArrayHasKey(0, $results);
        $this->assertInternalType('array', $results[0]);
        $this->assertNotCount(0, $results[0]);
    }

    /**
     * @covers \bheisig\idoitapi\API::rawRequest
     * @throws \Exception on error
     */
    public function testRawRequest() {
        $request = [
            'jsonrpc' => '2.0',
            'method' => 'idoit.version',
            'params' => [
                'apikey' => getenv('KEY')
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertInternalType('array', $response);
        $this->isValidResponse($response, $request);
        $this->assertInternalType('array', $response['result']);
    }

    /**
     * @covers \bheisig\idoitapi\API::rawRequest
     * @covers \bheisig\idoitapi\API::getLastRequestHeaders
     * @throws \Exception on error
     */
    public function testRequestWithAdditionalHeaders() {
        $request = [
            'jsonrpc' => '2.0',
            'method' => 'idoit.version',
            'params' => [
                'apikey' => getenv('KEY')
            ],
            'id' => 1
        ];

        $headers = [
            'X-Test-Header' => 'ABC123'
        ];

        $response = $this->api->rawRequest($request, $headers);

        $requestHeaders = explode("\r\n", $this->api->getLastRequestHeaders());
        $this->assertContains('X-Test-Header: ABC123', $requestHeaders);

        // Additional checks:
        $this->assertInternalType('array', $response);
        $this->isValidResponse($response, $request);
        $this->assertInternalType('array', $response['result']);
    }

    /**
     * @return array
     */
    public function provideInvalidRequests(): array {
        return [
            'empty' => [
                []
            ]
        ];
    }

    /**
     * @return array
     */
    public function provideInvalidBatch(): array {
        return [
            'array with integer' => [
                [1]
            ],
            'array with list of integers' => [
                [1, 2, 3]
            ]
        ];
    }

    /**
     * @covers \bheisig\idoitapi\API::rawRequest
     * @throws \Exception on error
     * @dataProvider provideInvalidRequests
     * @param mixed $request Invalid request
     */
    public function testInvalidRequest($request) {
        $response = $this->api->rawRequest($request);

        $this->assertInternalType('array', $response);

        $this->isError($response);
        $this->assertNull($response['id']);
        $this->assertSame(-32600, $response['error']['code']);
    }

    /**
     * @covers \bheisig\idoitapi\API::rawRequest
     * @throws \Exception on error
     * @dataProvider provideInvalidBatch
     * @param mixed $request Invalid batch request
     */
    public function testInvalidBatchRequest($request) {
        $response = $this->api->rawRequest($request);

        $this->assertInternalType('array', $response);

        foreach ($response as $result) {
            $this->isError($result);
            $this->assertNull($result['id']);
            $this->assertSame(-32600, $result['error']['code']);
        }
    }

    /**
     * @covers \bheisig\idoitapi\API::rawRequest
     * @throws \Exception on error
     */
    public function testRequestWithMissingParameters() {
        $request = [
            'jsonrpc' => '2.0',
            'method' => 'idoit.version',
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertInternalType('array', $response);
        $this->isError($response);
        $this->hasValidJSONRPCIdentifier($request, $response);
        $this->assertSame(-32602, $response['error']['code']);
    }

    /**
     * @return array
     */
    public function provideInvalidParameters(): array {
        return [
            'null' => [null],
            'positive integer' => [23],
            'negative integer' => [-42],
            'float' => [123.456],
            'true' => [true],
            'false' => [false],
            'empty string' => [''],
            'random string' => [$this->generateRandomString()]
        ];
    }

    /**
     * @covers \bheisig\idoitapi\API::rawRequest
     * @throws \Exception on error
     * @param mixed $parameters Invalid parameters
     * @dataProvider provideInvalidParameters
     */
    public function testRequestWithInvalidParameters($parameters) {
        $request = [
            'jsonrpc' => '2.0',
            'method' => 'idoit.version',
            'params' => $parameters,
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertInternalType('array', $response);
        $this->isError($response);
        $this->hasValidJSONRPCIdentifier($request, $response);
        $this->assertSame(-32602, $response['error']['code']);
    }

    /**
     * @covers \bheisig\idoitapi\API::rawRequest
     * @group API-107
     * @throws \Exception on error
     */
    public function testRequestWithMissingApiKey() {
        $request = [
            'jsonrpc' => '2.0',
            'method' => 'idoit.version',
            'params' => [],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertInternalType('array', $response);
        $this->isError($response);
        $this->hasValidJSONRPCIdentifier($request, $response);
        $this->assertSame(-32099, $response['error']['code']);
    }

    /**
     * @return array
     */
    public function provideInvalidAPIKeys(): array {
        return [
            'null' => [null],
            'object' => [new \StdClass()],
            'empty array' => [[]],
            'positive integer' => [23],
            'negative integer' => [-42],
            'float' => [123.456],
            'true' => [true],
            'false' => [false],
            'empty string' => [''],
            'random string' => [$this->generateRandomString()]
        ];
    }

    /**
     * @covers \bheisig\idoitapi\API::rawRequest
     * @group API-107
     * @throws \Exception on error
     * @param mixed $apiKey Invalid API key
     * @dataProvider provideInvalidAPIKeys
     */
    public function testRequestWithInvalidApiKeys($apiKey) {
        $request = [
            'jsonrpc' => '2.0',
            'method' => 'idoit.version',
            'params' => [
                'apikey' => $apiKey
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertInternalType('array', $response);
        $this->isError($response);
        $this->hasValidJSONRPCIdentifier($request, $response);
        $this->assertSame(-32099, $response['error']['code']);
    }

    /**
     * @covers \bheisig\idoitapi\API::rawRequest
     * @throws \Exception on error
     */
    public function testRequestWithMissingVersionNumber() {
        $request = [
            'method' => 'idoit.version',
            'params' => [
                'apikey' => getenv('KEY')
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertInternalType('array', $response);
        $this->isError($response);
        $this->hasValidJSONRPCIdentifier($request, $response);
        $this->assertSame(-32600, $response['error']['code']);
    }

    /**
     * @return array
     */
    public function provideInvalidVersionNumbers() {
        return [
            'null' => [null],
            'object' => [new \StdClass()],
            'empty array' => [[]],
            'positive integer' => [23],
            'negative integer' => [-42],
            'float' => [123.456],
            'true' => [true],
            'false' => [false],
            'empty string' => [''],
            'random string' => [$this->generateRandomString()]
        ];
    }

    /**
     * @covers \bheisig\idoitapi\API::rawRequest
     * @throws \Exception on error
     * @param mixed $versionNumber Invalid version number
     * @dataProvider provideInvalidVersionNumbers
     */
    public function testRequestWithInvalidVersionNumbers($versionNumber) {
        $request = [
            'jsonrpc' => $versionNumber,
            'method' => 'idoit.version',
            'params' => [
                'apikey' => getenv('KEY')
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertInternalType('array', $response);
        $this->isError($response);
        $this->hasValidJSONRPCIdentifier($request, $response);
        $this->assertSame(-32600, $response['error']['code']);
    }

    /**
     * @return array
     */
    public function provideValidIdentifiers(): array {
        return [
            'positive integer' => [23],
            'negative integer' => [-42],
            'zero' => [0],
            'random string' => [$this->generateRandomString()],
            'positive integer as string' => ['23'],
            'negative integer as string' => ['-42'],
            'zero as string' => ['0'],
        ];
    }

    /**
     * @covers \bheisig\idoitapi\API::rawRequest
     * @throws \Exception on error
     * @param mixed $identifier Valid identifier
     * @dataProvider provideValidIdentifiers
     */
    public function testRequestWithValidIdentifiers($identifier) {
        $request = [
            'jsonrpc' => '2.0',
            'method' => 'idoit.version',
            'params' => [
                'apikey' => getenv('KEY')
            ],
            'id' => $identifier
        ];

        $response = $this->api->rawRequest($request);

        $this->assertInternalType('array', $response);
        $this->isValidResponse($response, $request);
        $this->hasValidJSONRPCIdentifier($request, $response);
    }

    /**
     * @return array
     */
    public function provideInvalidIdentifiers(): array {
        return [
            'null' => [null],
            'object' => [new \StdClass()],
            'empty array' => [[]],
            'float' => [123.456],
            'true' => [true],
            'false' => [false],
            'empty string' => ['']
        ];
    }

    /**
     * @covers \bheisig\idoitapi\API::rawRequest
     * @throws \Exception on error
     * @param mixed $identifier Invalid identifier
     * @dataProvider provideInvalidIdentifiers
     */
    public function testRequestWithInvalidIdentifiers($identifier) {
        $request = [
            'jsonrpc' => '2.0',
            'method' => 'idoit.version',
            'params' => [
                'apikey' => getenv('KEY')
            ],
            'id' => $identifier
        ];

        $response = $this->api->rawRequest($request);

        $this->assertInternalType('array', $response);
        $this->isError($response);
        $this->assertNull($response['id']);
        $this->assertSame(-32603, $response['error']['code']);
    }

    /**
     * @covers \bheisig\idoitapi\API::rawRequest
     * @throws \Exception on error
     */
    public function testRequestWithMissingMethod() {
        $request = [
            'jsonrpc' => '2.0',
            'params' => [
                'apikey' => getenv('KEY')
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertInternalType('array', $response);
        $this->isError($response);
        $this->hasValidJSONRPCIdentifier($request, $response);
        $this->assertSame(-32600, $response['error']['code']);
    }

    /**
     * Provide invalid methods
     *
     * @return array
     */
    public function provideInvalidMethods(): array {
        return [
            'empty string' => [''],
            'null' => [null],
            'object' => [new \StdClass()],
            'empty array' => [[]],
            'positive integer' => [23],
            'negative integer' => [-42],
            'float' => [123.456],
            'true' => [true],
            'false' => [false]
        ];
    }

    /**
     * @covers \bheisig\idoitapi\API::rawRequest
     * @throws \Exception on error
     * @param mixed $method Invalid method
     * @dataProvider provideInvalidMethods
     */
    public function testRequestWithInvalidMethods($method) {
        $request = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => [
                'apikey' => getenv('KEY')
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertInternalType('array', $response);
        $this->isError($response);
        $this->hasValidJSONRPCIdentifier($request, $response);
        $this->assertSame(-32600, $response['error']['code']);
    }

    /**
     * Provide unknown methods
     *
     * @return array
     */
    public function provideUnknownMethods() {
        return [
            'random string' => [$this->generateRandomString()],
            'cmdb.nope' => ['cmdb.nope'],
            // @todo i-doit's routing thinks it's "cmdb.objects.read" (even if "read" doesn't exist):
            //'' => ['cmdb.objects.' . $this->generateRandomString()]
        ];
    }

    /**
     * @covers \bheisig\idoitapi\API::rawRequest
     * @throws \Exception on error
     * @param string $method Unknown method
     * @dataProvider provideUnknownMethods
     */
    public function testRequestWithUnknownMethod(string $method) {
        $request = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => [
                'apikey' => getenv('KEY')
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertInternalType('array', $response);
        $this->isError($response);
        $this->hasValidJSONRPCIdentifier($request, $response);
        $this->assertSame(-32601, $response['error']['code']);
    }

    /**
     * @covers \bheisig\idoitapi\API::rawRequest
     * @group API-118
     * @throws \Exception on error
     */
    public function testRepeatingIdentifiersInBatchRequest() {
        $request = [
            [
                'jsonrpc' => '2.0',
                'method' => 'idoit.version',
                'params' => [
                    'apikey' => getenv('KEY')
                ],
                'id' => 1
            ],
            [
                'jsonrpc' => '2.0',
                'method' => 'idoit.version',
                'params' => [
                    'apikey' => getenv('KEY')
                ],
                'id' => $this->generateRandomString()
            ],
            [
                'jsonrpc' => '2.0',
                'method' => 'idoit.version',
                'params' => [
                    'apikey' => getenv('KEY')
                ],
                'id' => 1
            ]
        ];

        $response = $this->api->rawRequest($request);

        $this->assertInternalType('array', $response);
        $this->isError($response);
        $this->assertNull($response['id']);
        $this->assertSame(-32603, $response['error']['code']);
    }

    /**
     * @covers \bheisig\idoitapi\API::rawRequest
     * @group API-119
     * @throws \Exception on error
     */
    public function testVariousApiKeysInBatchRequest() {
        $request = [
            [
                'jsonrpc' => '2.0',
                'method' => 'idoit.version',
                'params' => [
                    'apikey' => getenv('KEY')
                ],
                'id' => 1
            ],
            [
                'jsonrpc' => '2.0',
                'method' => 'idoit.version',
                'params' => [
                    'apikey' => $this->generateRandomString()
                ],
                'id' => 2
            ],
            [
                'jsonrpc' => '2.0',
                'method' => 'idoit.version',
                'params' => [
                    'apikey' => $this->generateRandomString()
                ],
                'id' => 3
            ]
        ];

        $response = $this->api->rawRequest($request);

        $this->assertInternalType('array', $response);
        $this->isError($response);
        $this->assertNull($response['id']);
        $this->assertSame(-32602, $response['error']['code']);
    }

    /**
     * @covers \bheisig\idoitapi\API::rawRequest
     * @group API-77
     * @throws \Exception on error
     * @todo At the moment this library expects a JSON string in response body, so this test will fail.
     * @expectedException \Exception
     */
    public function testNotification() {
        $request = [
            'jsonrpc' => '2.0',
            'method' => 'idoit.version',
            'params' => [
                'apikey' => getenv('KEY')
            ]
        ];

        $response = $this->api->rawRequest($request);

        $this->assertEmpty($response);
    }

    /**
     * @covers \bheisig\idoitapi\API::rawRequest
     * @group API-77
     * @throws \Exception on error
     * @todo At the moment this library expects a JSON string in response body, so this test will fail.
     * @expectedException \Exception
     */
    public function testOnlyNotificationsInBatchRequest() {
        $request = [
            [
                'jsonrpc' => '2.0',
                'method' => 'idoit.version',
                'params' => [
                    'apikey' => getenv('KEY')
                ]
            ],
            [
                'jsonrpc' => '2.0',
                'method' => 'cmdb.object.read',
                'params' => [
                    'apikey' => getenv('KEY')
                ]
            ],
            [
                'jsonrpc' => '2.0',
                'method' => 'cmdb.category.read',
                'params' => [
                    'apikey' => getenv('KEY')
                ]
            ]
        ];

        $response = $this->api->rawRequest($request);

        $this->assertEmpty($response);
    }

    /**
     * @covers \bheisig\idoitapi\API::rawRequest
     * @group API-77
     * @throws \Exception on error
     */
    public function testSomeNotificationsInBatchRequest() {
        $request = [
            [
                'jsonrpc' => '2.0',
                'method' => 'idoit.version',
                'params' => [
                    'apikey' => getenv('KEY')
                ],
                'id' => 1
            ],
            [
                'jsonrpc' => '2.0',
                'method' => 'cmdb.object.read',
                'params' => [
                    'apikey' => getenv('KEY')
                ]
            ],
            [
                'jsonrpc' => '2.0',
                'method' => 'cmdb.category.read',
                'params' => [
                    'apikey' => getenv('KEY')
                ],
                'id' => 2
            ]
        ];

        $response = $this->api->rawRequest($request);

        $this->assertInternalType('array', $response);
        $this->assertCount(2, $response);

        $this->assertArrayHasKey(0, $response);
        $this->isValidResponse($response[0], $response[0]);

        $this->assertArrayHasKey(1, $response);
        $this->isError($response[1]);
        $this->hasValidJSONRPCIdentifier($request[2], $response[1]);
    }

    /**
     * @covers \bheisig\idoitapi\API::request
     * @covers \bheisig\idoitapi\API::getLastInfo
     * @throws \Exception
     */
    public function testGetLastInfo() {
        $this->api->request('idoit.version');

        $this->assertInternalType('array', $this->api->getLastInfo());
        $this->assertNotCount(0, $this->api->getLastInfo());
    }

    /**
     * @covers \bheisig\idoitapi\API::request
     * @covers \bheisig\idoitapi\API::getLastResponse
     * @throws \Exception
     */
    public function testGetLastResponse() {
        $this->api->request('idoit.version');

        $this->assertInternalType('array', $this->api->getLastResponse());
        $this->assertNotCount(0, $this->api->getLastResponse());
    }

    /**
     * @covers \bheisig\idoitapi\API::request
     * @covers \bheisig\idoitapi\API::getLastRequestContent
     * @throws \Exception
     */
    public function testGetLastRequestContent() {
        $this->api->request('idoit.version');

        $this->assertInternalType('array', $this->api->getLastRequestContent());
        $this->assertNotCount(0, $this->api->getLastRequestContent());
    }

    /**
     * @covers \bheisig\idoitapi\API::request
     * @covers \bheisig\idoitapi\API::getLastResponseHeaders
     * @throws \Exception
     */
    public function testGetLastResponseHeaders() {
        $this->api->request('idoit.version');

        $this->assertInternalType('string', $this->api->getLastResponseHeaders());
        $this->assertNotEmpty($this->api->getLastResponseHeaders());
    }

    /**
     * @covers \bheisig\idoitapi\API::request
     * @covers \bheisig\idoitapi\API::getLastRequestHeaders
     * @throws \Exception
     */
    public function testGetLastRequestHeaders() {
        $this->api->request('idoit.version');

        $this->assertInternalType('string', $this->api->getLastRequestHeaders());
        $this->assertNotEmpty($this->api->getLastRequestHeaders());
    }

    /**
     * @covers \bheisig\idoitapi\API::request
     * @throws \Exception
     */
    public function testValidateLanguageParameter() {
        // Test object type "printer":
        $objectTypeTitles = [
            'en' => 'Printer',
            'de' => 'Drucker'
        ];

        foreach ($objectTypeTitles as $language => $translation) {
            $result = $this->api->request(
                'cmdb.object_types.read',
                [
                    'filter' => [
                        'id' => 'C__OBJTYPE__PRINTER'
                    ],
                    API::LANGUAGE => $language
                ]
            );

            $this->assertInternalType('array', $result);
            $this->assertCount(1, $result);
            $this->assertArrayHasKey(0, $result);
            $this->assertInternalType('array', $result[0]);
            $this->assertArrayHasKey('title', $result[0]);
            $this->assertSame($translation, $result[0]['title']);
        }

        // Test attribute "serial number" in category "model":
        $attributeTitles = [
            'en' => 'Serial number',
            'de' => 'Seriennummer'
        ];

        foreach ($attributeTitles as $language => $translation) {
            $result = $this->api->request(
                'cmdb.category_info.read',
                [
                    'category' => 'C__CATG__MODEL',
                    API::LANGUAGE => $language
                ]
            );

            $this->assertInternalType('array', $result);
            $this->assertArrayHasKey('serial', $result);
            $this->assertInternalType('array', $result['serial']);
            $this->assertArrayHasKey('title', $result['serial']);
            $this->assertSame($translation, $result['serial']['title']);
        }
    }

    /**
     * @group API-123
     * @covers \bheisig\idoitapi\API::login
     * @covers \bheisig\idoitapi\API::getLastResponseHeaders
     * @covers \bheisig\idoitapi\API::getLastRequestHeaders
     * @covers \bheisig\idoitapi\API::logout
     * @throws \Exception
     */
    public function testValidateSession() {
        $sessionHeader = 'X-RPC-Auth-Session';
        $this->api->login();
        $sessionIDLogin = $this->getHeader($sessionHeader, $this->api->getLastResponseHeaders());

        // Random request:
        $this->api->request('idoit.version');
        $sessionIDRequest = $this->getHeader($sessionHeader, $this->api->getLastRequestHeaders());
        $sessionIDResponse = $this->getHeader($sessionHeader, $this->api->getLastResponseHeaders());

        $this->api->logout();
        $sessionIDLogoutRequest = $this->getHeader($sessionHeader, $this->api->getLastRequestHeaders());

        $this->assertSame($sessionIDLogin, $sessionIDRequest);
        $this->assertSame($sessionIDResponse, $sessionIDRequest);
        $this->assertSame($sessionIDLogin, $sessionIDLogoutRequest);
    }

    /**
     * @param string $header Needle
     * @param string $headers Haystack
     *
     * @return string Header's value
     *
     * @throws \Exception on error
     */
    protected function getHeader(string $header, string $headers) {
        $lines = explode(PHP_EOL, $headers);

        foreach ($lines as $line) {
            if (strpos($line, $header) === 0) {
                return substr(
                    $line,
                    // "<HEADER>: "
                    strlen($header) + 2
                );
            }
        }

        throw new \RuntimeException(sprintf(
            'HTTP header "%s" not found',
            $header
        ));
    }

}
