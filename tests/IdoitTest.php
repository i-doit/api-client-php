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

use bheisig\idoitapi\Idoit;
use bheisig\idoitapi\CMDBObject;

class IdoitTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\Idoit
     */
    protected $instance;

    /**
     * @throws \Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->instance = new Idoit($this->api);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadVersion() {
        $result = $this->instance->readVersion();

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadConstants() {
        $result = $this->instance->readConstants();

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testSearch() {
        // We need something to look for:
        $objectTitle = 'demo';
        $cmdbObject = new CMDBObject($this->api);
        $cmdbObject->create('C__OBJTYPE__SERVER', $objectTitle);

        $results = $this->instance->search('demo');

        $this->assertInternalType('array', $results);
        $this->assertNotCount(0, $results);

        foreach ($results as $result) {
            $this->assertInternalType('array', $result);

            $this->assertArrayHasKey('documentId', $result);
            $this->assertInternalType('string', $result['documentId']);
            // "documentId" is a numeric string:
            $documentId = (int) $result['documentId'];
            $this->assertGreaterThan(0, $documentId);

            $this->assertArrayHasKey('key', $result);
            $this->assertInternalType('string', $result['key']);

            $this->assertArrayHasKey('value', $result);
            $this->assertInternalType('string', $result['value']);

            $this->assertArrayHasKey('type', $result);
            $this->assertInternalType('string', $result['type']);

            $this->assertArrayHasKey('link', $result);
            $this->assertInternalType('string', $result['link']);

            $this->assertArrayHasKey('score', $result);
            $this->assertInternalType('string', $result['score']);
        }
    }

    /**
     * @throws \Exception on error
     */
    public function testBatchSearch() {
        $batchResult = $this->instance->batchSearch(['demo', 'test', 'server']);

        $this->assertInternalType('array', $batchResult);
        $this->assertNotCount(0, $batchResult);

        foreach ($batchResult as $result) {
            $this->assertInternalType('array', $result);
        }
    }

    /**
     * @throws \Exception on error
     */
    public function testSearchForNewObject() {
        $objectTitle = $this->generateRandomString();
        $cmdbObject = new CMDBObject($this->api);
        $objectID = $cmdbObject->create('C__OBJTYPE__SERVER', $objectTitle);
        $result = $this->instance->search($objectTitle);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey(0, $result);
        $this->assertInternalType('array', $result[0]);
        $this->assertArrayHasKey('documentId', $result[0]);
        $documentId = (int) $result[0]['documentId'];
        $this->assertEquals($objectID, $documentId);
    }

}
