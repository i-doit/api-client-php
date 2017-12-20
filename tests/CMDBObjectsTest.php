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

use bheisig\idoitapi\CMDBObjects;

class CMDBObjectsTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\CMDBObjects
     */
    protected $instance;

    public function setUp() {
        parent::setUp();

        $this->instance = new CMDBObjects($this->api);
    }

    /**
     * @throws \Exception
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
     * @throws \Exception
     */
    public function testRead() {
        $objects = $this->instance->read();

        $this->assertInternalType('array', $objects);
        $this->assertNotCount(0, $objects);

        $objects = $this->instance->read([], 10, 0, 'title', CMDBObjects::SORT_DESCENDING);

        $this->assertInternalType('array', $objects);
        $this->assertCount(10, $objects);
    }

    /**
     * @throws \Exception
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
     * @throws \Exception
     */
    public function testReadByType() {
        $objects = $this->instance->readByType('C__OBJTYPE__PERSON');

        $this->assertInternalType('array', $objects);
        $this->assertNotCount(0, $objects);
    }

    /**
     * @throws \Exception
     */
    public function testReadArchived() {
        $objects = $this->instance->readArchived();

        $this->assertInternalType('array', $objects);

        $objects = $this->instance->readArchived('C__OBJTYPE__PERSON');

        $this->assertInternalType('array', $objects);
    }

    /**
     * @throws \Exception
     */
    public function testReadDeleted() {
        $objects = $this->instance->readDeleted();

        $this->assertInternalType('array', $objects);

        $objects = $this->instance->readDeleted('C__OBJTYPE__PERSON');

        $this->assertInternalType('array', $objects);
    }

    /**
     * @throws \Exception
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
     * @throws \Exception
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
     * @throws \Exception
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
     * @throws \Exception
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
     * @throws \Exception
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
        $this->assertEquals($objectIDs[0], $objectID);
    }

}
