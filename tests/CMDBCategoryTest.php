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

use bheisig\idoitapi\CMDBCategory;

class CMDBCategoryTest extends BaseTest {

    /**
     * @group unreleased
     * @throws \Exception on error
     */
    public function testSaveNewEntryInSingleValueCategory() {
        $objectID = $this->createServer();

        $attributes = [
            'manufacturer' => $this->generateRandomString(),
            'title' => $this->generateRandomString()
        ];

        $entryID = $this->cmdbCategory->save(
            $objectID,
            'C__CATG__MODEL',
            $attributes
        );

        $this->assertInternalType('int', $entryID);
        $this->isID($entryID);

        $entries = $this->cmdbCategory->read(
            $objectID,
            'C__CATG__MODEL'
        );

        $this->assertInternalType('array', $entries);
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);

        $entry = $entries[0];

        // Check both dialog+ attributes:
        foreach ($attributes as $attribute => $value) {
            $this->assertArrayHasKey($attribute, $entry);
            $this->assertInternalType('array', $entry[$attribute]);
            $this->assertArrayHasKey('title', $entry[$attribute]);
            $this->assertSame($value, $entry[$attribute]['title']);
        }
    }

    /**
     * @group unreleased
     * @throws \Exception on error
     */
    public function testSaveNewEntryInMultiValueCategory() {
        $objectID = $this->createServer();

        $attributes = [
            'net' => $this->getIPv4Net(),
            'active' => 0,
            'primary' => 0,
            'net_type' => 1,
            'ipv4_assignment' => 2,
            'ipv4_address' => $this->generateIPv4Address(),
            'description' => $this->generateDescription()
        ];

        $entryID = $this->cmdbCategory->save(
            $objectID,
            'C__CATG__IP',
            $attributes
        );

        $this->assertInternalType('int', $entryID);
        $this->isID($entryID);

        $entries = $this->cmdbCategory->read(
            $objectID,
            'C__CATG__IP'
        );

        $this->assertInternalType('array', $entries);
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);

        $entry = $entries[0];

        foreach ($attributes as $attribute => $value) {
            $this->assertArrayHasKey($attribute, $entry);
        }
    }

    /**
     * @group unreleased
     * @throws \Exception on error
     */
    public function testSaveExistingEntryInSingleValueCategory() {
        $objectID = $this->createServer();

        // Original entry:

        $attributes = [
            'manufacturer' => $this->generateRandomString(),
            'title' => $this->generateRandomString()
        ];

        $entryID = $this->cmdbCategory->save(
            $objectID,
            'C__CATG__MODEL',
            $attributes
        );

        $this->assertInternalType('int', $entryID);
        $this->isID($entryID);

        $entries = $this->cmdbCategory->read(
            $objectID,
            'C__CATG__MODEL'
        );

        $this->assertInternalType('array', $entries);
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);

        $entry = $entries[0];

        $this->assertArrayHasKey('id', $entry);
        $id = (int) $entry['id'];
        $this->assertSame($entryID, $id);

        foreach ($attributes as $attribute => $value) {
            $this->assertArrayHasKey($attribute, $entry);
            $this->assertInternalType('array', $entry[$attribute]);
            $this->assertArrayHasKey('title', $entry[$attribute]);
            $this->assertSame($value, $entry[$attribute]['title']);
        }

        // Updated entry:

        $newAttributes = [
            'manufacturer' => $this->generateRandomString(),
            'title' => $this->generateRandomString()
        ];

        $newEntryID = $this->cmdbCategory->save(
            $objectID,
            'C__CATG__MODEL',
            $newAttributes
        );

        $this->assertInternalType('int', $newEntryID);
        $this->isID($newEntryID);

        $entries = $this->cmdbCategory->read(
            $objectID,
            'C__CATG__MODEL'
        );

        $this->assertInternalType('array', $entries);
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);

        $newEntry = $entries[0];

        $this->assertArrayHasKey('id', $newEntry);
        $id = (int) $newEntry['id'];
        $this->assertSame($newEntryID, $id);
        $this->assertSame($entryID, $newEntryID);

        foreach ($newAttributes as $attribute => $value) {
            $this->assertArrayHasKey($attribute, $newEntry);
            $this->assertInternalType('array', $newEntry[$attribute]);
            $this->assertArrayHasKey('title', $newEntry[$attribute]);
            $this->assertSame($value, $newEntry[$attribute]['title']);
        }

        // Verify that further tests really pass:
        foreach (array_keys($attributes) as $attribute) {
            $this->assertNotSame($entry[$attribute], $newEntry[$attribute]);
            $this->assertNotSame($entry[$attribute], $newEntry[$attribute]);
        }
    }

    /**
     * @group unreleased
     * @throws \Exception on error
     */
    public function testSaveExistingEntryInMultiValueCategory() {
        $objectID = $this->createServer();

        // Original entry:

        $attributes = [
            'net' => $this->getIPv4Net(),
            'active' => 0,
            'primary' => 0,
            'net_type' => 1,
            'ipv4_assignment' => 2,
            'ipv4_address' => $this->generateIPv4Address(),
            'description' => $this->generateDescription()
        ];

        $entryID = $this->cmdbCategory->save(
            $objectID,
            'C__CATG__IP',
            $attributes
        );

        $this->assertInternalType('int', $entryID);
        $this->isID($entryID);

        $entries = $this->cmdbCategory->read(
            $objectID,
            'C__CATG__IP'
        );

        $this->assertInternalType('array', $entries);
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);

        $entry = $entries[0];

        foreach ($attributes as $attribute => $value) {
            $this->assertArrayHasKey($attribute, $entry);
        }

        // Updated entry:

        $newAttributes = [
            'net' => $this->getIPv4Net(),
            'active' => 1,
            'primary' => 1,
            'net_type' => 1,
            'ipv4_assignment' => 2,
            'ipv4_address' => $this->generateIPv4Address(),
            'description' => $this->generateDescription()
        ];

        $newEntryID = $this->cmdbCategory->save(
            $objectID,
            'C__CATG__IP',
            $newAttributes,
            $entryID
        );

        $this->assertInternalType('int', $newEntryID);
        $this->isID($newEntryID);

        $entries = $this->cmdbCategory->read(
            $objectID,
            'C__CATG__IP'
        );

        $this->assertInternalType('array', $entries);
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);

        $newEntry = $entries[0];

        $this->assertArrayHasKey('id', $newEntry);
        $id = (int) $newEntry['id'];
        $this->assertSame($newEntryID, $id);
        $this->assertSame($entryID, $newEntryID);

        foreach ($newAttributes as $attribute => $value) {
            $this->assertArrayHasKey($attribute, $newEntry);
        }
    }

    /**
     * @group unreleased
     * @throws \Exception on error
     */
    public function testSaveAdditionalEntryInMultiValueCategory() {
        $objectID = $this->createServer();

        // First entry:

        $firstAttributes = [
            'net' => $this->getIPv4Net(),
            'active' => 0,
            'primary' => 0,
            'net_type' => 1,
            'ipv4_assignment' => 2,
            'ipv4_address' => $this->generateIPv4Address(),
            'description' => $this->generateDescription()
        ];

        $firstEntryID = $this->cmdbCategory->save(
            $objectID,
            'C__CATG__IP',
            $firstAttributes
        );

        $this->assertInternalType('int', $firstEntryID);
        $this->isID($firstEntryID);

        $entries = $this->cmdbCategory->read(
            $objectID,
            'C__CATG__IP'
        );

        $this->assertInternalType('array', $entries);
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);

        $entry = $entries[0];

        foreach ($firstAttributes as $attribute => $value) {
            $this->assertArrayHasKey($attribute, $entry);
        }

        // Additional entry:

        $secondAttributes = [
            'net' => $this->getIPv4Net(),
            'active' => 1,
            'primary' => 1,
            'net_type' => 1,
            'ipv4_assignment' => 2,
            'ipv4_address' => $this->generateIPv4Address(),
            'description' => $this->generateDescription()
        ];

        $secondEntryID = $this->cmdbCategory->save(
            $objectID,
            'C__CATG__IP',
            $secondAttributes
        );

        $this->assertInternalType('int', $secondEntryID);
        $this->isID($secondEntryID);

        $entries = $this->cmdbCategory->read(
            $objectID,
            'C__CATG__IP'
        );

        $this->assertInternalType('array', $entries);
        $this->assertCount(2, $entries);
        $this->assertArrayHasKey(1, $entries);

        $secondEntry = $entries[1];

        $this->assertArrayHasKey('id', $secondEntry);
        $id = (int) $secondEntry['id'];
        $this->assertSame($secondEntryID, $id);
        $this->assertNotSame($firstEntryID, $secondEntryID);

        foreach ($secondAttributes as $attribute => $value) {
            $this->assertArrayHasKey($attribute, $secondAttributes);
        }
    }

    /**
     * @group unreleased
     * @group API-79
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testSaveUnknownAttribute() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $result = $this->cmdbCategory->save(
            $objectID,
            'C__CATG__MODEL',
            [
                'unknown' => $this->generateRandomString()
            ]
        );

        $this->isID($result);
    }

    /**
     * @group unreleased
     * @group API-78
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testSaveInvalidAttribute() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $result = $this->cmdbCategory->save(
            $objectID,
            'C__CATG__MODEL',
            [
                'serial' => [1, 2, 3]
            ]
        );

        $this->isID($result);
    }

    /**
     * @throws \Exception on error
     */
    public function testCreate() {
        $objectID = $this->createServer();

        $entryID = $this->cmdbCategory->create(
            $objectID,
            'C__CATG__IP',
            [
                'net' => $this->getIPv4Net(),
                'active' => 0,
                'primary' => 0,
                'net_type' => 1,
                'ipv4_assignment' => 2,
                'ipv4_address' => $this->generateIPv4Address(),
                'description' => $this->generateDescription()
            ]
        );

        $this->assertGreaterThanOrEqual(1, $entryID);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadSingleValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);

        $result = $this->cmdbCategory->read(
            $objectID,
            'C__CATG__MODEL'
        );

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey(0, $result);
        $this->assertInternalType('array', $result[0]);

        $this->assertArrayHasKey('id', $result[0]);
        $this->isIDAsString($result[0]['id']);
        $this->assertSame($entryID, (int) $result[0]['id']);

        $this->assertArrayHasKey('objID', $result[0]);
        $this->isIDAsString($result[0]['objID']);
        $this->assertSame($objectID, (int) $result[0]['objID']);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadEntriesInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $numberOfEntries = 3;
        $entryIDs = [];

        for ($index = 0; $index < $numberOfEntries; $index++) {
            $entryIDs[] = $entryID = $this->addIPv4($objectID);
            $this->isID($entryID);
        }

        $result = $this->cmdbCategory->read(
            $objectID,
            'C__CATG__IP'
        );

        $this->assertInternalType('array', $result);
        $this->assertCount($numberOfEntries, $result);

        foreach ($result as $index => $entry) {
            $this->assertInternalType('int', $index);
            $this->assertGreaterThanOrEqual(0, $index);

            $this->assertInternalType('array', $entry);

            $this->assertArrayHasKey('id', $entry);
            $this->isIDAsString($entry['id']);
            $this->assertArrayHasKey($index, $entryIDs);
            $this->assertSame($entryIDs[$index], (int) $entry['id']);

            $this->assertArrayHasKey('objID', $entry);
            $this->isIDAsString($entry['objID']);
            $this->assertSame($objectID, (int) $entry['objID']);
        }
    }

    /**
     * @group unreleased
     * @group API-99
     * @throws \Exception on error
     */
    public function testReadArchivedEntriesInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $numberOfEntries = 3;
        $entryIDs = [];

        for ($index = 0; $index < $numberOfEntries; $index++) {
            $entryIDs[] = $entryID = $this->addIPv4($objectID);
            $this->isID($entryID);
        }

        $result = $this->cmdbCategory->read(
            $objectID,
            'C__CATG__IP'
        );

        $this->assertInternalType('array', $result);
        $this->assertCount($numberOfEntries, $result);

        // Rank the first entry:
        $rankedEntryID = $entryIDs[0];
        $this->cmdbCategory->archive($objectID, 'C__CATG__IP', $rankedEntryID);

        // Read it:
        $result = $this->cmdbCategory->read(
            $objectID,
            'C__CATG__IP',
            3 // Archived
        );

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey(0, $result);
        $this->assertInternalType('array', $result[0]);

        $this->assertArrayHasKey('id', $result[0]);
        $this->isIDAsString($result[0]['id']);
        $this->assertSame($rankedEntryID, (int) $result[0]['id']);

        $this->assertArrayHasKey('objID', $result[0]);
        $this->isIDAsString($result[0]['objID']);
        $this->assertSame($objectID, (int) $result[0]['objID']);

        // Check the other entries:
        $result = $this->cmdbCategory->read(
            $objectID,
            'C__CATG__IP',
            2
        );

        $this->assertInternalType('array', $result);
        // Only 2 left:
        $this->assertCount(2, $result);

        foreach ($result as $index => $entry) {
            $this->assertInternalType('int', $index);
            $this->assertGreaterThanOrEqual(0, $index);

            $this->assertInternalType('array', $entry);

            $this->assertArrayHasKey('id', $entry);
            $this->isIDAsString($entry['id']);
            $this->assertContains((int) $entry['id'], $entryIDs);
            $this->assertNotSame($rankedEntryID, (int) $entry['id']);

            $this->assertArrayHasKey('objID', $entry);
            $this->isIDAsString($entry['objID']);
            $this->assertSame($objectID, (int) $entry['objID']);
        }
    }

    /**
     * @group unreleased
     * @group API-99
     * @throws \Exception on error
     */
    public function testReadDeletedEntriesInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $numberOfEntries = 3;
        $entryIDs = [];

        for ($index = 0; $index < $numberOfEntries; $index++) {
            $entryIDs[] = $entryID = $this->addIPv4($objectID);
            $this->isID($entryID);
        }

        $result = $this->cmdbCategory->read(
            $objectID,
            'C__CATG__IP'
        );

        $this->assertInternalType('array', $result);
        $this->assertCount($numberOfEntries, $result);

        // Rank the first entry:
        $rankedEntryID = $entryIDs[0];
        $this->cmdbCategory->delete($objectID, 'C__CATG__IP', $rankedEntryID);

        // Read it:
        $result = $this->cmdbCategory->read(
            $objectID,
            'C__CATG__IP',
            4 // Deleted
        );

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey(0, $result);
        $this->assertInternalType('array', $result[0]);

        $this->assertArrayHasKey('id', $result[0]);
        $this->isIDAsString($result[0]['id']);
        $this->assertSame($rankedEntryID, (int) $result[0]['id']);

        $this->assertArrayHasKey('objID', $result[0]);
        $this->isIDAsString($result[0]['objID']);
        $this->assertSame($objectID, (int) $result[0]['objID']);

        // Check the other entries:
        $result = $this->cmdbCategory->read(
            $objectID,
            'C__CATG__IP',
            2
        );

        $this->assertInternalType('array', $result);
        // Only 2 left:
        $this->assertCount(2, $result);

        foreach ($result as $index => $entry) {
            $this->assertInternalType('int', $index);
            $this->assertGreaterThanOrEqual(0, $index);

            $this->assertInternalType('array', $entry);

            $this->assertArrayHasKey('id', $entry);
            $this->isIDAsString($entry['id']);
            $this->assertContains((int) $entry['id'], $entryIDs);
            $this->assertNotSame($rankedEntryID, (int) $entry['id']);

            $this->assertArrayHasKey('objID', $entry);
            $this->isIDAsString($entry['objID']);
            $this->assertSame($objectID, (int) $entry['objID']);
        }
    }

    /**
     * @group unreleased
     * @group API-99
     * @throws \Exception on error
     */
    public function testReadEntriesInMultiValueCategoryByAllStates() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $numberOfEntries = 3;
        $entryIDs = [];

        for ($index = 0; $index < $numberOfEntries; $index++) {
            $entryIDs[] = $entryID = $this->addIPv4($objectID);
            $this->isID($entryID);
        }

        $result = $this->cmdbCategory->read(
            $objectID,
            'C__CATG__IP'
        );

        $this->assertInternalType('array', $result);
        $this->assertCount($numberOfEntries, $result);

        // Archive the first one:
        $archivedEntryID = $entryIDs[0];
        $this->cmdbCategory->archive($objectID, 'C__CATG__IP', $archivedEntryID);

        // Delete the second one:
        $deletedEntryID = $entryIDs[1];
        $this->cmdbCategory->delete($objectID, 'C__CATG__IP', $deletedEntryID);

        // Read all of them:
        $result = $this->cmdbCategory->read(
            $objectID,
            'C__CATG__IP',
            -1 // All
        );

        $this->assertInternalType('array', $result);
        $this->assertCount($numberOfEntries, $result);

        foreach ($result as $index => $entry) {
            $this->assertInternalType('int', $index);
            $this->assertGreaterThanOrEqual(0, $index);

            $this->assertInternalType('array', $entry);

            $this->assertArrayHasKey('id', $entry);
            $this->isIDAsString($entry['id']);
            $this->assertArrayHasKey($index, $entryIDs);
            $this->assertSame($entryIDs[$index], (int) $entry['id']);

            $this->assertArrayHasKey('objID', $entry);
            $this->isIDAsString($entry['objID']);
            $this->assertSame($objectID, (int) $entry['objID']);
        }
    }

    /**
     * @throws \Exception on error
     */
    public function testReadOneSingleValueCategoryByItsIdentifier() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);

        $result = $this->cmdbCategory->readOneByID(
            $objectID,
            'C__CATG__MODEL',
            $entryID
        );

        $this->assertInternalType('array', $result);

        $this->assertArrayHasKey('id', $result);
        $this->isIDAsString($result['id']);
        $this->assertSame($entryID, (int) $result['id']);

        $this->assertArrayHasKey('objID', $result);
        $this->isIDAsString($result['objID']);
        $this->assertSame($objectID, (int) $result['objID']);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadOneEntryInMultiValueCategoryByItsIdentifier() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);

        $result = $this->cmdbCategory->readOneByID(
            $objectID,
            'C__CATG__IP',
            $entryID
        );

        $this->assertInternalType('array', $result);

        $this->assertArrayHasKey('id', $result);
        $this->isIDAsString($result['id']);
        $this->assertSame($entryID, (int) $result['id']);

        $this->assertArrayHasKey('objID', $result);
        $this->isIDAsString($result['objID']);
        $this->assertSame($objectID, (int) $result['objID']);
    }

    /**
     * @throws \Exception on error
     */
    public function testReadFirst() {
        $objectID = $this->createServer();
        $this->defineModel($objectID);

        // Test single-value category:
        $result = $this->cmdbCategory->readFirst(
            $objectID,
            'C__CATG__MODEL'
        );

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
        $this->assertArrayHasKey('id', $result);

        $this->addIPv4($objectID);

        // Test multi-value category:
        $result = $this->cmdbCategory->readFirst(
            $objectID,
            'C__CATG__IP'
        );

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
        $this->assertArrayHasKey('id', $result);

        // Test empty category (no entry for object):
        $result = $this->cmdbCategory->readFirst(
            $objectID,
            'C__CATG__ACCESS'
        );

        $this->assertInternalType('array', $result);
        $this->assertCount(0, $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testUpdate() {
        $objectID = $this->createServer();

        // Test single-value category:
        $itself = $this->cmdbCategory->update(
            $objectID,
            'C__CATG__GLOBAL',
            [
                'cmdb_status' => 10
            ]
        );

        $this->assertInstanceOf(CMDBCategory::class, $itself);

        // Test multi-valie category:
        $amount = 3;
        $entryIDs = [];

        for ($i = 0; $i < $amount; $i++) {
            $entryIDs[] = $this->addIPv4($objectID);
        }

        for ($i = 0; $i < $amount; $i++) {
            $itself = $this->cmdbCategory->update($objectID, 'C__CATG__IP', [
                'ipv4_address' => $this->generateIPv4Address()
            ], $entryIDs[$i]);

            $this->assertInstanceOf(CMDBCategory::class, $itself);
        }
    }

    /**
     * @throws \Exception on error
     */
    public function testBatchCreate() {
        $objectID1 = $this->createServer();
        $objectID2 = $this->createServer();

        // Single-valued category:
        $result = $this->cmdbCategory->batchCreate(
            [$objectID1, $objectID2],
            'C__CATG__MODEL',
            [
                [
                    'manufacturer' => $this->generateRandomString(),
                    'title' => $this->generateRandomString(),
                    'serial' => $this->generateRandomString(),
                    'description' => $this->generateDescription()
                ]
            ]
        );

        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);

        foreach ($result as $entryID) {
            $this->assertInternalType('int', $entryID);
            $this->assertGreaterThan(0, $entryID);
        }

        // Multi-valued category:
        $result = $this->cmdbCategory->batchCreate(
            [$objectID1, $objectID2],
            'C__CATG__IP',
            [
                [
                    'net' => $this->getIPv4Net(),
                    'active' => 1,
                    'primary' => 1,
                    'net_type' => 1,
                    'ipv4_assignment' => 2,
                    "ipv4_address" =>  $this->generateIPv4Address(),
                    'description' => $this->generateDescription()
                ],
                [
                    'net' => $this->getIPv4Net(),
                    'active' => 1,
                    'primary' => 0,
                    'net_type' => 1,
                    'ipv4_assignment' => 2,
                    "ipv4_address" =>  $this->generateIPv4Address(),
                    'description' => $this->generateDescription()
                ]
            ]
        );

        $this->assertInternalType('array', $result);
        $this->assertCount(4, $result);

        foreach ($result as $entryID) {
            $this->assertInternalType('int', $entryID);
            $this->assertGreaterThan(0, $entryID);
        }
    }

    /**
     * @throws \Exception on error
     */
    public function testBatchRead() {
        $objectID1 = $this->createServer();
        $objectID2 = $this->createServer();
        $this->addIPv4($objectID1);
        $this->addIPv4($objectID2);
        $this->defineModel($objectID1);
        $this->defineModel($objectID2);

        $batchResult = $this->cmdbCategory->batchRead(
            [$objectID1, $objectID2],
            ['C__CATG__IP', 'C__CATG__MODEL']
        );

        $this->assertInternalType('array', $batchResult);
        $this->assertCount(4, $batchResult);

        if (is_array($batchResult)) {
            foreach ($batchResult as $result) {
                $this->assertInternalType('array', $result);
                $this->assertNotCount(0, $result);
            }
        }
    }

    /**
     * @throws \Exception on error
     */
    public function testBatchUpdate() {
        $objectID1 = $this->createServer();
        $objectID2 = $this->createServer();

        $entryIDs = [];

        $entryIDs[] = $this->defineModel($objectID1);
        $entryIDs[] = $this->defineModel($objectID2);

        $itself = $this->cmdbCategory->batchUpdate(
            [$objectID1, $objectID2],
            'C__CATG__MODEL',
            [
                'manufacturer' => $this->generateRandomString(),
                'title' => $this->generateRandomString(),
                'serial' => $this->generateRandomString(),
                'description' => $this->generateDescription()
            ]
        );

        $this->assertInstanceOf(CMDBCategory::class, $itself);
    }

}
