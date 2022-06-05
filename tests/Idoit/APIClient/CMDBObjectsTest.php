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
 * @link https://github.com/i-doit/api-client-php
 */

declare(strict_types=1);

namespace Idoit\APIClient;

use \Exception;
use Idoit\APIClient\Constants\Category;
use Idoit\APIClient\Constants\ObjectType;

/**
 * @coversDefaultClass \Idoit\APIClient\CMDBObjects
 */
class CMDBObjectsTest extends BaseTest {

    /**
     * @var CMDBObjects
     */
    protected $instance;

    /**
     * @throws Exception on error
     */
    public function setUp(): void {
        parent::setUp();

        $this->instance = new CMDBObjects($this->api);
    }

    /**
     * @throws Exception on error
     * @group API-81
     */
    public function testCreate() {
        $objectIDs = $this->instance->create(
            [
                ['type' => ObjectType::SERVER, 'title' => 'Server No. One'],
                ['type' => ObjectType::SERVER, 'title' => 'Server No. Two'],
                ['type' => ObjectType::SERVER, 'title' => 'Server No. Three']
            ]
        );

        $this->assertIsArray($objectIDs);
        $this->assertCount(3, $objectIDs);

        foreach ($objectIDs as $objectID) {
            $this->assertIsInt($objectID);
            $this->isID($objectID);
        }
    }

    /**
     * @throws Exception on error
     * @group API-81
     */
    public function testRead() {
        $objects = $this->instance->read();

        $this->assertIsArray($objects);
        $this->assertNotCount(0, $objects);

        foreach ($objects as $object) {
            $this->isObject($object);
        }
    }

    /**
     * @throws Exception on error
     */
    public function testReadSome() {
        $objects = $this->instance->read([], 10, 0, 'title', CMDBObjects::SORT_DESCENDING);

        $this->assertIsArray($objects);
        $this->assertCount(10, $objects);

        foreach ($objects as $object) {
            $this->isObject($object);
        }
    }

    /**
     * @throws Exception on error
     */
    public function testReadByIdentifiers() {
        $objectIDs = $this->instance->create(
            [
                ['type' => ObjectType::SERVER, 'title' => 'Server No. Four'],
                ['type' => ObjectType::SERVER, 'title' => 'Server No. Five'],
                ['type' => ObjectType::SERVER, 'title' => 'Server No. Six']
            ]
        );

        $objects = $this->instance->readByIDs($objectIDs);

        $this->assertIsArray($objects);
        $this->assertCount(3, $objects);

        foreach ($objects as $object) {
            $this->isObject($object);
        }
    }

    /**
     * @throws Exception on error
     */
    public function testReadByType() {
        $objects = $this->instance->readByType(ObjectType::PERSON);

        $this->assertIsArray($objects);
        $this->assertNotCount(0, $objects);

        foreach ($objects as $object) {
            $this->isObject($object);
        }
    }

    /**
     * @throws Exception on error
     */
    public function testReadArchived() {
        $objects = $this->instance->readArchived();

        $this->assertIsArray($objects);

        $objects = $this->instance->readArchived(ObjectType::PERSON);

        $this->assertIsArray($objects);

        foreach ($objects as $object) {
            $this->isObject($object);
        }
    }

    /**
     * @throws Exception on error
     */
    public function testReadDeleted() {
        $objects = $this->instance->readDeleted();

        $this->assertIsArray($objects);

        $objects = $this->instance->readDeleted(ObjectType::PERSON);

        $this->assertIsArray($objects);

        foreach ($objects as $object) {
            $this->isObject($object);
        }
    }

    /**
     * @group API-83
     * @throws Exception on error
     */
    public function testReadWithSomeCategories() {
        $objectID = $this->createServer();
        $this->isID($objectID);
        $person = $this->createPerson();
        $this->defineModel($objectID);
        $this->addIPv4($objectID);
        $this->addContact($objectID, $person['id']);

        $categoryConstants = [
            Category::CATG__MODEL,
            Category::CATG__IP,
            Category::CATG__CONTACT
        ];

        $result = $this->useCMDBObjects()->read(['ids' => [$objectID]], null, null, null, null, $categoryConstants);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey(0, $result);
        $this->assertIsArray($result[0]);
        $this->isObject($result[0]);
        $this->assertSame($objectID, $result[0]['id']);

        $this->assertArrayHasKey('categories', $result[0]);
        $this->assertIsArray($result[0]['categories']);
        $this->assertCount(3, $result[0]['categories']);

        foreach ($result[0]['categories'] as $categoryConstant => $entries) {
            $this->assertIsString($categoryConstant);
            $this->assertContains($categoryConstant, $categoryConstants);

            $this->assertIsArray($entries);
            $this->assertNotCount(0, $entries);

            foreach ($entries as $index => $entry) {
                $this->assertIsInt($index);
                $this->assertGreaterThanOrEqual(0, $index);

                $this->assertIsArray($entry);
                $this->isCategoryEntry($entry);
                $this->assertSame($objectID, (int) $entry['objID']);
            }
        }
    }

