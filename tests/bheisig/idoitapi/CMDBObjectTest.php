<?php

/**
 * Copyright (C) 2016-2020 Benjamin Heisig
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
 * @copyright Copyright (C) 2016-2020 Benjamin Heisig
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
 * @coversDefaultClass \bheisig\idoitapi\CMDBObject
 */
class CMDBObjectTest extends BaseTest {

    /**
     * @throws Exception on error
     * @group API-81
     */
    public function testCreate() {
        $objectID = $this->useCMDBObject()->create(
            ObjectType::SERVER,
            $this->generateRandomString()
        );

        $this->assertIsInt($objectID);
        $this->isID($objectID);
    }

    /**
     * @throws Exception on error
     */
    public function testCreateObjectForEveryType() {
        $objectTypeConstants = array_map(
            function($type) {
                return $type['const'];
            },
            (new CMDBObjectTypes($this->api))->read()
        );

        foreach ($objectTypeConstants as $objectTypeConstant) {
            $objectID = $this->useCMDBObject()->create($objectTypeConstant, $this->generateRandomString());
            $this->isID($objectID);
        }
    }

    /**
     * @throws Exception on error
     * @group API-81
     */
    public function testCreateWithMoreAttributes() {
        $objectID = $this->useCMDBObject()->create(
            ObjectType::SERVER,
            $this->generateRandomString(),
            [
                'category' => 'Test',
                'cmdb_status' => 9,
                'description' => $this->generateDescription(),
                'purpose' => 'for reasons',
                'sysid' => $this->generateRandomString()
            ]
        );

        $this->assertIsInt($objectID);
        $this->isID($objectID);
    }

    /**
     * @throws Exception on error
     */
    public function testCreateNormalObject() {
        $objectID = $this->useCMDBObject()->create(
            ObjectType::SERVER,
            $this->generateRandomString(),
            [
                'status' => 2
            ]
        );

        $this->isID($objectID);
        $this->isNormal($objectID);
    }

    /**
     * @throws Exception on error
     */
    public function testCreateArchivedObject() {
        $objectID = $this->useCMDBObject()->create(
            ObjectType::SERVER,
            $this->generateRandomString(),
            [
                'status' => 3
            ]
        );

        $this->isID($objectID);
        $this->isArchived($objectID);
    }

    /**
     * @throws Exception on error
     */
    public function testCreateDeletedObject() {
        $objectID = $this->useCMDBObject()->create(
            ObjectType::SERVER,
            $this->generateRandomString(),
            [
                'status' => 4
            ]
        );

        $this->isID($objectID);
        $this->isDeleted($objectID);
    }

    /**
     * @throws Exception on error
     */
    public function testCreateTemplate() {
        $objectID = $this->useCMDBObject()->create(
            ObjectType::SERVER,
            $this->generateRandomString(),
            [
                'status' => 6
            ]
        );

        $this->isID($objectID);
        $this->isTemplate($objectID);
    }

    /**
     * @throws Exception on error
     */
    public function testCreateMassChangeTemplate() {
        $objectID = $this->useCMDBObject()->create(
            ObjectType::SERVER,
            $this->generateRandomString(),
            [
                'status' => 7
            ]
        );

        $this->isID($objectID);
        $this->isMassChangeTemplate($objectID);
    }

