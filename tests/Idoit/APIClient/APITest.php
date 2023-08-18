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

use \stdClass;
use \Exception;
use \RuntimeException;
use Idoit\APIClient\Constants\Category;
use Idoit\APIClient\Constants\ObjectType;

class APITest extends BaseTest {

    /**
     * @covers \Idoit\APIClient\API::__construct
     * @covers \Idoit\APIClient\API::testConfig
     * @throws Exception
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
     * @covers \Idoit\APIClient\API::__construct
     * @covers \Idoit\APIClient\API::testConfig
     * @covers \Idoit\APIClient\API::__destruct
     * @throws Exception
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
     * @covers \Idoit\APIClient\API::connect
     * @throws Exception
     */
    public function testConnect() {
        $this->assertInstanceOf(API::class, $this->api->connect());
    }

    /**
     * @covers \Idoit\APIClient\API::connect
     * @covers \Idoit\APIClient\API::disconnect
     * @throws Exception
     */
    public function testDisconnect() {
        $this->api->connect();

        $this->assertInstanceOf(API::class, $this->api->disconnect());
    }

    /**
     * @covers \Idoit\APIClient\API::isConnected
     * @covers \Idoit\APIClient\API::connect
     * @covers \Idoit\APIClient\API::disconnect
     * @throws Exception
     */
    public function testIsConnected() {
        $this->assertFalse($this->api->isConnected());

        $this->api->connect();

        $this->assertTrue($this->api->isConnected());

        $this->api->disconnect();

        $this->assertFalse($this->api->isConnected());
    }

    /**
     * @covers \Idoit\APIClient\API::login
     * @throws Exception
     */
    public function testLogin() {
        $this->assertInstanceOf(API::class, $this->api->login());
    }

    /**
     * @covers \Idoit\APIClient\API::login
     * @covers \Idoit\APIClient\API::logout
     * @throws Exception
     */
    public function testLogout() {
        $this->api->login();

        $this->assertInstanceOf(API::class, $this->api->logout());
    }

    /**
     * @covers \Idoit\APIClient\API::isLoggedIn
     * @covers \Idoit\APIClient\API::login
     * @covers \Idoit\APIClient\API::logout
     * @throws Exception
     */
    public function testIsLoggedIn() {
        $this->assertFalse($this->api->isLoggedIn());

        $this->api->login();

        $this->assertTrue($this->api->isLoggedIn());

        $this->api->logout();

        $this->assertFalse($this->api->isLoggedIn());
    }

    /**
     * @covers \Idoit\APIClient\API::request
     * @covers \Idoit\APIClient\API::setCURLOptions
     * @covers \Idoit\APIClient\API::evaluateResponse
     * @throws Exception
     */
    public function testRequest() {
        $result = $this->api->request('idoit.version');

        $this->assertIsArray($result);
        $this->assertNotCount(0, $result);
    }

    /**
     * @covers \Idoit\APIClient\API::request
     * @covers \Idoit\APIClient\API::countRequests
     * @covers \Idoit\APIClient\API::genID
     * @throws Exception
     */
    public function testCountRequests() {
        $this->api->request('idoit.version');

        $count = $this->api->countRequests();

        $this->assertIsInt($count);
        $this->assertSame(1, $count);

        $this->api->request('idoit.version');

        $count = $this->api->countRequests();

        $this->assertIsInt($count);
        $this->assertSame(2, $count);
    }

    /**
     * @covers \Idoit\APIClient\API::batchRequest
     * @throws Exception
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

        $this->assertIsArray($results);
        $this->assertCount(2, $results);

        foreach ($results as $result) {
            $this->assertIsArray($result);
            $this->assertNotCount(0, $result);
        }
    }

    /**
     * @covers \Idoit\APIClient\API::batchRequest
     * @throws Exception
     */
    public function testOneRequestInABatch() {
        $results = $this->api->batchRequest([
            [
                'method' => 'idoit.version'
            ]
        ]);

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertArrayHasKey(0, $results);
        $this->assertIsArray($results[0]);
        $this->assertNotCount(0, $results[0]);
    }

