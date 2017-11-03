<?php

/**
 * Copyright (C) 2016-17 Benjamin Heisig
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
 * @copyright Copyright (C) 2016-17 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

use bheisig\idoitapi\CMDBCategory;

class CMDBCategoryTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\CMDBCategory
     */
    protected $category;

    public function setUp() {
        parent::setUp();

        $this->category = new CMDBCategory($this->api);
    }

    public function testCreate() {
        $objectID = $this->createServer();

        $entryID = $this->category->create(
            $objectID,
            'C__CATG__IP',
            [
                'net' => $this->getIPv4Net(),
                'active' => false,
                'primary' => false,
                'net_type' => 1,
                'ipv4_assignment' => 2,
                "ipv4_address" =>  '10.20.10.100',
                'description' => 'API TEST'
            ]
        );

        $this->assertGreaterThanOrEqual(1, $entryID);
    }

    public function testRead() {
        $objectID = $this->createServer();
        $this->defineModel($objectID);

        $result = $this->category->read(
            $objectID,
            'C__CATG__MODEL'
        );

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
    }

    public function testReadOneByID() {
        $objectID = $this->createServer();
        $entryID = $this->defineModel($objectID);

        // Test single-value category:
        $result = $this->category->readOneByID(
            $objectID,
            'C__CATG__MODEL',
            $entryID
        );

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
        $this->assertArrayHasKey('id', $result);

        $entryID = $this->addIPv4($objectID);

        // Test multi-value category:
        $result = $this->category->readOneByID(
            $objectID,
            'C__CATG__IP',
            $entryID
        );

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
        $this->assertArrayHasKey('id', $result);
    }

    public function testReadFirst() {
        $objectID = $this->createServer();
        $this->defineModel($objectID);

        // Test single-value category:
        $result = $this->category->readFirst(
            $objectID,
            'C__CATG__MODEL'
        );

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
        $this->assertArrayHasKey('id', $result);

        $this->addIPv4($objectID);

        // Test multi-value category:
        $result = $this->category->readFirst(
            $objectID,
            'C__CATG__IP'
        );

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
        $this->assertArrayHasKey('id', $result);
    }

    public function testUpdate() {
        $objectID = $this->createServer();

        $itself = $this->category->update(
            $objectID,
            'C__CATG__GLOBAL',
            [
                'cmdb_status' => 10
            ]
        );

        $this->assertInstanceOf(CMDBCategory::class, $itself);
    }

    public function testArchive() {
        $objectID = $this->createServer();
        $entryID = $this->addIPv4($objectID);

        $itself = $this->category->archive(
            $objectID,
            'C__CATG__IP',
            $entryID
        );

        $this->assertInstanceOf(CMDBCategory::class, $itself);
    }

    public function testDelete() {
        $objectID = $this->createServer();
        $entryID = $this->addIPv4($objectID);

        $itself = $this->category->delete(
            $objectID,
            'C__CATG__IP',
            $entryID
        );

        $this->assertInstanceOf(CMDBCategory::class, $itself);
    }

    public function testPurge() {
        $objectID = $this->createServer();
        $entryID = $this->addIPv4($objectID);

        $itself = $this->category->purge(
            $objectID,
            'C__CATG__IP',
            $entryID
        );

        $this->assertInstanceOf(CMDBCategory::class, $itself);
    }

    public function testBatchCreate() {
        // @todo Implement me!
    }

    public function testBatchRead() {
        $objectID1 = $this->createServer();
        $objectID2 = $this->createServer();
        $this->addIPv4($objectID1);
        $this->addIPv4($objectID2);
        $this->defineModel($objectID1);
        $this->defineModel($objectID2);

        $batchResult = $this->category->batchRead(
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

    public function testBatchUpdate() {
        // @todo Implement me!
    }

    public function testBatchArchive() {
        // @todo Implement me!
    }

    public function testBatchDelete() {
        // @todo Implement me!
    }

    public function testBatchPurge() {
        // @todo Implement me!
    }

}
