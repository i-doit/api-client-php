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

        $this->assertIsArray($result);
        $this->assertNotCount(0, $result);

        $this->assertArrayHasKey('login', $result);
        $this->assertIsArray($result['login']);

        $this->assertArrayHasKey('userid', $result['login']);
        $this->assertIsString($result['login']['userid']);

        $this->assertArrayHasKey('name', $result['login']);
        $this->assertIsString($result['login']['name']);

        $this->assertArrayHasKey('mail', $result['login']);
        $this->assertIsString($result['login']['mail']);
        if (strlen($result['login']['mail']) > 0) {
            $this->isEmail($result['login']['mail']);
        }

        $this->assertArrayHasKey('username', $result['login']);
        $this->assertIsString($result['login']['username']);

        $this->assertArrayHasKey('mandator', $result['login']);
        $this->assertIsString($result['login']['mandator']);

        $this->assertArrayHasKey('language', $result['login']);
        $this->assertIsString($result['login']['language']);
        $this->assertContains($result['login']['language'], ['en', 'de']);

        $this->assertArrayHasKey('version', $result);
        $this->assertIsString($result['version']);

        $this->assertArrayHasKey('step', $result);
        $this->assertIsString($result['step']);

        $this->assertArrayHasKey('type', $result);
        $this->assertIsString($result['type']);
        $this->assertContains($result['type'], ['OPEN', 'PRO']);
    }

    /**
     * @group API-55
     * @throws \Exception on error
     */
    public function testGetAddOns() {
        $result = $this->instance->getAddOns();

        $this->assertIsArray($result);
        $this->assertNotCount(0, $result);

        foreach ($result as $index => $addOn) {
            $this->assertIsInt($index);
            $this->assertGreaterThanOrEqual(0, $index);

            $this->assertIsArray($addOn);

            $this->assertArrayHasKey('title', $addOn);
            $this->assertIsString($addOn['title']);
            $this->isOneLiner($addOn['title']);

            $this->assertArrayHasKey('key', $addOn);
            $this->assertIsString($addOn['key']);
            $this->isOneLiner($addOn['key']);

            $this->assertArrayHasKey('version', $addOn);
            $this->assertIsString($addOn['version']);
            $this->isOneLiner($addOn['version']);

            $this->assertArrayHasKey('author', $addOn);
            $this->assertIsArray($addOn['author']);
            $this->assertArrayHasKey('name', $addOn['author']);
            $this->assertIsString($addOn['author']['name']);
            $this->isOneLiner($addOn['author']['name']);

            $this->assertArrayHasKey('active', $addOn);
            $this->assertIsBool($addOn['active']);

            $this->assertArrayHasKey('licensed', $addOn);
            $this->assertIsBool($addOn['licensed']);

            $this->assertArrayHasKey('installed', $addOn);
            $this->assertIsBool($addOn['installed']);
        }
    }

    /**
     * @group API-101
     * @throws \Exception on error
     */
    public function testReadLicense() {
        $result = $this->instance->getLicense();

        $this->assertIsArray($result);
        $this->assertNotCount(0, $result);

        $this->assertArrayHasKey('id', $result);
        $this->assertIsInt($result['id']);
        $this->isID($result['id']);

        $this->assertArrayHasKey('organization', $result);
        $this->assertIsString($result['organization']);
        $this->isOneLiner($result['organization']);

        $this->assertArrayHasKey('email', $result);
        $this->assertIsString($result['email']);
        $this->isEmail($result['email']);

        $this->assertArrayHasKey('registrationDate', $result);
        $this->assertIsString($result['registrationDate']);
        $this->isTime($result['registrationDate']);

        $this->assertArrayHasKey('expiryDate', $result);
        $this->assertIsString($result['expiryDate']);
        $this->isTime($result['expiryDate']);

        $this->assertArrayHasKey('installationDate', $result);
        $this->assertIsString($result['installationDate']);
        $this->isTime($result['installationDate']);

        $registrationDate = strtotime($result['registrationDate']);
        $installationDate = strtotime($result['installationDate']);
        $expiryDate = strtotime($result['expiryDate']);

        $this->assertGreaterThan(0, $registrationDate);
        $this->assertGreaterThanOrEqual($registrationDate, $installationDate);
        $this->assertGreaterThanOrEqual($installationDate, $expiryDate);

        $this->assertArrayHasKey('type', $result);
        $this->assertIsString($result['type']);
        $this->assertSame('Client', $result['type']);

        $this->assertArrayHasKey('objectCapacity', $result);
        $this->assertIsArray($result['objectCapacity']);

        $this->assertArrayHasKey('total', $result['objectCapacity']);
        $this->assertIsInt($result['objectCapacity']['total']);
        $this->assertGreaterThan(0, $result['objectCapacity']['total']);

        $this->assertArrayHasKey('inUse', $result['objectCapacity']);
        $this->assertIsInt($result['objectCapacity']['inUse']);
        $this->assertGreaterThanOrEqual(0, $result['objectCapacity']['inUse']);

        $this->assertArrayHasKey('unlimited', $result['objectCapacity']);
        $this->assertIsBool($result['objectCapacity']['unlimited']);

        $this->assertArrayHasKey('modules', $result);
        $this->assertIsArray($result['modules']);

        foreach ($result['modules'] as $index => $addOnKey) {
            $this->assertIsInt($index);
            $this->assertGreaterThanOrEqual(0, $index);

            $this->assertIsString($addOnKey);
            $this->isOneLiner($addOnKey);
        }

        $this->assertArrayHasKey('valid', $result);
        $this->assertIsBool($result['valid']);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadConstants() {
        $result = $this->instance->readConstants();

        $this->assertIsArray($result);
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
            $this->assertIsArray($result[$topic]);

            // Check category constants later:
            if ($topic !== 'categories') {
                $this->validateConstants($result[$topic]);
            }
        }

        $this->assertArrayHasKey('g', $result['categories']);
        $this->assertIsArray($result['categories']['g']);
        $this->validateConstants($result['categories']['g']);

        $this->assertArrayHasKey('s', $result['categories']);
        $this->assertIsArray($result['categories']['s']);
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
            $this->assertIsString($constant);
            $this->isConstant($constant);

            $this->assertIsString($value);
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

        $this->assertIsArray($results);
        $this->assertNotCount(0, $results);

        foreach ($results as $result) {
            $this->assertIsArray($result);
            $this->isSearchResult($result);
        }
    }

    /**
     * @throws \Exception on error
     */
    public function testBatchSearch() {
        $batch = $this->instance->batchSearch(['demo', 'test', 'server']);

        $this->assertIsArray($batch);
        $this->assertNotCount(0, $batch);

        foreach ($batch as $results) {
            foreach ($results as $result) {
                $this->assertIsArray($result);
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

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertArrayHasKey(0, $results);
        $this->assertIsArray($results[0]);
        $this->isSearchResult($results[0]);
        $this->assertArrayHasKey('documentId', $results[0]);
        $this->assertSame($objectID, (int) $results[0]['documentId']);
    }

}