    /**
     * @covers \Idoit\APIClient\API::rawRequest
     * @throws Exception on error
     */
    public function testRawRequest() {
        $request = [
            'jsonrpc' => '2.0',
            'method' => 'idoit.version',
            'params' => [
                'apikey' => getenv('IDOIT_KEY')
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
        $this->isValidResponse($response, $request);
        $this->assertIsArray($response['result']);
    }

    /**
     * @covers \Idoit\APIClient\API::rawRequest
     * @covers \Idoit\APIClient\API::getLastRequestHeaders
     * @throws Exception on error
     */
    public function testRequestWithAdditionalHeaders() {
        $request = [
            'jsonrpc' => '2.0',
            'method' => 'idoit.version',
            'params' => [
                'apikey' => getenv('IDOIT_KEY')
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
        $this->assertIsArray($response);
        $this->isValidResponse($response, $request);
        $this->assertIsArray($response['result']);
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
     * @covers \Idoit\APIClient\API::rawRequest
     * @throws Exception on error
     * @dataProvider provideInvalidRequests
     * @param mixed $request Invalid request
     */
    public function testInvalidRequest($request) {
        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);

        $this->isError($response);
        $this->assertNull($response['id']);
        $this->assertSame(-32600, $response['error']['code']);
    }

    /**
     * @covers \Idoit\APIClient\API::rawRequest
     * @throws Exception on error
     * @dataProvider provideInvalidBatch
     * @param mixed $request Invalid batch request
     */
    public function testInvalidBatchRequest($request) {
        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);

        foreach ($response as $result) {
            $this->isError($result);
            $this->assertNull($result['id']);
            $this->assertSame(-32600, $result['error']['code']);
        }
    }

    /**
     * @covers \Idoit\APIClient\API::rawRequest
     * @throws Exception on error
     */
    public function testRequestWithMissingParameters() {
        $request = [
            'jsonrpc' => '2.0',
            'method' => 'idoit.version',
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
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
     * @covers \Idoit\APIClient\API::rawRequest
     * @throws Exception on error
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

        $this->assertIsArray($response);
        $this->isError($response);
        $this->hasValidJSONRPCIdentifier($request, $response);
        $this->assertSame(-32602, $response['error']['code']);
    }

    /**
     * @covers \Idoit\APIClient\API::rawRequest
     * @group API-107
     * @throws Exception on error
     */
    public function testRequestWithMissingApiKey() {
        $request = [
            'jsonrpc' => '2.0',
            'method' => 'idoit.version',
            'params' => [],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
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
            'object' => [new stdClass()],
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
     * @covers \Idoit\APIClient\API::rawRequest
     * @group API-107
     * @throws Exception on error
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

        $this->assertIsArray($response);
        $this->isError($response);
        $this->hasValidJSONRPCIdentifier($request, $response);
        $this->assertSame(-32099, $response['error']['code']);
    }

    /**
     * @covers \Idoit\APIClient\API::rawRequest
     * @throws Exception on error
     */
    public function testRequestWithMissingVersionNumber() {
        $request = [
            'method' => 'idoit.version',
            'params' => [
                'apikey' => getenv('IDOIT_KEY')
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
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
            'object' => [new stdClass()],
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
     * @covers \Idoit\APIClient\API::rawRequest
     * @throws Exception on error
     * @param mixed $versionNumber Invalid version number
     * @dataProvider provideInvalidVersionNumbers
     */
    public function testRequestWithInvalidVersionNumbers($versionNumber) {
        $request = [
            'jsonrpc' => $versionNumber,
            'method' => 'idoit.version',
            'params' => [
                'apikey' => getenv('IDOIT_KEY')
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
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
     * @covers \Idoit\APIClient\API::rawRequest
     * @throws Exception on error
     * @param mixed $identifier Valid identifier
     * @dataProvider provideValidIdentifiers
     */
    public function testRequestWithValidIdentifiers($identifier) {
        $request = [
            'jsonrpc' => '2.0',
            'method' => 'idoit.version',
            'params' => [
                'apikey' => getenv('IDOIT_KEY')
            ],
            'id' => $identifier
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
        $this->isValidResponse($response, $request);
        $this->hasValidJSONRPCIdentifier($request, $response);
    }

    /**
     * @return array
     */
    public function provideInvalidIdentifiers(): array {
        return [
            'null' => [null],
            'object' => [new stdClass()],
            'empty array' => [[]],
            'float' => [123.456],
            'true' => [true],
            'false' => [false],
            'empty string' => ['']
        ];
    }

    /**
     * @covers \Idoit\APIClient\API::rawRequest
     * @throws Exception on error
     * @param mixed $identifier Invalid identifier
     * @dataProvider provideInvalidIdentifiers
     */
    public function testRequestWithInvalidIdentifiers($identifier) {
        $request = [
            'jsonrpc' => '2.0',
            'method' => 'idoit.version',
            'params' => [
                'apikey' => getenv('IDOIT_KEY')
            ],
            'id' => $identifier
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
        $this->isError($response);
        $this->assertNull($response['id']);
        $this->assertSame(-32603, $response['error']['code']);
    }

    /**
     * @covers \Idoit\APIClient\API::rawRequest
     * @throws Exception on error
     */
    public function testRequestWithMissingMethod() {
        $request = [
            'jsonrpc' => '2.0',
            'params' => [
                'apikey' => getenv('IDOIT_KEY')
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
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
            'object' => [new stdClass()],
            'empty array' => [[]],
            'positive integer' => [23],
            'negative integer' => [-42],
            'float' => [123.456],
            'true' => [true],
            'false' => [false]
        ];
    }

    /**
     * @covers \Idoit\APIClient\API::rawRequest
     * @throws Exception on error
     * @param mixed $method Invalid method
     * @dataProvider provideInvalidMethods
     */
    public function testRequestWithInvalidMethods($method) {
        $request = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => [
                'apikey' => getenv('IDOIT_KEY')
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
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
     * @covers \Idoit\APIClient\API::rawRequest
     * @throws Exception on error
     * @param string $method Unknown method
     * @dataProvider provideUnknownMethods
     */
    public function testRequestWithUnknownMethod(string $method) {
        $request = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => [
                'apikey' => getenv('IDOIT_KEY')
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
        $this->isError($response);
        $this->hasValidJSONRPCIdentifier($request, $response);
        $this->assertSame(-32601, $response['error']['code']);
    }

    /**
     * @covers \Idoit\APIClient\API::rawRequest
     * @group API-118
     * @throws Exception on error
     */
    public function testRepeatingIdentifiersInBatchRequest() {
        $request = [
            [
                'jsonrpc' => '2.0',
                'method' => 'idoit.version',
                'params' => [
                    'apikey' => getenv('IDOIT_KEY')
                ],
                'id' => 1
            ],
            [
                'jsonrpc' => '2.0',
                'method' => 'idoit.version',
                'params' => [
                    'apikey' => getenv('IDOIT_KEY')
                ],
                'id' => $this->generateRandomString()
            ],
            [
                'jsonrpc' => '2.0',
                'method' => 'idoit.version',
                'params' => [
                    'apikey' => getenv('IDOIT_KEY')
                ],
                'id' => 1
            ]
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
        $this->isError($response);
        $this->assertNull($response['id']);
        $this->assertSame(-32603, $response['error']['code']);
    }

    /**
     * @covers \Idoit\APIClient\API::rawRequest
     * @group API-119
     * @throws Exception on error
     */
    public function testVariousApiKeysInBatchRequest() {
        $request = [
            [
                'jsonrpc' => '2.0',
                'method' => 'idoit.version',
                'params' => [
                    'apikey' => getenv('IDOIT_KEY')
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

        $this->assertIsArray($response);
        $this->isError($response);
        $this->assertNull($response['id']);
        $this->assertSame(-32602, $response['error']['code']);
    }

    /**
     * @covers \Idoit\APIClient\API::rawRequest
     * @group API-77
     * @throws Exception on error
     * @todo At the moment this library expects a JSON string in response body, so this test will fail.
     */
    public function testNotification() {
        $this->expectException(Exception::class);

        $request = [
            'jsonrpc' => '2.0',
            'method' => 'idoit.version',
            'params' => [
                'apikey' => getenv('IDOIT_KEY')
            ]
        ];

        $response = $this->api->rawRequest($request);

        $this->assertEmpty($response);
    }

    /**
     * @covers \Idoit\APIClient\API::rawRequest
     * @group API-77
     * @throws Exception on error
     * @todo At the moment this library expects a JSON string in response body, so this test will fail.
     */
    public function testOnlyNotificationsInBatchRequest() {
        $this->expectException(Exception::class);

        $request = [
            [
                'jsonrpc' => '2.0',
                'method' => 'idoit.version',
                'params' => [
                    'apikey' => getenv('IDOIT_KEY')
                ]
            ],
            [
                'jsonrpc' => '2.0',
                'method' => 'cmdb.object.read',
                'params' => [
                    'apikey' => getenv('IDOIT_KEY')
                ]
            ],
            [
                'jsonrpc' => '2.0',
                'method' => 'cmdb.category.read',
                'params' => [
                    'apikey' => getenv('IDOIT_KEY')
                ]
            ]
        ];

        $response = $this->api->rawRequest($request);

        $this->assertEmpty($response);
    }

    /**
     * @covers \Idoit\APIClient\API::rawRequest
     * @group API-77
     * @throws Exception on error
     */
    public function testSomeNotificationsInBatchRequest() {
        $request = [
            [
                'jsonrpc' => '2.0',
                'method' => 'idoit.version',
                'params' => [
                    'apikey' => getenv('IDOIT_KEY')
                ],
                'id' => 1
            ],
            [
                'jsonrpc' => '2.0',
                'method' => 'cmdb.object.read',
                'params' => [
                    'apikey' => getenv('IDOIT_KEY')
                ]
            ],
            [
                'jsonrpc' => '2.0',
                'method' => 'cmdb.category.read',
                'params' => [
                    'apikey' => getenv('IDOIT_KEY')
                ],
                'id' => 2
            ]
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
        $this->assertCount(2, $response);

        $this->assertArrayHasKey(0, $response);
        $this->isValidResponse($response[0], $response[0]);

        $this->assertArrayHasKey(1, $response);
        $this->isError($response[1]);
        $this->hasValidJSONRPCIdentifier($request[2], $response[1]);
    }

    /**
     * @covers \Idoit\APIClient\API::request
     * @covers \Idoit\APIClient\API::getLastInfo
     * @throws Exception
     */
    public function testGetLastInfo() {
        $this->api->request('idoit.version');

        $this->assertIsArray($this->api->getLastInfo());
        $this->assertNotCount(0, $this->api->getLastInfo());
    }

    /**
     * @covers \Idoit\APIClient\API::request
     * @covers \Idoit\APIClient\API::getLastResponse
     * @throws Exception
     */
    public function testGetLastResponse() {
        $this->api->request('idoit.version');

        $this->assertIsArray($this->api->getLastResponse());
        $this->assertNotCount(0, $this->api->getLastResponse());
    }

    /**
     * @covers \Idoit\APIClient\API::request
     * @covers \Idoit\APIClient\API::getLastRequestContent
     * @throws Exception
     */
    public function testGetLastRequestContent() {
        $this->api->request('idoit.version');

        $this->assertIsArray($this->api->getLastRequestContent());
        $this->assertNotCount(0, $this->api->getLastRequestContent());
    }

    /**
     * @covers \Idoit\APIClient\API::request
     * @covers \Idoit\APIClient\API::getLastResponseHeaders
     * @throws Exception
     */
    public function testGetLastResponseHeaders() {
        $this->api->request('idoit.version');

        $this->assertIsString($this->api->getLastResponseHeaders());
        $this->assertNotEmpty($this->api->getLastResponseHeaders());
    }

    /**
     * @covers \Idoit\APIClient\API::request
     * @covers \Idoit\APIClient\API::getLastRequestHeaders
     * @throws Exception
     */
    public function testGetLastRequestHeaders() {
        $this->api->request('idoit.version');

        $this->assertIsString($this->api->getLastRequestHeaders());
        $this->assertNotEmpty($this->api->getLastRequestHeaders());
    }

    /**
     * @covers \Idoit\APIClient\API::request
     * @throws Exception
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
                        'id' => ObjectType::PRINTER
                    ],
                    API::LANGUAGE => $language
                ]
            );

            $this->assertIsArray($result);
            $this->assertCount(1, $result);
            $this->assertArrayHasKey(0, $result);
            $this->assertIsArray($result[0]);
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
                    'category' => Category::CATG__MODEL,
                    API::LANGUAGE => $language
                ]
            );

            $this->assertIsArray($result);
            $this->assertArrayHasKey('serial', $result);
            $this->assertIsArray($result['serial']);
            $this->assertArrayHasKey('title', $result['serial']);
            $this->assertSame($translation, $result['serial']['title']);
        }
    }

    /**
     * @group API-123
     * @covers \Idoit\APIClient\API::login
     * @covers \Idoit\APIClient\API::getLastResponseHeaders
     * @covers \Idoit\APIClient\API::getLastRequestHeaders
     * @covers \Idoit\APIClient\API::logout
     * @throws Exception
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
     * @throws Exception on error
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

        throw new RuntimeException(sprintf(
            'HTTP header "%s" not found',
            $header
        ));
    }

}
