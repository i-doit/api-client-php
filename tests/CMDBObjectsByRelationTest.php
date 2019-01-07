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

use bheisig\idoitapi\CMDBObjectsByRelation;

/**
 * @coversDefaultClass \bheisig\idoitapi\CMDBObjectsByRelation
 */
class CMDBObjectsByRelationTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\CMDBObjectsByRelation
     */
    protected $cmdbObjectsByRelation;

    /**
     * @throws \Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->cmdbObjectsByRelation = new CMDBObjectsByRelation($this->api);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadByIdentifier() {
        $relationType = 10; // Location

        $objectID = $this->createServer();
        $locationID = $this->getRootLocation();
        $this->addObjectToLocation($objectID, $locationID);

        $result = $this->cmdbObjectsByRelation->readByID(
            $objectID,
            $relationType
        );

        $this->assertIsArray($result);
        $this->assertCount(1, $result);

        $first = end($result);

        $this->assertArrayHasKey('data', $first);
        $this->assertIsArray($first['data']);
        $this->assertArrayHasKey('children', $first);
        $this->assertIsBool($first['children']);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadByConstant() {
        $relationType = 'C__RELATION_TYPE__LOCATION';

        $objectID = $this->createServer();
        $locationID = $this->getRootLocation();
        $this->addObjectToLocation($objectID, $locationID);

        $result = $this->cmdbObjectsByRelation->readByConst(
            $objectID,
            $relationType
        );

        $this->assertIsArray($result);
        $this->assertCount(1, $result);

        $first = end($result);

        $this->assertArrayHasKey('data', $first);
        $this->assertIsArray($first['data']);
        $this->assertArrayHasKey('children', $first);
        $this->assertIsBool($first['children']);
    }

    /**
     * @group API-71
     * @throws \Exception on error
     */
    public function testFilterByStatusNormal() {
        $relationType = 'C__RELATION_TYPE__LOCATION';

        $objectID = $this->createServer();
        $locationID = $this->getRootLocation();
        $this->addObjectToLocation($objectID, $locationID);

        $result = $this->cmdbObjectsByRelation->readByConst(
            $objectID,
            $relationType,
            2
        );

        $this->assertIsArray($result);
        $this->assertCount(1, $result);

        $first = end($result);

        $this->assertArrayHasKey('data', $first);
        $this->assertIsArray($first['data']);
        $this->assertArrayHasKey('children', $first);
        $this->assertIsBool($first['children']);
    }

    /**
     * @group API-71
     * @throws \Exception on error
     */
    public function testFilterByStatusArchived() {
        $hostID = $this->createServer();
        $admin = $this->createPerson();
        $entryID = $this->addContact($hostID, $admin['id'], 1);
        $this->cmdbCategory->archive($hostID, 'C__CATG__CONTACT', $entryID);

        $result = $this->cmdbObjectsByRelation->readByConst(
            $hostID,
            'C__RELATION_TYPE__ADMIN',
            3
        );

        $this->assertIsArray($result);
        $this->assertCount(1, $result);

        $first = end($result);

        $this->assertArrayHasKey('data', $first);
        $this->assertIsArray($first['data']);
        $this->assertArrayHasKey('children', $first);
        $this->assertIsBool($first['children']);
    }

    /**
     * @group API-71
     * @throws \Exception on error
     */
    public function testFilterByStatusDeleted() {
        $hostID = $this->createServer();
        $admin = $this->createPerson();
        $entryID = $this->addContact($hostID, $admin['id'], 1);
        $this->cmdbCategory->delete($hostID, 'C__CATG__CONTACT', $entryID);

        $result = $this->cmdbObjectsByRelation->readByConst(
            $hostID,
            'C__RELATION_TYPE__ADMIN',
            4
        );

        $this->assertIsArray($result);
        $this->assertCount(1, $result);

        $first = end($result);

        $this->assertArrayHasKey('data', $first);
        $this->assertIsArray($first['data']);
        $this->assertArrayHasKey('children', $first);
        $this->assertIsBool($first['children']);
    }

    /**
     * @return array
     */
    public function provideInvalidStatus(): array {
        return [
            'negative' => [-1],
            'zero' => [0],
            'unfinished' => [1],
            'purged' => [5],
            'template' => [6],
            'mass change template' => [7]
        ];
    }

    /**
     * @group API-71
     * @dataProvider provideInvalidStatus
     * @param int $status
     * @throws \Exception on error
     */
    public function testFilterByInvalidStatus(int $status) {
        $this->expectException(\RuntimeException::class);

        $hostID = $this->createServer();
        $admin = $this->createPerson();
        $this->addContact($hostID, $admin['id'], 1);

        $this->cmdbObjectsByRelation->readByConst(
            $hostID,
            'C__RELATION_TYPE__ADMIN',
            $status
        );
    }

}
