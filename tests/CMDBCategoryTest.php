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

use bheisig\idoitapi\CMDBCategory;

class CMDBCategoryTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\CMDBCategory
     */
    protected $instance;

    /**
     * @throws \Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->instance = new CMDBCategory($this->api);
    }

    /**
     * @throws \Exception on error
     */
    public function testCreate() {
        $objectID = $this->createServer();

        $entryID = $this->instance->create(
            $objectID,
            'C__CATG__IP',
            [
                'net' => $this->getIPv4Net(),
                'active' => false,
                'primary' => false,
                'net_type' => 1,
                'ipv4_assignment' => 2,
                'ipv4_address' => $this->generateIPv4Address(),
                'description' => $this->generateDescription()
            ]
        );

        $this->assertGreaterThanOrEqual(1, $entryID);
    }

    /**
     * @throws \Exception on error
     */
    public function testRead() {
        $objectID = $this->createServer();
        $this->defineModel($objectID);

        $result = $this->instance->read(
            $objectID,
            'C__CATG__MODEL'
        );

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadOneByID() {
        $objectID = $this->createServer();
        $entryID = $this->defineModel($objectID);

        // Test single-value category:
        $result = $this->instance->readOneByID(
            $objectID,
            'C__CATG__MODEL',
            $entryID
        );

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
        $this->assertArrayHasKey('id', $result);

        $entryID = $this->addIPv4($objectID);

        // Test multi-value category:
        $result = $this->instance->readOneByID(
            $objectID,
            'C__CATG__IP',
            $entryID
        );

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
        $this->assertArrayHasKey('id', $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadFirst() {
        $objectID = $this->createServer();
        $this->defineModel($objectID);

        // Test single-value category:
        $result = $this->instance->readFirst(
            $objectID,
            'C__CATG__MODEL'
        );

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
        $this->assertArrayHasKey('id', $result);

        $this->addIPv4($objectID);

        // Test multi-value category:
        $result = $this->instance->readFirst(
            $objectID,
            'C__CATG__IP'
        );

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
        $this->assertArrayHasKey('id', $result);

        // Test empty category (no entry for object):
        $result = $this->instance->readFirst(
            $objectID,
            'C__CATG__ACCESS'
        );

        $this->assertInternalType('array', $result);
        $this->assertCount(0, $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testUpdate() {
        $objectID = $this->createServer();

        $itself = $this->instance->update(
            $objectID,
            'C__CATG__GLOBAL',
            [
                'cmdb_status' => 10
            ]
        );

        $this->assertInstanceOf(CMDBCategory::class, $itself);
    }

    /**
     * @throws \Exception on error
     */
    public function testArchive() {
        $objectID = $this->createServer();

        // Single-valued category:
        // @todo Not supported by i-doit!
//        $entryID = $this->defineModel($objectID);
//
//        $itself = $this->instance->archive(
//            $objectID,
//            'C__CATG__MODEL',
//            $entryID
//        );
//
//        $this->assertInstanceOf(CMDBCategory::class, $itself);

        // Multi-valued category:
        $entryID = $this->addIPv4($objectID);

        $itself = $this->instance->archive(
            $objectID,
            'C__CATG__IP',
            $entryID
        );

        $this->assertInstanceOf(CMDBCategory::class, $itself);
    }

    /**
     * @throws \Exception on error
     */
    public function testDelete() {
        $objectID = $this->createServer();
        $entryID = $this->addIPv4($objectID);

        $itself = $this->instance->delete(
            $objectID,
            'C__CATG__IP',
            $entryID
        );

        $this->assertInstanceOf(CMDBCategory::class, $itself);
    }

    /**
     * @throws \Exception on error
     */
    public function testPurge() {
        $objectID = $this->createServer();
        $entryID = $this->addIPv4($objectID);

        $itself = $this->instance->purge(
            $objectID,
            'C__CATG__IP',
            $entryID
        );

        $this->assertInstanceOf(CMDBCategory::class, $itself);
    }

    /**
     * @throws \Exception on error
     */
    public function testBatchCreate() {
        $objectID1 = $this->createServer();
        $objectID2 = $this->createServer();

        // Single-valued category:
        $result = $this->instance->batchCreate(
            [$objectID1, $objectID2],
            'C__CATG__MODEL',
            [
                [
                    'manufacturer' => $this->generateRandomString(),
                    'title' => $this->generateRandomString(),
                    'serial' => $this->generateRandomString(),
                    'description' => $this->generateDescription()
                ]
            ]
        );

        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);

        foreach ($result as $entryID) {
            $this->assertInternalType('int', $entryID);
            $this->assertGreaterThan(0, $entryID);
        }

        // Multi-valued category:
        $result = $this->instance->batchCreate(
            [$objectID1, $objectID2],
            'C__CATG__IP',
            [
                [
                    'net' => $this->getIPv4Net(),
                    'active' => true,
                    'primary' => true,
                    'net_type' => 1,
                    'ipv4_assignment' => 2,
                    "ipv4_address" =>  $this->generateIPv4Address(),
                    'description' => $this->generateDescription()
                ],
                [
                    'net' => $this->getIPv4Net(),
                    'active' => true,
                    'primary' => false,
                    'net_type' => 1,
                    'ipv4_assignment' => 2,
                    "ipv4_address" =>  $this->generateIPv4Address(),
                    'description' => $this->generateDescription()
                ]
            ]
        );

        $this->assertInternalType('array', $result);
        $this->assertCount(4, $result);

        foreach ($result as $entryID) {
            $this->assertInternalType('int', $entryID);
            $this->assertGreaterThan(0, $entryID);
        }
    }

    /**
     * @throws \Exception on error
     */
    public function testBatchRead() {
        $objectID1 = $this->createServer();
        $objectID2 = $this->createServer();
        $this->addIPv4($objectID1);
        $this->addIPv4($objectID2);
        $this->defineModel($objectID1);
        $this->defineModel($objectID2);

        $batchResult = $this->instance->batchRead(
            [$objectID1, $objectID2],
            ['C__CATG__IP', 'C__CATG__MODEL']
        );

        $this->assertInternalType('array', $batchResult);
        $this->assertCount(4, $batchResult);

        if (is_array($batchResult)) {
            foreach ($batchResult as $result) {
                $this->assertInternalType('array', $result);
                $this->assertNotCount(0, $result);
            }
        }
    }

    /**
     * @throws \Exception on error
     */
    public function testBatchUpdate() {
        $objectID1 = $this->createServer();
        $objectID2 = $this->createServer();

        $entryIDs = [];

        $entryIDs[] = $this->defineModel($objectID1);
        $entryIDs[] = $this->defineModel($objectID2);

        $itself = $this->instance->batchUpdate(
            [$objectID1, $objectID2],
            'C__CATG__MODEL',
            [
                'manufacturer' => $this->generateRandomString(),
                'title' => $this->generateRandomString(),
                'serial' => $this->generateRandomString(),
                'description' => $this->generateDescription()
            ]
        );

        $this->assertInstanceOf(CMDBCategory::class, $itself);
    }

    /**
     * @throws \Exception on error
     */
    public function testClear() {
        $objectID = $this->createServer();
        $this->addIPv4($objectID);
        $this->addIPv4($objectID);
        $this->addContact($objectID, 9, 1);

        $result = $this->instance->clear($objectID, [
            'C__CATG__IP',
            'C__CATG__CONTACT'
        ]);

        $this->assertInternalType('int', $result);
        $this->assertEquals(3, $result);
    }

}
