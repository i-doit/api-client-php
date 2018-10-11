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

use bheisig\idoitapi\CMDBObject;

/**
 * @coversDefaultClass \bheisig\idoitapi\CMDBObject
 */
class CMDBObjectTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\CMDBObject
     */
    protected $object;

    /**
     * @throws \Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->object = new CMDBObject($this->api);
    }

    /**
     * @throws \Exception on error
     */
    public function testCreate() {
        $objectID = $this->object->create(
            'C__OBJTYPE__SERVER',
            $this->generateRandomString()
        );

        $this->assertInternalType('int', $objectID);
        $this->isID($objectID);
    }

    /**
     * @throws \Exception on error
     */
    public function testCreateWithMoreAttributes() {
        $objectID = $this->object->create(
            'C__OBJTYPE__SERVER',
            $this->generateRandomString(),
            [
                'category' => 'Test',
                'purpose' => 'for reasons',
                'cmdb_status' => 9,
                'description' => $this->generateDescription()
            ]
        );

        $this->assertInternalType('int', $objectID);
        $this->isID($objectID);
    }

    /**
     * @group unreleased
     * @throws \Exception on error
     */
    public function testCreateWithCategories() {
        $result = $this->object->createWithCategories(
            'C__OBJTYPE__SERVER',
            $this->generateRandomString(),
            [
                'C__CATG__MODEL' => [
                    [
                        'manufacturer' => $this->generateRandomString(),
                        'title' => $this->generateRandomString()
                    ]
                ],
                'C__CATG__IP' => [
                    [
                        'net' => $this->getIPv4Net(),
                        'active' => 1,
                        'primary' => 1,
                        'net_type' => 1,
                        'ipv4_assignment' => 2,
                        'ipv4_address' => $this->generateIPv4Address(),
                        'description' => $this->generateDescription()
                    ],
                    [
                        'net' => $this->getIPv4Net(),
                        'active' => 1,
                        'primary' => 0,
                        'net_type' => 1,
                        'ipv4_assignment' => 2,
                        'ipv4_address' => $this->generateIPv4Address(),
                        'description' => $this->generateDescription()
                    ]
                ]
            ]
        );

        $this->assertArrayHasKey('id', $result);
        $this->isID($result['id']);

        $this->assertArrayHasKey('categories', $result);
        $this->assertInternalType('array', $result['categories']);
        $this->assertCount(2, $result['categories']);

        $this->assertArrayHasKey('C__CATG__MODEL', $result['categories']);
        $this->assertInternalType('array', $result['categories']['C__CATG__MODEL']);
        $this->assertCount(1, $result['categories']['C__CATG__MODEL']);
        $this->assertArrayHasKey(0, $result['categories']['C__CATG__MODEL']);
        $this->isID($result['categories']['C__CATG__MODEL'][0]);

        $this->assertArrayHasKey('C__CATG__IP', $result['categories']);
        $this->assertInternalType('array', $result['categories']['C__CATG__IP']);
        $this->assertCount(2, $result['categories']['C__CATG__IP']);
        $this->assertArrayHasKey(0, $result['categories']['C__CATG__IP']);
        $this->isID($result['categories']['C__CATG__IP'][0]);
        $this->assertArrayHasKey(1, $result['categories']['C__CATG__IP']);
        $this->isID($result['categories']['C__CATG__IP'][1]);

        // Verify entries:

        $objectID = $result['id'];
        $modelEntryID = (int) $result['categories']['C__CATG__MODEL'][0];
        $firstIPEntryID = $result['categories']['C__CATG__IP'][0];
        $secondIPEntryID = $result['categories']['C__CATG__IP'][0];

        $model = $this->cmdbCategory->readOneByID($objectID, 'C__CATG__MODEL', $modelEntryID);
        $this->assertArrayHasKey('id', $model);
        $this->isIDAsString($model['id']);
        $id = (int) $model['id'];
        $this->assertSame($modelEntryID, $id);

        $firstIPEntry = $this->cmdbCategory->readOneByID($objectID, 'C__CATG__IP', $firstIPEntryID);
        $this->assertArrayHasKey('id', $firstIPEntry);
        $this->isIDAsString($firstIPEntry['id']);
        $id = (int) $firstIPEntry['id'];
        $this->assertSame($firstIPEntryID, $id);

        $secondIPEntry = $this->cmdbCategory->readOneByID($objectID, 'C__CATG__IP', $secondIPEntryID);
        $this->assertArrayHasKey('id', $secondIPEntry);
        $this->isIDAsString($secondIPEntry['id']);
        $id = (int) $secondIPEntry['id'];
        $this->assertSame($secondIPEntryID, $id);
    }

    /**
     * @throws \Exception on error
     */
    public function testRead() {
        $objectID = $this->createServer();

        $result = $this->object->read($objectID);

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);

        $requiredKeys = [
            'id',
            'title',
            'sysid',
            'objecttype',
            'created',
            'type_title',
            'type_icon',
            'status',
            'cmdb_status',
            'cmdb_status_title',
            'image'
        ];

        $optionalKeys = [
            'updated'
        ];

        $keys = array_merge($requiredKeys, $optionalKeys);

        foreach (array_keys($result) as $key) {
            $this->assertContains($key, $keys);
        }

        foreach ($requiredKeys as $key) {
            $this->assertArrayHasKey($key, $result);
        }

        $this->assertInternalType('string', $result['id']);
        $id = (int) $result['id'];
        $this->assertGreaterThan(0, $id);

        $this->assertInternalType('string', $result['title']);

        $this->assertInternalType('string', $result['sysid']);

        $this->assertInternalType('string', $result['objecttype']);
        $typeID = (int) $result['objecttype'];
        $this->assertGreaterThan(0, $typeID);

        $this->assertInternalType('string', $result['type_title']);

        $this->assertInternalType('string', $result['type_icon']);

        $this->assertInternalType('string', $result['status']);
        $this->assertContains((int) $result['status'], $this->conditions);

        $this->assertInternalType('string', $result['created']);
        $this->isTime($result['created']);

        if (array_key_exists('updated', $result)) {
            $this->isTime($result['updated']);
        }

        $this->assertInternalType('string', $result['cmdb_status']);
        $cmdbStatus = (int) $result['cmdb_status'];
        $this->assertGreaterThan(0, $cmdbStatus);

        $this->assertInternalType('string', $result['cmdb_status_title']);

        $this->assertInternalType('string', $result['image']);
    }

    /**
     * @throws \Exception on error
     */
    public function testUpdate() {
        $objectID = $this->createServer();

        $this->assertInstanceOf(
            CMDBObject::class,
            $this->object->update($objectID, ['title' => 'Anne Admin'])
        );
    }

    /**
     * @throws \Exception on error
     */
    public function testArchive() {
        $objectID = $this->createServer();

        $this->assertInstanceOf(
            CMDBObject::class,
            $this->object->archive($objectID)
        );
    }

    /**
     * @throws \Exception on error
     */
    public function testDelete() {
        $objectID = $this->createServer();

        $this->assertInstanceOf(
            CMDBObject::class,
            $this->object->delete($objectID)
        );
    }

    /**
     * @throws \Exception on error
     */
    public function testPurge() {
        $objectID = $this->createServer();

        $this->assertInstanceOf(
            CMDBObject::class,
            $this->object->purge($objectID)
        );
    }

    /**
     * @throws \Exception on error
     */
    public function testLoad() {
        $objectID = $this->createServer();

        $result = $this->object->load($objectID);

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testUpsert() {
        $title = $this->generateRandomString();

        // Exists:
        $objectID = $this->object->create('C__OBJTYPE__SERVER', $title);
        $result = $this->object->upsert('C__OBJTYPE__SERVER', $title, ['purpose' => 'Private stuff']);

        $this->assertInternalType('int', $result);
        $this->assertSame($objectID, $result);

        // Does not exist:
        $result = $this->object->upsert('C__OBJTYPE__SERVER', $this->generateRandomString());

        $this->assertInternalType('int', $result);
        $this->assertGreaterThan(0, $result);
    }

}
