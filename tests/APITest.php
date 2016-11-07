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

use PHPUnit\Framework\TestCase;
use net\benjaminheisig\idoitapi\API;

class APITest extends TestCase {

    public function testTestConfig() {
        $idoitAPI = new API([
            'url' => 'https://demo.i-doit.com/src/jsonrpc.php',
            'key' => 'c1ia5q',
            'username' => 'admin',
            'password' => 'admin'
        ]);

        $this->assertTrue($idoitAPI->testConfig());
    } //function

    public function testConnect() {
        $idoitAPI = new API([
            'url' => 'https://demo.i-doit.com/src/jsonrpc.php',
            'key' => 'c1ia5q',
            'username' => 'admin',
            'password' => 'admin'
        ]);

        $this->assertInstanceOf(API::class, $idoitAPI->connect());
    } //function

    public function testDisconnect() {
        $idoitAPI = new API([
            'url' => 'https://demo.i-doit.com/src/jsonrpc.php',
            'key' => 'c1ia5q',
            'username' => 'admin',
            'password' => 'admin'
        ]);

        $idoitAPI->connect();

        $this->assertInstanceOf(API::class, $idoitAPI->disconnect());
    } //function

    public function testIsConnected() {
        $idoitAPI = new API([
            'url' => 'https://demo.i-doit.com/src/jsonrpc.php',
            'key' => 'c1ia5q',
            'username' => 'admin',
            'password' => 'admin'
        ]);

        $this->assertFalse($idoitAPI->isConnected());

        $idoitAPI->connect();

        $this->assertTrue($idoitAPI->isConnected());

        $idoitAPI->disconnect();

        $this->assertFalse($idoitAPI->isConnected());
    } //function

    public function testLogin() {
        $idoitAPI = new API([
            'url' => 'https://demo.i-doit.com/src/jsonrpc.php',
            'key' => 'c1ia5q',
            'username' => 'admin',
            'password' => 'admin'
        ]);

        $result = $idoitAPI->login();

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('session-id', $result);
        $this->assertStringMatchesFormat('%s', $result['session-id']);
    } //function

    public function testLogout() {
        $idoitAPI = new API([
            'url' => 'https://demo.i-doit.com/src/jsonrpc.php',
            'key' => 'c1ia5q',
            'username' => 'admin',
            'password' => 'admin'
        ]);

        $idoitAPI->login();

        $result = $idoitAPI->logout();

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('result', $result);
        $this->assertTrue($result['result']);
    } //function

    public function testIsLoggedIn() {
        $idoitAPI = new API([
            'url' => 'https://demo.i-doit.com/src/jsonrpc.php',
            'key' => 'c1ia5q',
            'username' => 'admin',
            'password' => 'admin'
        ]);

        $this->assertFalse($idoitAPI->isLoggedIn());

        $idoitAPI->login();

        $this->assertTrue($idoitAPI->isLoggedIn());

        $idoitAPI->logout();

        $this->assertFalse($idoitAPI->isLoggedIn());
    } //function

    public function testCountRequests() {
        $idoitAPI = new API([
            'url' => 'https://demo.i-doit.com/src/jsonrpc.php',
            'key' => 'c1ia5q',
            'username' => 'admin',
            'password' => 'admin'
        ]);

        $idoitAPI->request('idoit.version');

        $count = $idoitAPI->countRequests();

        $this->assertInternalType('integer', $count);
        $this->assertEquals(1, $count);

        $idoitAPI->request('idoit.version');

        $count = $idoitAPI->countRequests();

        $this->assertInternalType('integer', $count);
        $this->assertEquals(2, $count);
    } //function

    public function testRequest() {
        $idoitAPI = new API([
            'url' => 'https://demo.i-doit.com/src/jsonrpc.php',
            'key' => 'c1ia5q',
            'username' => 'admin',
            'password' => 'admin'
        ]);

        $result = $idoitAPI->request('idoit.version');

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
    } //function

    public function testBatchRequest() {
        $idoitAPI = new API([
            'url' => 'https://demo.i-doit.com/src/jsonrpc.php',
            'key' => 'c1ia5q',
            'username' => 'admin',
            'password' => 'admin'
        ]);

        $results = $idoitAPI->batchRequest([
            [
                'method' => 'idoit.version'
            ],
            [
                'method' => 'cmdb.object.read',
                'params' => ['id' => 1]
            ]
        ]);

        $this->assertInternalType('array', $results);
        $this->assertCount(2, $results);

        foreach ($results as $result) {
            $this->assertInternalType('array', $result);
            $this->assertNotCount(0, $result);
        } //foreach
    } //function

    public function testGetLastInfo() {
        $idoitAPI = new API([
            'url' => 'https://demo.i-doit.com/src/jsonrpc.php',
            'key' => 'c1ia5q',
            'username' => 'admin',
            'password' => 'admin'
        ]);

        $idoitAPI->request('idoit.version');

        $this->assertInternalType('array', $idoitAPI->getLastInfo());
        $this->assertNotCount(0, $idoitAPI->getLastInfo());
    } //function

    public function testGetLastRequestContent() {
        $idoitAPI = new API([
            'url' => 'https://demo.i-doit.com/src/jsonrpc.php',
            'key' => 'c1ia5q',
            'username' => 'admin',
            'password' => 'admin'
        ]);

        $idoitAPI->request('idoit.version');

        $this->assertInternalType('array', $idoitAPI->getLastRequestContent());
        $this->assertNotCount(0, $idoitAPI->getLastRequestContent());
    } //function

    public function testGetLastResponseHeaders() {
        $idoitAPI = new API([
            'url' => 'https://demo.i-doit.com/src/jsonrpc.php',
            'key' => 'c1ia5q',
            'username' => 'admin',
            'password' => 'admin'
        ]);

        $idoitAPI->request('idoit.version');

        $this->assertInternalType('string', $idoitAPI->getLastResponseHeaders());
        $this->assertNotEmpty($idoitAPI->getLastResponseHeaders());
    } //function

    public function testGetLastRequestHeaders() {
        $idoitAPI = new API([
            'url' => 'https://demo.i-doit.com/src/jsonrpc.php',
            'key' => 'c1ia5q',
            'username' => 'admin',
            'password' => 'admin'
        ]);

        $idoitAPI->request('idoit.version');

        $this->assertInternalType('string', $idoitAPI->getLastRequestHeaders());
        $this->assertNotEmpty($idoitAPI->getLastRequestHeaders());
    } //function

} //class
