<?php

/**
 * Copyright (C) 2022 synetics GmbH
 * Copyright (C) 2016-2022 Benjamin Heisig
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
 * @copyright Copyright (C) 2022 synetics GmbH
 * @copyright Copyright (C) 2016-2022 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

declare(strict_types=1);

namespace bheisig\idoitapi;

use \Exception;
use \RuntimeException;
use bheisig\idoitapi\Constants\Category;
use bheisig\idoitapi\Constants\ObjectType;

/**
 * @coversDefaultClass \bheisig\idoitapi\CMDBImpact
 */
class CMDBImpactTest extends BaseTest {

    /**
     * @var CMDBImpact
     */
    protected $instance;

    /**
     * @throws Exception on error
     */
    public function setUp(): void {
        parent::setUp();

        $this->instance = new CMDBImpact($this->api);
    }

    /**
     * @throws Exception on error
     */
    public function testReadByIdentifier() {
        $relationType = 10; // Location

        $objectID = $this->createServer();
        $rootLocationID = $this->getRootLocation();
        $roomID = $this->useCMDBObject()->create(
            ObjectType::ROOM,
            $this->generateRandomString()
        );

        $this->addObjectToLocation($roomID, $rootLocationID);
        $this->addObjectToLocation($objectID, $roomID);

        $result = $this->instance->readByID(
            $roomID,
            $relationType
        );

        $this->assertIsArray($result);
        $this->assertCount(1, $result);

        foreach ($result as $relation) {
            $this->isRelation($relation);
            $this->assertSame($objectID, $relation['id']);
            $this->assertSame($objectID, $relation['data']['objID']);
        }
    }

    /**
     * @throws Exception on error
     */
    public function testReadByConstant() {
        $relationType = 'C__RELATION_TYPE__LOCATION';

        $objectID = $this->createServer();
        $rootLocationID = $this->getRootLocation();
        $roomID = $this->useCMDBObject()->create(
            ObjectType::ROOM,
            $this->generateRandomString()
        );

        $this->addObjectToLocation($roomID, $rootLocationID);
        $this->addObjectToLocation($objectID, $roomID);

        $result = $this->instance->readByConst(
            $roomID,
            $relationType
        );

        $this->assertIsArray($result);
        $this->assertCount(1, $result);

        foreach ($result as $relation) {
            $this->isRelation($relation);
            $this->assertSame($objectID, $relation['id']);
            $this->assertSame($objectID, $relation['data']['objID']);
        }
    }

    /**
     * @throws Exception on error
     */
    public function testReadByTypes() {
        $relationTypes = [
            'C__RELATION_TYPE__USER',
            'C__RELATION_TYPE__ADMIN'
        ];

        $objectID = $this->createServer();
        $this->isID($objectID);
        $admin = $this->createPerson();
        $user = $this->createPerson();

        $this->addContact($objectID, $admin['id'], 1);
        $this->addContact($objectID, $user['id'], 2);

        $result = $this->instance->readByTypes(
            $objectID,
            $relationTypes
        );

        $this->assertIsArray($result);
        // Only user has an impact:
        $this->assertCount(1, $result);

        foreach ($result as $relation) {
            $this->isRelation($relation);
            $this->assertSame($user['id'], $relation['id']);
            $this->assertSame($user['id'], $relation['data']['objID']);
        }
    }

    /**
     * @return array
     */
    public function provideInvalidRelationTypeIdentifiers(): array {
        return [
            '-1' => [-1],
            '0' => [0]
        ];
    }

    /**
     * @dataProvider provideInvalidRelationTypeIdentifiers
     * @param int $invalidRelationType
     * @throws Exception on error
     */
    public function readByInvalidRelationTypeIdentifier(int $invalidRelationType) {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();

        $this->instance->readByID(
            $objectID,
            $invalidRelationType
        );
    }

    /**
     * @return array
     */
    public function provideInvalidRelationsTypeConstants(): array {
        return [
            'zero' => ['0'],
            'one' => ['1'],
            'random string' => [$this->generateRandomString()],
            'empty string' => [''],
            'null' => ['NULL'],
            'unknown constant' => ['C__RELATION_TYPE__UNKNOWN']
        ];
    }

    /**
     * @dataProvider provideInvalidRelationsTypeConstants
     * @param string $invalidRelationType
     * @throws Exception on error
     */
    public function readByInvalidRelationTypeConstants(string $invalidRelationType) {
        $this->expectException(RuntimeException::class);

        $objectID = $this->createServer();

        $this->instance->readByConst(
            $objectID,
            $invalidRelationType
        );
    }

