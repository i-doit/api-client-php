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
        $this->assertGreaterThanOrEqual(1, $objectID);

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
        $this->assertGreaterThanOrEqual(1, $objectID);
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
