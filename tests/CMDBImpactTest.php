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

use bheisig\idoitapi\CMDBImpact;

/**
 * @coversDefaultClass \bheisig\idoitapi\CMDBImpact
 */
class CMDBImpactTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\CMDBImpact
     */
    protected $instance;

    /**
     * @throws \Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->instance = new CMDBImpact($this->api);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadByIdentifier() {
        $relationType = 10; // Location

        $objectID = $this->createServer();
        $rootLocationID = $this->getRootLocation();
        $roomID = $this->cmdbObject->create(
            'C__OBJTYPE__ROOM',
            $this->generateRandomString()
        );

        $this->addObjectToLocation($roomID, $rootLocationID);
        $this->addObjectToLocation($objectID, $roomID);

        $result = $this->instance->readByID(
            $roomID,
            $relationType
        );

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        foreach ($result as $relation) {
            $this->isRelation($relation);
            $this->assertSame($objectID, $relation['id']);
            $this->assertSame($objectID, $relation['data']['objID']);
        }
    }

    /**
     * @throws \Exception on error
     */
    public function testReadByConstant() {
        $relationType = 'C__RELATION_TYPE__LOCATION';

        $objectID = $this->createServer();
        $rootLocationID = $this->getRootLocation();
        $roomID = $this->cmdbObject->create(
            'C__OBJTYPE__ROOM',
            $this->generateRandomString()
        );

        $this->addObjectToLocation($roomID, $rootLocationID);
        $this->addObjectToLocation($objectID, $roomID);

        $result = $this->instance->readByConst(
            $roomID,
            $relationType
        );

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        foreach ($result as $relation) {
            $this->isRelation($relation);
            $this->assertSame($objectID, $relation['id']);
            $this->assertSame($objectID, $relation['data']['objID']);
        }
    }

    /**
     * @throws \Exception on error
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

        $this->assertInternalType('array', $result);
        // Only user has an impact:
        $this->assertCount(1, $result);

        foreach ($result as $relation) {
            $this->isRelation($relation);
            $this->assertSame($user['id'], $relation['id']);
            $this->assertSame($user['id'], $relation['data']['objID']);
        }
    }

    /**
     * @throws \Exception on error
     */
    public function readByInvalidRelationTypeIdentifiers() {
        $invalidRelationTypes = [
            -1,
            0
        ];

        $objectID = $this->createServer();

        foreach ($invalidRelationTypes as $invalidRelationType) {
            try {
                $this->instance->readByID(
                    $objectID,
                    $invalidRelationType
                );
            } catch (\Exception $e) {
                $this->expectException(\RuntimeException::class);
            }
        }
    }

    /**
     * @throws \Exception on error
     */
    public function readByInvalidRelationTypeConstants() {
        $invalidRelationTypes = [
            '0',
            '1',
            $this->generateRandomString(),
            '',
            'NULL',
            'C__RELATION_TYPE__UNKNOWN'
        ];

        $objectID = $this->createServer();

        foreach ($invalidRelationTypes as $invalidRelationType) {
            try {
                $this->instance->readByConst(
                    $objectID,
                    $invalidRelationType
                );
            } catch (\Exception $e) {
                $this->expectException(\RuntimeException::class);
            }
        }
    }

    /**
     * @group unreleased
     * @group API-71
     * @throws \Exception on error
     */
    public function testFilterByStatusNormal() {
        $relationType = 'C__RELATION_TYPE__LOCATION';

        $objectID = $this->createServer();
        $rootLocationID = $this->getRootLocation();
        $roomID = $this->cmdbObject->create(
            'C__OBJTYPE__ROOM',
            $this->generateRandomString()
        );

        $this->addObjectToLocation($roomID, $rootLocationID);
        $this->addObjectToLocation($objectID, $roomID);

        $result = $this->instance->readByConst(
            $roomID,
            $relationType,
            2
        );

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        foreach ($result as $relation) {
            $this->isRelation($relation);
            $this->assertSame($objectID, $relation['id']);
            $this->assertSame($objectID, $relation['data']['objID']);
        }
    }

    /**
     * @group unreleased
     * @group API-71
     * @throws \Exception on error
     */
    public function testFilterByStatusArchived() {
        $relationType = 'C__RELATION_TYPE__LOCATION';

        $objectID = $this->createServer();
        $rootLocationID = $this->getRootLocation();
        $roomID = $this->cmdbObject->create(
            'C__OBJTYPE__ROOM',
            $this->generateRandomString()
        );

        $this->addObjectToLocation($roomID, $rootLocationID);
        $this->addObjectToLocation($objectID, $roomID);

        $this->cmdbObject->archive($objectID);

        $result = $this->instance->readByConst(
            $roomID,
            $relationType,
            3
        );

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        foreach ($result as $relation) {
            $this->isRelation($relation);
            $this->assertSame($objectID, $relation['id']);
            $this->assertSame($objectID, $relation['data']['objID']);
        }
    }

    /**
     * @group unreleased
     * @group API-71
     * @throws \Exception on error
     */
    public function testFilterByStatusDeleted() {
        $relationType = 'C__RELATION_TYPE__LOCATION';

        $objectID = $this->createServer();
        $rootLocationID = $this->getRootLocation();
        $roomID = $this->cmdbObject->create(
            'C__OBJTYPE__ROOM',
            $this->generateRandomString()
        );

        $this->addObjectToLocation($roomID, $rootLocationID);
        $this->addObjectToLocation($objectID, $roomID);

        $this->cmdbObject->delete($objectID);

        $result = $this->instance->readByConst(
            $roomID,
            $relationType,
            4
        );

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        foreach ($result as $relation) {
            $this->isRelation($relation);
            $this->assertSame($objectID, $relation['id']);
            $this->assertSame($objectID, $relation['data']['objID']);
        }
    }

    /**
     * @group unreleased
     * @group API-71
     * @throws \Exception on error
     */
    public function testFilterByInvalidStatus() {
        $invalidStatus = [
            -1,
            0,
            1,
            5,
            6,
            7
        ];

        $relationType = 'C__RELATION_TYPE__LOCATION';

        $objectID = $this->createServer();
        $rootLocationID = $this->getRootLocation();
        $roomID = $this->cmdbObject->create(
            'C__OBJTYPE__ROOM',
            $this->generateRandomString()
        );

        $this->addObjectToLocation($roomID, $rootLocationID);
        $this->addObjectToLocation($objectID, $roomID);

        foreach ($invalidStatus as $status) {
            try {
                $this->instance->readByConst(
                    $roomID,
                    $relationType,
                    $status
                );
            } catch (\Exception $e) {
                $this->expectException(\RuntimeException::class);
            }
        }
    }

    protected function isRelation(array $relation) {
        $this->assertArrayHasKey('id', $relation);
        $this->isID($relation['id']);

        $this->assertArrayHasKey('data', $relation);
        $this->assertInternalType('array', $relation['data']);

        $this->assertArrayHasKey('relation', $relation['data']);
        $this->assertInternalType('array', $relation['data']['relation']);

        $this->assertArrayHasKey('type', $relation['data']['relation']);
        $this->assertInternalType('string', $relation['data']['relation']['type']);
        $this->isOneLiner($relation['data']['relation']['type']);

        $this->assertArrayHasKey('text', $relation['data']['relation']);
        $this->assertInternalType('string', $relation['data']['relation']['text']);
        $this->isOneLiner($relation['data']['relation']['text']);

        $this->assertArrayHasKey('color', $relation['data']);
        $this->assertInternalType('string', $relation['data']['color']);
        $this->isOneLiner($relation['data']['color']);

        $this->assertArrayHasKey('statusColor', $relation['data']);
        $this->assertInternalType('string', $relation['data']['statusColor']);
        $this->isOneLiner($relation['data']['statusColor']);

        $this->assertArrayHasKey('objTypeID', $relation['data']);
        $this->assertInternalType('integer', $relation['data']['objTypeID']);
        $this->isID($relation['data']['objTypeID']);

        $this->assertArrayHasKey('objectType', $relation['data']);
        $this->assertInternalType('string', $relation['data']['objectType']);
        $this->isOneLiner($relation['data']['objectType']);

        $this->assertArrayHasKey('objID', $relation['data']);
        $this->isID($relation['data']['objID']);
    }

}
