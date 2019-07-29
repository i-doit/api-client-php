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

use bheisig\idoitapi\tests\Constants\ObjectType;
use \Exception;
use bheisig\idoitapi\Idoit;
use bheisig\idoitapi\CMDBObject;

/**
 * @coversDefaultClass \bheisig\idoitapi\Idoit
 */
class IdoitTest extends BaseTest {

    /**
     * @var Idoit
     */
    protected $instance;

    /**
     * @throws Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->instance = new Idoit($this->api);
    }

    /**
     * @throws Exception on error
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
     * @throws Exception on error
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
     * @group API-168
     * @throws Exception on error
     */
    public function testReadLicense() {
        $result = $this->instance->getLicense();

        $this->assertIsArray($result);
        $this->assertNotCount(0, $result);

        /**
         * Object capacity:
         */

        $this->assertArrayHasKey('objectCapacity', $result);
        $this->assertIsArray($result['objectCapacity']);
        $this->assertCount(2, $result['objectCapacity']);

        $this->assertArrayHasKey('total', $result['objectCapacity']);
        $this->assertIsInt($result['objectCapacity']['total']);
        $this->assertGreaterThanOrEqual(0, $result['objectCapacity']['total']);

        $this->assertArrayHasKey('inUse', $result['objectCapacity']);
        $this->assertIsInt($result['objectCapacity']['inUse']);
        $this->assertGreaterThanOrEqual(0, $result['objectCapacity']['inUse']);

        /**
         * Add-ons:
         */

        $this->assertArrayHasKey('addons', $result);
        $this->assertIsArray($result['addons']);

        foreach ($result['addons'] as $key => $addon) {
            $this->assertIsString($key);

            $this->assertIsArray($addon);

            $this->assertArrayHasKey('label', $addon);
            $this->assertIsString($addon['label']);

            $this->assertArrayHasKey('licensed', $addon);
            $this->assertIsBool($addon['licensed']);
        }

        /**
         * Licenses:
         */

        $this->assertArrayHasKey('licenses', $result);

        foreach ($result['licenses'] as $index => $license) {
            $this->assertIsInt($index);
            $this->assertGreaterThanOrEqual(0, $index);

            $this->assertArrayHasKey('id', $license);
            $this->assertIsInt($license['id']);
            $this->isID($license['id']);

            $this->assertArrayHasKey('label', $license);
            $this->assertIsString($license['label']);

            $this->assertArrayHasKey('licenseType', $license);
            $this->assertIsString($license['licenseType']);

            $this->assertArrayHasKey('registrationDate', $license);
            $this->assertIsString($license['registrationDate']);
            $this->isTime($license['registrationDate']);

            $this->assertArrayHasKey('validUntil', $license);
            $this->assertIsString($license['validUntil']);
            $this->isTime($license['validUntil']);

            $registrationDate = strtotime($license['registrationDate']);
            $validUntilDate = strtotime($license['validUntil']);

            $this->assertGreaterThan(0, $registrationDate);
            $this->assertGreaterThanOrEqual($registrationDate, $validUntilDate);

            $this->assertArrayHasKey('objects', $license);
            $this->assertIsInt($license['objects']);
            $this->assertGreaterThanOrEqual(0, $license['objects']);

            $this->assertArrayHasKey('tenants', $license);
            $this->assertIsInt($license['tenants']);
            $this->assertGreaterThanOrEqual(0, $license['tenants']);

            $this->assertArrayHasKey('environment', $license);
            $this->assertIsString($license['environment']);

            $this->assertArrayHasKey('valid', $license);
            $this->assertIsBool($license['valid']);
        }
    }

    /**
     * @throws Exception on error
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
     * @throws Exception on error
     */
    public function testSearch() {
        // We need something to look for:
        $objectTitle = 'demo';
        $cmdbObject = new CMDBObject($this->api);
        $cmdbObject->create(ObjectType::SERVER, $objectTitle);

        $results = $this->instance->search('demo');

        $this->assertIsArray($results);
        $this->assertNotCount(0, $results);

        foreach ($results as $result) {
            $this->assertIsArray($result);
            $this->isSearchResult($result);
        }
    }

    /**
     * @throws Exception on error
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
     * @throws Exception on error
     */
    public function testSearchForNewObject() {
        $objectTitle = $this->generateRandomString();
        $cmdbObject = new CMDBObject($this->api);
        $objectID = $cmdbObject->create(ObjectType::SERVER, $objectTitle);
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
