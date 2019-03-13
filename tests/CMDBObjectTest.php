<?php

/**
 * Copyright (C) 2016-19 Benjamin Heisig
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
 * @copyright Copyright (C) 2016-19 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

declare(strict_types=1);

namespace bheisig\idoitapi\tests;

use bheisig\idoitapi\CMDBObject;
use bheisig\idoitapi\CMDBObjectTypes;
use bheisig\idoitapi\CMDBCategoryInfo;

/**
 * @coversDefaultClass \bheisig\idoitapi\CMDBObject
 */
class CMDBObjectTest extends BaseTest {

    /**
     * @throws \Exception on error
     * @group API-81
     */
    public function testCreate() {
        $objectID = $this->cmdbObject->create(
            'C__OBJTYPE__SERVER',
            $this->generateRandomString()
        );

        $this->assertIsInt($objectID);
        $this->isID($objectID);
    }

    /**
     * @throws \Exception on error
     */
    public function testCreateObjectForEveryType() {
        $objectTypeConstants = array_map(
            function($type) {
                return $type['const'];
            },
            (new CMDBObjectTypes($this->api))->read()
        );

        foreach ($objectTypeConstants as $objectTypeConstant) {
            $objectID = $this->cmdbObject->create($objectTypeConstant, $this->generateRandomString());
            $this->isID($objectID);
        }
    }

    /**
     * @throws \Exception on error
     * @group API-81
     */
    public function testCreateWithMoreAttributes() {
        $objectID = $this->cmdbObject->create(
            'C__OBJTYPE__SERVER',
            $this->generateRandomString(),
            [
                'category' => 'Test',
                'purpose' => 'for reasons',
                'cmdb_status' => 9,
                'description' => $this->generateDescription()
            ]
        );

        $this->assertIsInt($objectID);
        $this->isID($objectID);
    }

    /**
     * @group API-84
     * @throws \Exception on error
     */
    public function testCreateWithCategories() {
        $result = $this->cmdbObject->createWithCategories(
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

        $this->assertArrayHasKey('C__CATG__MODEL', $result['categories']);
        $this->assertIsArray($result['categories']['C__CATG__MODEL']);
        $this->assertCount(1, $result['categories']['C__CATG__MODEL']);
        $this->assertArrayHasKey(0, $result['categories']['C__CATG__MODEL']);
        $this->isID($result['categories']['C__CATG__MODEL'][0]);

        $this->assertArrayHasKey('C__CATG__IP', $result['categories']);
        $this->assertIsArray($result['categories']['C__CATG__IP']);
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
     * @group API-81
     */
    public function testRead() {
        $objectID = $this->createServer();

        $result = $this->cmdbObject->read($objectID);

        $this->assertIsArray($result);
        $this->isObject($result);
    }

    /**
     * Validate common information about an object
     *
     * Note: There are differences between an object read by cmdb.object.read and read by cmdb.objects.read :-(
     *
     * @param array $object Common information about an object
     */
    protected function isObject(array $object) {
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
     * @throws \Exception on error
     */
    public function testUpdate() {
        $objectID = $this->createServer();

        $this->assertInstanceOf(
            CMDBObject::class,
            $this->cmdbObject->update($objectID, ['title' => 'Anne Admin'])
        );
    }

    /**
     * @throws \Exception on error
     * @group API-81
     */
    public function testLoad() {
        $objectID = $this->createServer();

        $result = $this->cmdbObject->load($objectID);

        $this->assertIsArray($result);
        $this->assertNotCount(0, $result);
    }

    /**
     * @group new
     * @throws \Exception on error
     */
    public function testReadAll() {
        $objectIDs = array_map(
            function ($object) {
                return $object['id'];
            },
            $this->cmdbObjects->read()
        );

        $categoryInfo = new CMDBCategoryInfo($this->api);
        $blacklistedCategoryConstants = $categoryInfo->getVirtualCategoryConstants();

        foreach ($objectIDs as $objectID) {
            $result = $this->cmdbObject->readAll($objectID);

            $this->assertIsArray($result);
            $this->isObject($result[0]);
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

                    if ($categoryConstant === 'C__CATG__RELATION') {
                        continue;
                    }

                    $this->assertSame($objectID, (int) $entry['objID']);
                }
            }
        }
    }

    /**
     * @group new
     * @throws \Exception on error
     */
    public function testReadAllFromNonExistingObject() {
        $this->expectException(\RuntimeException::class);
        $this->cmdbObject->readAll($this->generateRandomID());
    }

    /**
     * @throws \Exception on error
     * @group API-81
     */
    public function testUpsert() {
        $title = $this->generateRandomString();

        // Exists:
        $objectID = $this->cmdbObject->create('C__OBJTYPE__SERVER', $title);
        $result = $this->cmdbObject->upsert('C__OBJTYPE__SERVER', $title, ['purpose' => 'Private stuff']);

        $this->assertIsInt($result);
        $this->assertSame($objectID, $result);

        // Does not exist:
        $result = $this->cmdbObject->upsert('C__OBJTYPE__SERVER', $this->generateRandomString());

        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }

}