    /**
     * @group API-84
     * @throws Exception on error
     */
    public function testCreateWithCategories() {
        $result = $this->useCMDBObject()->createWithCategories(
            ObjectType::SERVER,
            $this->generateRandomString(),
            [
                Category::CATG__MODEL => [
                    [
                        'manufacturer' => $this->generateRandomString(),
                        'title' => $this->generateRandomString()
                    ]
                ],
                Category::CATG__IP => [
                    [
                        'net' => $this->getIPv4Net(),
                        'active' => mt_rand(0, 1),
                        'primary' => 1,
                        'net_type' => 1,
                        'ipv4_assignment' => 2,
                        'ipv4_address' => $this->generateIPv4Address(),
                        'description' => $this->generateDescription()
                    ],
                    [
                        'net' => $this->getIPv4Net(),
                        'active' => mt_rand(0, 1),
                        'primary' => 0,
                        'net_type' => 1,
                        'ipv4_assignment' => 2,
                        'ipv4_address' => $this->generateIPv4Address(),
                        'description' => $this->generateDescription()
                    ]
                ]
            ]
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->isID($result['id']);

        $this->assertArrayHasKey('categories', $result);
        $this->assertIsArray($result['categories']);
        $this->assertCount(2, $result['categories']);

        $this->assertArrayHasKey(Category::CATG__MODEL, $result['categories']);
        $this->assertIsArray($result['categories'][Category::CATG__MODEL]);
        $this->assertCount(1, $result['categories'][Category::CATG__MODEL]);
        $this->assertArrayHasKey(0, $result['categories'][Category::CATG__MODEL]);
        $this->isID($result['categories'][Category::CATG__MODEL][0]);

        $this->assertArrayHasKey(Category::CATG__IP, $result['categories']);
        $this->assertIsArray($result['categories'][Category::CATG__IP]);
        $this->assertCount(2, $result['categories'][Category::CATG__IP]);
        $this->assertArrayHasKey(0, $result['categories'][Category::CATG__IP]);
        $this->isID($result['categories'][Category::CATG__IP][0]);
        $this->assertArrayHasKey(1, $result['categories'][Category::CATG__IP]);
        $this->isID($result['categories'][Category::CATG__IP][1]);

        // Verify entries:

        $objectID = $result['id'];
        $modelEntryID = (int) $result['categories'][Category::CATG__MODEL][0];
        $firstIPEntryID = $result['categories'][Category::CATG__IP][0];
        $secondIPEntryID = $result['categories'][Category::CATG__IP][0];

        $model = $this->useCMDBCategory()->readOneByID($objectID, Category::CATG__MODEL, $modelEntryID);
        $this->assertArrayHasKey('id', $model);
        $this->isIDAsString($model['id']);
        $id = (int) $model['id'];
        $this->assertSame($modelEntryID, $id);

        $firstIPEntry = $this->useCMDBCategory()->readOneByID($objectID, Category::CATG__IP, $firstIPEntryID);
        $this->assertArrayHasKey('id', $firstIPEntry);
        $this->isIDAsString($firstIPEntry['id']);
        $id = (int) $firstIPEntry['id'];
        $this->assertSame($firstIPEntryID, $id);

        $secondIPEntry = $this->useCMDBCategory()->readOneByID($objectID, Category::CATG__IP, $secondIPEntryID);
        $this->assertArrayHasKey('id', $secondIPEntry);
        $this->isIDAsString($secondIPEntry['id']);
        $id = (int) $secondIPEntry['id'];
        $this->assertSame($secondIPEntryID, $id);
    }

    /**
     * @throws Exception on error
     * @group API-81
     */
    public function testRead() {
        $objectID = $this->createServer();

        $result = $this->useCMDBObject()->read($objectID);

        $this->assertIsArray($result);
        $this->isOneObject($result);
    }

    /**
     * Validate common information about an object
     *
     * Note: There are differences between an object read by cmdb.object.read and read by cmdb.objects.read :-(
     *
     * @param array $object Common information about an object
     */
    protected function isOneObject(array $object) {
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

        foreach (array_keys($object) as $key) {
            $this->assertContains($key, $keys);
        }

        foreach ($requiredKeys as $key) {
            $this->assertArrayHasKey($key, $object);
        }

        $this->assertIsInt($object['id']);
        $this->isID($object['id']);

        $this->assertIsString($object['title']);

        $this->assertIsString($object['sysid']);

        $this->assertIsInt($object['objecttype']);
        $this->isID($object['objecttype']);

        $this->assertIsString($object['type_title']);

        $this->assertIsString($object['type_icon']);

        $this->assertIsInt($object['status']);
        $this->isID($object['status']);
        $this->assertContains($object['status'], $this->conditions);

        $this->assertIsString($object['created']);
        $this->isTime($object['created']);

        if (array_key_exists('updated', $object)) {
            $this->assertIsString($object['updated']);
            $this->isTime($object['updated']);
        }

        $this->assertIsInt($object['cmdb_status']);
        $this->isID($object['cmdb_status']);

        $this->assertIsString($object['cmdb_status_title']);

        $this->assertIsString($object['image']);
    }

    /**
     * @throws Exception on error
     */
    public function testUpdate() {
        $objectID = $this->createServer();

        $this->assertInstanceOf(
            CMDBObject::class,
            $this->useCMDBObject()->update($objectID, ['title' => 'Anne Admin'])
        );
    }

    /**
     * @throws Exception on error
     * @group API-81
     */
    public function testLoad() {
        $objectID = $this->createServer();

        $result = $this->useCMDBObject()->load($objectID);

        $this->assertIsArray($result);
        $this->assertNotCount(0, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testReadAll() {
        $objectIDs = array_map(
            function ($object) {
                return $object['id'];
            },
            // No, do not test every single object, but some recently created ones:
            $this->useCMDBObjects()->read([], 10, 0, 'id', CMDBObjects::SORT_DESCENDING)
        );

        $categoryInfo = new CMDBCategoryInfo($this->api);
        $blacklistedCategoryConstants = $categoryInfo->getVirtualCategoryConstants();

        foreach ($objectIDs as $objectID) {
            $result = $this->useCMDBObject()->readAll($objectID);

            $this->assertIsArray($result);
            $this->isObject($result);
            $this->assertSame($objectID, $result['id']);

            if (!array_key_exists('categories', $result)) {
                continue;
            }

            $this->assertIsArray($result['categories']);

            foreach ($result['categories'] as $categoryConstant => $entries) {
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
    }

    /**
     * @throws Exception on error
     */
    public function testReadAllFromNonExistingObject() {
        $this->expectException(RuntimeException::class);
        $this->useCMDBObject()->readAll($this->generateRandomID());
    }

    /**
     * @throws Exception on error
     * @group API-81
     */
    public function testUpsert() {
        $title = $this->generateRandomString();

        // Exists:
        $objectID = $this->useCMDBObject()->create(ObjectType::SERVER, $title);
        $result = $this->useCMDBObject()->upsert(ObjectType::SERVER, $title, ['purpose' => 'Private stuff']);

        $this->assertIsInt($result);
        $this->assertSame($objectID, $result);

        // Does not exist:
        $result = $this->useCMDBObject()->upsert(ObjectType::SERVER, $this->generateRandomString());

        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }

}
