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

namespace bheisig\idoitapi\tests;

use bheisig\idoitapi\API;
use bheisig\idoitapi\Idoit;

class APITest extends BaseTest {

    /**
     * @throws \Exception
     */
    public function testConnect() {
        $this->assertInstanceOf(API::class, $this->api->connect());
    }

    /**
     * @throws \Exception
     */
    public function testDisconnect() {
        $this->api->connect();

        $this->assertInstanceOf(API::class, $this->api->disconnect());
    }

    /**
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
     * @throws \Exception
     */
    public function testLogin() {
        $this->assertInstanceOf(API::class, $this->api->login());
    }

    /**
     * @throws \Exception
     */
    public function testLogout() {
        $this->api->login();

        $this->assertInstanceOf(API::class, $this->api->logout());
    }

    /**
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
     * @throws \Exception
     */
    public function testRequest() {
        $result = $this->api->request('idoit.version');

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
    }

    /**
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
     * @throws \Exception
     */
    public function testGetLastInfo() {
        $this->api->request('idoit.version');

        $this->assertInternalType('array', $this->api->getLastInfo());
        $this->assertNotCount(0, $this->api->getLastInfo());
    }

    /**
     * @throws \Exception
     */
    public function testLastResponse() {
        $this->api->request('idoit.version');

        $this->assertInternalType('array', $this->api->getLastResponse());
        $this->assertNotCount(0, $this->api->getLastResponse());
    }

    /**
     * @throws \Exception
     */
    public function testGetLastRequestContent() {
        $this->api->request('idoit.version');

        $this->assertInternalType('array', $this->api->getLastRequestContent());
        $this->assertNotCount(0, $this->api->getLastRequestContent());
    }

    /**
     * @throws \Exception
     */
    public function testGetLastResponseHeaders() {
        $this->api->request('idoit.version');

        $this->assertInternalType('string', $this->api->getLastResponseHeaders());
        $this->assertNotEmpty($this->api->getLastResponseHeaders());
    }

    /**
     * @throws \Exception
     */
    public function testGetLastRequestHeaders() {
        $this->api->request('idoit.version');

        $this->assertInternalType('string', $this->api->getLastRequestHeaders());
        $this->assertNotEmpty($this->api->getLastRequestHeaders());
    }

    /**
     * @throws \Exception
     */
    public function testLanguageParameter() {
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
     * @throws \Exception
     */
    public function testSessionID() {
        $sessionHeader = 'X-RPC-Auth-Session';
        $this->api->login();
        $sessionIDLogin = $this->getHeader($sessionHeader, $this->api->getLastResponseHeaders());

        // Random request:
        $idoit = new Idoit($this->api);
        $idoit->readVersion();
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
    protected function getHeader($header, $headers) {
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

        throw new \Exception(sprintf(
            'HTTP header "%s" not found',
            $header
        ));
    }

}
