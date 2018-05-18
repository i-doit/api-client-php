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

use bheisig\idoitapi\CMDBObjects;

/**
 * @coversDefaultClass \bheisig\idoitapi\CMDBObjects
 */
class CMDBObjectsTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\CMDBObjects
     */
    protected $instance;

    /**
     * @throws \Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->instance = new CMDBObjects($this->api);
    }

    /**
     * @throws \Exception on error
     */
    public function testCreate() {
        $objectIDs = $this->instance->create(
            [
                ['type' => 'C__OBJTYPE__SERVER', 'title' => 'Server No. One'],
                ['type' => 'C__OBJTYPE__SERVER', 'title' => 'Server No. Two'],
                ['type' => 'C__OBJTYPE__SERVER', 'title' => 'Server No. Three']
            ]
        );

        $this->assertInternalType('array', $objectIDs);
        $this->assertCount(3, $objectIDs);

        foreach ($objectIDs as $objectID) {
            $this->assertInternalType('int', $objectID);
            $this->assertGreaterThan(0, $objectID);
        }
    }

    /**
     * @throws \Exception on error
     */
    public function testRead() {
        $objects = $this->instance->read();

        $this->assertInternalType('array', $objects);
        $this->assertNotCount(0, $objects);

        foreach ($objects as $object) {
            $this->validateObject($object);
        }
    }

    protected function validateObject($object) {
        $this->assertArrayHasKey('id', $object);
        $this->assertInternalType('string', $object['id']);
        $objectID = (int) $object['id'];
        $this->assertGreaterThan(0, $objectID);

        $this->assertArrayHasKey('title', $object);
        $this->assertInternalType('string', $object['title']);
        $this->assertNotEmpty($object['title']);

        $this->assertArrayHasKey('sysid', $object);
        $this->assertInternalType('string', $object['sysid']);
        $this->assertNotEmpty($object['sysid']);

        $this->assertArrayHasKey('type', $object);
        $this->assertInternalType('string', $object['type']);
        $objectTypeID = (int) $object['type'];
        $this->assertGreaterThan(0, $objectTypeID);

        $this->assertArrayHasKey('created', $object);
        $this->assertInternalType('string', $object['created']);
        $this->validateTime($object['created']);

        if (array_key_exists('updated', $object)) {
            $this->assertInternalType('string', $object['updated']);
            $this->validateTime($object['updated']);
        }

        $this->assertArrayHasKey('type_title', $object);
        $this->assertInternalType('string', $object['type_title']);
        $this->assertNotEmpty($object['type_title']);

        $this->assertArrayHasKey('type_group_title', $object);
        $this->assertInternalType('string', $object['type_group_title']);
        $this->assertNotEmpty($object['type_group_title']);

        $this->assertArrayHasKey('status', $object);
        $this->assertInternalType('string', $object['status']);
        $this->assertContains($object['status'], [
            '1', // Unfinished
            '2', // Normal
            '3', // Archived
            '4', // Deleted
            '6', // Template
            '7' // Mass change template
        ]);

        $this->assertArrayHasKey('cmdb_status', $object);
        $this->assertInternalType('string', $object['cmdb_status']);
        $cmdbStatusID = (int) $object['cmdb_status'];
        $this->assertGreaterThan(0, $cmdbStatusID);

        $this->assertArrayHasKey('cmdb_status_title', $object);
        $this->assertInternalType('string', $object['cmdb_status_title']);
        $this->assertNotEmpty($object['cmdb_status_title']);

        $this->assertArrayHasKey('image', $object);
        $this->assertInternalType('string', $object['image']);
        $this->assertNotEmpty($object['image']);
    }

    protected function validateTime($time) {
        $timestamp = strtotime($time);
        $this->assertInternalType('int', $timestamp);
        $formattedTimestamp = date('Y-m-d H:i:s', $timestamp);
        $this->assertInternalType('string', $formattedTimestamp);
        $this->assertSame($formattedTimestamp, $time);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadSome() {
        $objects = $this->instance->read([], 10, 0, 'title', CMDBObjects::SORT_DESCENDING);

        $this->assertInternalType('array', $objects);
        $this->assertCount(10, $objects);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadByIDs() {
        $objectIDs = $this->instance->create(
            [
                ['type' => 'C__OBJTYPE__SERVER', 'title' => 'Server No. Four'],
                ['type' => 'C__OBJTYPE__SERVER', 'title' => 'Server No. Five'],
                ['type' => 'C__OBJTYPE__SERVER', 'title' => 'Server No. Six']
            ]
        );

        $objects = $this->instance->readByIDs($objectIDs);

        $this->assertInternalType('array', $objects);
        $this->assertCount(3, $objects);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadByType() {
        $objects = $this->instance->readByType('C__OBJTYPE__PERSON');

        $this->assertInternalType('array', $objects);
        $this->assertNotCount(0, $objects);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadArchived() {
        $objects = $this->instance->readArchived();

        $this->assertInternalType('array', $objects);

        $objects = $this->instance->readArchived('C__OBJTYPE__PERSON');

        $this->assertInternalType('array', $objects);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadDeleted() {
        $objects = $this->instance->readDeleted();

        $this->assertInternalType('array', $objects);

        $objects = $this->instance->readDeleted('C__OBJTYPE__PERSON');

        $this->assertInternalType('array', $objects);
    }

    /**
     * @throws \Exception on error
     */
    public function testUpdate() {
        $objectIDs = $this->instance->create(
            [
                ['type' => 'C__OBJTYPE__SERVER', 'title' => 'Server No. Seven'],
                ['type' => 'C__OBJTYPE__SERVER', 'title' => 'Server No. Eight'],
                ['type' => 'C__OBJTYPE__SERVER', 'title' => 'Server No. Nine']
            ]
        );

        $result = $this->instance->update([
            ['id' => $objectIDs[0], 'title' => 'Server No. Ten'],
            ['id' => $objectIDs[1], 'title' => 'Server No. Eleven'],
            ['id' => $objectIDs[2], 'title' => 'Server No. Twelve'],
        ]);

        $this->assertInstanceOf(
            CMDBObjects::class,
            $result
        );
    }

    /**
     * @throws \Exception on error
     */
    public function testArchive() {
        $objectIDs = $this->instance->create(
            [
                ['type' => 'C__OBJTYPE__SERVER', 'title' => 'Archived Server One'],
                ['type' => 'C__OBJTYPE__SERVER', 'title' => 'Archived Server Two'],
                ['type' => 'C__OBJTYPE__SERVER', 'title' => 'Archived Server Three']
            ]
        );

        $this->assertInstanceOf(
            CMDBObjects::class,
            $this->instance->archive($objectIDs)
        );
    }

    /**
     * @throws \Exception on error
     */
    public function testDelete() {
        $objectIDs = $this->instance->create(
            [
                ['type' => 'C__OBJTYPE__SERVER', 'title' => 'Deleted Server One'],
                ['type' => 'C__OBJTYPE__SERVER', 'title' => 'Deleted Server Two'],
                ['type' => 'C__OBJTYPE__SERVER', 'title' => 'Deleted Server Three']
            ]
        );

        $this->assertInstanceOf(
            CMDBObjects::class,
            $this->instance->delete($objectIDs)
        );
    }

    /**
     * @throws \Exception on error
     */
    public function testPurge() {
        $objectIDs = $this->instance->create(
            [
                ['type' => 'C__OBJTYPE__SERVER', 'title' => 'Purged Server One'],
                ['type' => 'C__OBJTYPE__SERVER', 'title' => 'Purged Server Two'],
                ['type' => 'C__OBJTYPE__SERVER', 'title' => 'Purged Server Three']
            ]
        );

        $this->assertInstanceOf(
            CMDBObjects::class,
            $this->instance->purge($objectIDs)
        );
    }

    /**
     * @throws \Exception on error
     */
    public function testGetID() {
        $uniqueTitle = 'Server No. ' . microtime(true);

        $objectIDs = $this->instance->create(
            [
                ['type' => 'C__OBJTYPE__SERVER', 'title' => $uniqueTitle]
            ]
        );

        $objectID = $this->instance->getID($uniqueTitle);

        $this->assertInternalType('int', $objectID);
        $this->assertSame($objectIDs[0], $objectID);
    }

}