    /**
     * @group API-71
     * @throws Exception on error
     */
    public function testFilterByStatusNormal() {
        $relationType = 'C__RELATION_TYPE__USER';

        $objectID = $this->createServer();
        $this->isID($objectID);
        $user = $this->createPerson();

        $this->addContact($objectID, $user['id'], 2);

        $result = $this->instance->readByConst(
            $objectID,
            $relationType,
            2
        );

        $this->assertIsArray($result);
        $this->assertCount(1, $result);

        foreach ($result as $relation) {
            $this->isRelation($relation);
            $this->assertSame($user['id'], $relation['id']);
            $this->assertSame($user['id'], $relation['data']['objID']);
        }
    }

    /**
     * @group API-71
     * @throws Exception on error
     */
    public function testFilterByStatusArchived() {
        $relationType = 'C__RELATION_TYPE__USER';

        $objectID = $this->createServer();
        $this->isID($objectID);
        $user = $this->createPerson();

        $entryID = $this->addContact($objectID, $user['id'], 2);

        $this->useCMDBCategory()->archive($objectID, Category::CATG__CONTACT, $entryID);

        $result = $this->instance->readByConst(
            $objectID,
            $relationType,
            3
        );

        $this->assertIsArray($result);
        $this->assertCount(1, $result);

        foreach ($result as $relation) {
            $this->isRelation($relation);
            $this->assertSame($user['id'], $relation['id']);
            $this->assertSame($user['id'], $relation['data']['objID']);
        }

        // There must not be any entry with "normal" status:
        $result = $this->instance->readByConst(
            $objectID,
            $relationType,
            2
        );

        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }

    /**
     * @group API-71
     * @throws Exception on error
     */
    public function testFilterByStatusDeleted() {
        $relationType = 'C__RELATION_TYPE__USER';

        $objectID = $this->createServer();
        $this->isID($objectID);
        $user = $this->createPerson();

        $entryID = $this->addContact($objectID, $user['id'], 2);

        $this->useCMDBCategory()->delete($objectID, Category::CATG__CONTACT, $entryID);

        $result = $this->instance->readByConst(
            $objectID,
            $relationType,
            4
        );

        $this->assertIsArray($result);
        $this->assertCount(1, $result);

        foreach ($result as $relation) {
            $this->isRelation($relation);
            $this->assertSame($user['id'], $relation['id']);
            $this->assertSame($user['id'], $relation['data']['objID']);
        }

        // There must not be any entry with "normal" status:
        $result = $this->instance->readByConst(
            $objectID,
            $relationType,
            2
        );

        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }

    /**
     * @return array
     */
    public function provideInvalidStatus(): array {
        return [
            'negative integer' => [-1],
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
     * @throws Exception on error
     */
    public function testFilterByInvalidStatus(int $status) {
        $this->expectException(RuntimeException::class);

        $relationType = 'C__RELATION_TYPE__CLUSTER_MEMBERSHIPS';

        $hostID = $this->createServer();
        $this->isID($hostID);

        $clusterID = $this->useCMDBObject()->create(
            ObjectType::CLUSTER,
            $this->generateRandomString()
        );
        $this->isID($clusterID);

        $entryID = $this->useCMDBCategory()->save(
            $clusterID,
            Category::CATG__CLUSTER_MEMBERS,
            [
                'member' => [$hostID]
            ]
        );
        $this->isID($entryID);

        $this->instance->readByConst(
            $clusterID,
            $relationType,
            $status
        );
    }

    protected function isRelation(array $relation) {
        $this->assertArrayHasKey('id', $relation);
        $this->isID($relation['id']);

        $this->assertArrayHasKey('data', $relation);
        $this->assertIsArray($relation['data']);

        $this->assertArrayHasKey('relation', $relation['data']);
        $this->assertIsArray($relation['data']['relation']);

        $this->assertArrayHasKey('type', $relation['data']['relation']);
        $this->assertIsString($relation['data']['relation']['type']);
        $this->isOneLiner($relation['data']['relation']['type']);

        $this->assertArrayHasKey('text', $relation['data']['relation']);
        $this->assertIsString($relation['data']['relation']['text']);
        $this->isOneLiner($relation['data']['relation']['text']);

        $this->assertArrayHasKey('color', $relation['data']);
        $this->assertIsString($relation['data']['color']);
        $this->isOneLiner($relation['data']['color']);

        $this->assertArrayHasKey('statusColor', $relation['data']);
        $this->assertIsString($relation['data']['statusColor']);
        $this->isOneLiner($relation['data']['statusColor']);

        $this->assertArrayHasKey('objTypeID', $relation['data']);
        $this->assertIsInt($relation['data']['objTypeID']);
        $this->isID($relation['data']['objTypeID']);

        $this->assertArrayHasKey('objectType', $relation['data']);
        $this->assertIsString($relation['data']['objectType']);
        $this->isOneLiner($relation['data']['objectType']);

        $this->assertArrayHasKey('objID', $relation['data']);
        $this->isID($relation['data']['objID']);
    }

}
