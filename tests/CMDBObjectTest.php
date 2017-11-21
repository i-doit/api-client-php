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

use bheisig\idoitapi\CMDBObject;

class CMDBObjectTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\CMDBObject
     */
    protected $object;

    public function setUp() {
        parent::setUp();

        $this->object = new CMDBObject($this->api);
    }

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

    public function testRead() {
        $objectID = $this->createServer();

        $result = $this->object->read($objectID);

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
    }

    public function testUpdate() {
        $objectID = $this->createServer();

        $this->assertInstanceOf(
            CMDBObject::class,
            $this->object->update($objectID, ['title' => 'Anne Admin'])
        );
    }

    public function testArchive() {
        $objectID = $this->createServer();

        $this->assertInstanceOf(
            CMDBObject::class,
            $this->object->archive($objectID)
        );
    }

    public function testDelete() {
        $objectID = $this->createServer();

        $this->assertInstanceOf(
            CMDBObject::class,
            $this->object->delete($objectID)
        );
    }

    public function testPurge() {
        $objectID = $this->createServer();

        $this->assertInstanceOf(
            CMDBObject::class,
            $this->object->purge($objectID)
        );
    }

    public function testLoad() {
        $objectID = $this->createServer();

        $result = $this->object->load($objectID);

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
    }

    public function testUpsert() {
        $title = $this->generateRandomString();

        // Exists:
        $objectID = $this->object->create('C__OBJTYPE__SERVER', $title);
        $result = $this->object->upsert('C__OBJTYPE__SERVER', $title, ['purpose' => 'Private stuff']);

        $this->assertInternalType('int', $result);
        $this->assertEquals($objectID, $result);

        // Does not exist:
        $result = $this->object->upsert('C__OBJTYPE__SERVER', $this->generateRandomString());

        $this->assertInternalType('int', $result);
        $this->assertGreaterThan(0, $result);
    }

}
