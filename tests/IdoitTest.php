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

use bheisig\idoitapi\Idoit;
use bheisig\idoitapi\CMDBObject;

/**
 * @coversDefaultClass \bheisig\idoitapi\Idoit
 */
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

        $this->assertArrayHasKey('login', $result);
        $this->assertInternalType('array', $result['login']);

        $this->assertArrayHasKey('userid', $result['login']);
        $this->assertInternalType('string', $result['login']['userid']);

        $this->assertArrayHasKey('name', $result['login']);
        $this->assertInternalType('string', $result['login']['name']);

        $this->assertArrayHasKey('mail', $result['login']);
        $this->assertInternalType('string', $result['login']['mail']);

        $this->assertArrayHasKey('username', $result['login']);
        $this->assertInternalType('string', $result['login']['username']);

        $this->assertArrayHasKey('mandator', $result['login']);
        $this->assertInternalType('string', $result['login']['mandator']);

        $this->assertArrayHasKey('language', $result['login']);
        $this->assertInternalType('string', $result['login']['language']);
        $this->assertContains($result['login']['language'], ['en', 'de']);

        $this->assertArrayHasKey('version', $result);
        $this->assertInternalType('string', $result['version']);

        $this->assertArrayHasKey('step', $result);
        $this->assertInternalType('string', $result['step']);

        $this->assertArrayHasKey('type', $result);
        $this->assertInternalType('string', $result['type']);
        $this->assertContains($result['type'], ['OPEN', 'PRO']);
    }

    /**
     * @group unreleased
     * @group API-55
     * @throws \Exception on error
     */
    public function testGetAddOns() {
        $result = $this->instance->getAddOns();

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);

        foreach ($result as $index => $addOn) {
            $this->assertInternalType('int', $index);
            $this->assertGreaterThanOrEqual(0, $index);

            $this->assertInternalType('array', $addOn);

            $this->assertArrayHasKey('title', $addOn);
            $this->assertInternalType('string', $addOn['title']);
            $this->isOneLiner($addOn['title']);

            $this->assertArrayHasKey('key', $addOn);
            $this->assertInternalType('string', $addOn['key']);
            $this->isOneLiner($addOn['key']);

            $this->assertArrayHasKey('version', $addOn);
            $this->assertInternalType('string', $addOn['version']);
            $this->isOneLiner($addOn['version']);

            $this->assertArrayHasKey('author', $addOn);
            $this->assertInternalType('array', $addOn['author']);
            $this->assertArrayHasKey('name', $addOn['author']);
            $this->assertInternalType('string', $addOn['author']['name']);
            $this->isOneLiner($addOn['author']['name']);

            $this->assertArrayHasKey('active', $addOn);
            $this->assertInternalType('boolean', $addOn['active']);

            $this->assertArrayHasKey('licensed', $addOn);
            $this->assertInternalType('boolean', $addOn['licensed']);

            $this->assertArrayHasKey('installed', $addOn);
            $this->assertInternalType('boolean', $addOn['installed']);
        }
    }

    /**
     * @group unreleased
     * @throws \Exception on error
     */
    public function testReadConstants() {
        $result = $this->instance->readConstants();

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);

        $topics = [
            'objectTypes',
            'categories',
            'recordStates',
            'relationTypes',
            'staticObjects'
        ];

        foreach ($topics as $topic) {
            $this->assertArrayHasKey($topic, $result);
            $this->assertInternalType('array', $result[$topic]);

            // Check category constants later:
            if ($topic !== 'categories') {
                $this->validateConstants($result[$topic]);
            }
        }

        $this->assertArrayHasKey('g', $result['categories']);
        $this->assertInternalType('array', $result['categories']['g']);
        $this->validateConstants($result['categories']['g']);

        $this->assertArrayHasKey('s', $result['categories']);
        $this->assertInternalType('array', $result['categories']['s']);
        $this->validateConstants($result['categories']['s']);
    }

    /**
     * Validate i-doit constants
     *
     * @param array $constants i-doit constants
     */
    protected function validateConstants(array $constants) {
        $this->assertNotCount(0, $constants);

        foreach ($constants as $constant => $value) {
            $this->assertInternalType('string', $constant);
            $this->isConstant($constant);

            $this->assertInternalType('string', $value);
            $this->assertNotEmpty($value);
        }
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
            $this->isSearchResult($result);
        }
    }

    /**
     * @throws \Exception on error
     */
    public function testBatchSearch() {
        $batch = $this->instance->batchSearch(['demo', 'test', 'server']);

        $this->assertInternalType('array', $batch);
        $this->assertNotCount(0, $batch);

        foreach ($batch as $results) {
            foreach ($results as $result) {
                $this->assertInternalType('array', $result);
                $this->isSearchResult($result);
            }
        }
    }

    /**
     * @throws \Exception on error
     */
    public function testSearchForNewObject() {
        $objectTitle = $this->generateRandomString();
        $cmdbObject = new CMDBObject($this->api);
        $objectID = $cmdbObject->create('C__OBJTYPE__SERVER', $objectTitle);
        $results = $this->instance->search($objectTitle);

        $this->assertInternalType('array', $results);
        $this->assertCount(1, $results);
        $this->assertArrayHasKey(0, $results);
        $this->assertInternalType('array', $results[0]);
        $this->isSearchResult($results[0]);
        $this->assertArrayHasKey('documentId', $results[0]);
        $this->assertSame($objectID, (int) $results[0]['documentId']);
    }

}