    /**
     * @group API-83
     * @throws Exception on error
     */
    public function testReadWithAllCategories() {
        $objectID = $this->createServer();
        $this->isID($objectID);
        $person = $this->createPerson();
        $this->defineModel($objectID);
        $this->addIPv4($objectID);
        $this->addContact($objectID, $person['id']);

        $result = $this->useCMDBObjects()->read(['ids' => [$objectID]], null, null, null, null, true);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey(0, $result);
        $this->assertIsArray($result[0]);
        $this->isObject($result[0]);
        $this->assertSame($objectID, $result[0]['id']);

        $this->assertArrayHasKey('categories', $result[0]);
        $this->assertIsArray($result[0]['categories']);
        $this->assertGreaterThanOrEqual(3, count($result[0]['categories']));

        $categoryInfo = new CMDBCategoryInfo($this->api);
        $blacklistedCategoryConstants = $categoryInfo->getVirtualCategoryConstants();

        foreach ($result[0]['categories'] as $categoryConstant => $entries) {
            $this->assertIsString($categoryConstant);
            $this->isConstant($categoryConstant);

            $this->assertNotContains($categoryConstant, $blacklistedCategoryConstants);

            $this->assertIsArray($entries);

            foreach ($entries as $index => $entry) {
                $this->assertIsInt($index);
                $this->assertGreaterThanOrEqual(0, $index);

                $this->assertIsArray($entry);
                $this->isCategoryEntry($entry);

                if ($categoryConstant === Category::CATG__RELATION) {
                    continue;
                }

                $this->assertSame($objectID, (int) $entry['objID']);
            }
        }
    }

    /**
     * @throws Exception on error
     */
    public function testUpdate() {
        $objectIDs = $this->instance->create(
            [
                ['type' => ObjectType::SERVER, 'title' => 'Server No. Seven'],
                ['type' => ObjectType::SERVER, 'title' => 'Server No. Eight'],
                ['type' => ObjectType::SERVER, 'title' => 'Server No. Nine']
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
     * @throws Exception on error
     * @group API-88
     */
    public function testArchive() {
        $objectIDs = $this->instance->create(
            [
                ['type' => ObjectType::SERVER, 'title' => 'Archived Server One'],
                ['type' => ObjectType::SERVER, 'title' => 'Archived Server Two'],
                ['type' => ObjectType::SERVER, 'title' => 'Archived Server Three']
            ]
        );

        $this->assertInstanceOf(
            CMDBObjects::class,
            $this->instance->archive($objectIDs)
        );
    }

    /**
     * @throws Exception on error
     * @group API-89
     */
    public function testDelete() {
        $objectIDs = $this->instance->create(
            [
                ['type' => ObjectType::SERVER, 'title' => 'Deleted Server One'],
                ['type' => ObjectType::SERVER, 'title' => 'Deleted Server Two'],
                ['type' => ObjectType::SERVER, 'title' => 'Deleted Server Three']
            ]
        );

        $this->assertInstanceOf(
            CMDBObjects::class,
            $this->instance->delete($objectIDs)
        );
    }

    /**
     * @throws Exception on error
     * @group API-90
     */
    public function testPurge() {
        $objectIDs = $this->instance->create(
            [
                ['type' => ObjectType::SERVER, 'title' => 'Purged Server One'],
                ['type' => ObjectType::SERVER, 'title' => 'Purged Server Two'],
                ['type' => ObjectType::SERVER, 'title' => 'Purged Server Three']
            ]
        );

        $this->assertInstanceOf(
            CMDBObjects::class,
            $this->instance->purge($objectIDs)
        );
    }

    /**
     * @throws Exception on error
     * @group API-91
     */
    public function testRecycle() {
        $objectIDs = $this->instance->create(
            [
                ['type' => ObjectType::SERVER, 'title' => 'Purged Server One'],
                ['type' => ObjectType::SERVER, 'title' => 'Purged Server Two'],
                ['type' => ObjectType::SERVER, 'title' => 'Purged Server Three']
            ]
        );

        $this->instance->archive($objectIDs);

        $result = $this->instance->recycle($objectIDs);

        $this->assertInstanceOf(
            CMDBObjects::class,
            $result
        );
    }

    /**
     * @throws Exception on error
     * @group API-81
     */
    public function testGetIdentifier() {
        $uniqueTitle = 'Server No. ' . microtime(true);

        $objectIDs = $this->instance->create(
            [
                ['type' => ObjectType::SERVER, 'title' => $uniqueTitle]
            ]
        );

        $objectID = $this->instance->getID($uniqueTitle);

        $this->assertIsInt($objectID);
        $this->assertSame($objectIDs[0], $objectID);
    }

}
