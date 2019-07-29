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

use bheisig\idoitapi\tests\Constants\Category;
use \Exception;
use bheisig\idoitapi\CMDBCategory;

class CMDBCategoryTest extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testSaveNewEntryInSingleValueCategory() {
        $objectID = $this->createServer();

        $attributes = [
            'manufacturer' => $this->generateRandomString(),
            'title' => $this->generateRandomString()
        ];

        $entryID = $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__MODEL,
            $attributes
        );

        $this->assertIsInt($entryID);
        $this->isID($entryID);

        $entries = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__MODEL
        );

        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);

        $entry = $entries[0];

        // Check both dialog+ attributes:
        foreach ($attributes as $attribute => $value) {
            $this->assertArrayHasKey($attribute, $entry);
            $this->assertIsArray($entry[$attribute]);
            $this->assertArrayHasKey('title', $entry[$attribute]);
            $this->assertSame($value, $entry[$attribute]['title']);
        }
    }

    /**
     * @throws Exception on error
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

        $entryID = $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__IP,
            $attributes
        );

        $this->assertIsInt($entryID);
        $this->isID($entryID);

        $entries = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__IP
        );

        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);

        $entry = $entries[0];

        foreach ($attributes as $attribute => $value) {
            $this->assertArrayHasKey($attribute, $entry);
        }
    }

    /**
     * @throws Exception on error
     */
    public function testSaveExistingEntryInSingleValueCategory() {
        $objectID = $this->createServer();

        // Original entry:

        $attributes = [
            'manufacturer' => $this->generateRandomString(),
            'title' => $this->generateRandomString()
        ];

        $entryID = $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__MODEL,
            $attributes
        );

        $this->assertIsInt($entryID);
        $this->isID($entryID);

        $entries = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__MODEL
        );

        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);

        $entry = $entries[0];

        $this->assertArrayHasKey('id', $entry);
        $id = (int) $entry['id'];
        $this->assertSame($entryID, $id);

        foreach ($attributes as $attribute => $value) {
            $this->assertArrayHasKey($attribute, $entry);
            $this->assertIsArray($entry[$attribute]);
            $this->assertArrayHasKey('title', $entry[$attribute]);
            $this->assertSame($value, $entry[$attribute]['title']);
        }

        // Updated entry:

        $newAttributes = [
            'manufacturer' => $this->generateRandomString(),
            'title' => $this->generateRandomString()
        ];

        $newEntryID = $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__MODEL,
            $newAttributes
        );

        $this->assertIsInt($newEntryID);
        $this->isID($newEntryID);

        $entries = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__MODEL
        );

        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);

        $newEntry = $entries[0];

        $this->assertArrayHasKey('id', $newEntry);
        $id = (int) $newEntry['id'];
        $this->assertSame($newEntryID, $id);
        $this->assertSame($entryID, $newEntryID);

        foreach ($newAttributes as $attribute => $value) {
            $this->assertArrayHasKey($attribute, $newEntry);
            $this->assertIsArray($newEntry[$attribute]);
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
     * @throws Exception on error
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

        $entryID = $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__IP,
            $attributes
        );

        $this->assertIsInt($entryID);
        $this->isID($entryID);

        $entries = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__IP
        );

        $this->assertIsArray($entries);
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

        $newEntryID = $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__IP,
            $newAttributes,
            $entryID
        );

        $this->assertIsInt($newEntryID);
        $this->isID($newEntryID);

        $entries = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__IP
        );

        $this->assertIsArray($entries);
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
     * @throws Exception on error
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

        $firstEntryID = $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__IP,
            $firstAttributes
        );

        $this->assertIsInt($firstEntryID);
        $this->isID($firstEntryID);

        $entries = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__IP
        );

        $this->assertIsArray($entries);
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

        $secondEntryID = $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__IP,
            $secondAttributes
        );

        $this->assertIsInt($secondEntryID);
        $this->isID($secondEntryID);

        $entries = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__IP
        );

        $this->assertIsArray($entries);
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
     * @group API-79
     * @throws Exception on error
     */
    public function testSaveUnknownAttribute() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $result = $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__MODEL,
            [
                'unknown' => $this->generateRandomString()
            ]
        );

        $this->isID($result);
    }

    /**
     * @group API-78
     * @throws Exception on error
     */
    public function testSaveInvalidAttribute() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $result = $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__MODEL,
            [
                'serial' => [1, 2, 3]
            ]
        );

        $this->isID($result);
    }

    /**
     * @throws Exception on error
     */
    public function testCreate() {
        $objectID = $this->createServer();

        $entryID = $this->useCMDBCategory()->create(
            $objectID,
            Category::CATG__IP,
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
     * @throws Exception on error
     */
    public function testReadSingleValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);

        $result = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__MODEL
        );

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey(0, $result);
        $this->assertIsArray($result[0]);

        $this->assertArrayHasKey('id', $result[0]);
        $this->isIDAsString($result[0]['id']);
        $this->assertSame($entryID, (int) $result[0]['id']);

        $this->assertArrayHasKey('objID', $result[0]);
        $this->isIDAsString($result[0]['objID']);
        $this->assertSame($objectID, (int) $result[0]['objID']);
    }

    /**
     * @throws Exception on error
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

        $result = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__IP
        );

        $this->assertIsArray($result);
        $this->assertCount($numberOfEntries, $result);

        foreach ($result as $index => $entry) {
            $this->assertIsInt($index);
            $this->assertGreaterThanOrEqual(0, $index);

            $this->assertIsArray($entry);

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
     * @group API-99
     * @throws Exception on error
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

        $result = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__IP
        );

        $this->assertIsArray($result);
        $this->assertCount($numberOfEntries, $result);

        // Rank the first entry:
        $rankedEntryID = $entryIDs[0];
        $this->useCMDBCategory()->archive($objectID, Category::CATG__IP, $rankedEntryID);

        // Read it:
        $result = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__IP,
            3 // Archived
        );

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey(0, $result);
        $this->assertIsArray($result[0]);

        $this->assertArrayHasKey('id', $result[0]);
        $this->isIDAsString($result[0]['id']);
        $this->assertSame($rankedEntryID, (int) $result[0]['id']);

        $this->assertArrayHasKey('objID', $result[0]);
        $this->isIDAsString($result[0]['objID']);
        $this->assertSame($objectID, (int) $result[0]['objID']);

        // Check the other entries:
        $result = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__IP,
            2
        );

        $this->assertIsArray($result);
        // Only 2 left:
        $this->assertCount(2, $result);

        foreach ($result as $index => $entry) {
            $this->assertIsInt($index);
            $this->assertGreaterThanOrEqual(0, $index);

            $this->assertIsArray($entry);

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
     * @group API-99
     * @throws Exception on error
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

        $result = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__IP
        );

        $this->assertIsArray($result);
        $this->assertCount($numberOfEntries, $result);

        // Rank the first entry:
        $rankedEntryID = $entryIDs[0];
        $this->useCMDBCategory()->delete($objectID, Category::CATG__IP, $rankedEntryID);

        // Read it:
        $result = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__IP,
            4 // Deleted
        );

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey(0, $result);
        $this->assertIsArray($result[0]);

        $this->assertArrayHasKey('id', $result[0]);
        $this->isIDAsString($result[0]['id']);
        $this->assertSame($rankedEntryID, (int) $result[0]['id']);

        $this->assertArrayHasKey('objID', $result[0]);
        $this->isIDAsString($result[0]['objID']);
        $this->assertSame($objectID, (int) $result[0]['objID']);

        // Check the other entries:
        $result = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__IP,
            2
        );

        $this->assertIsArray($result);
        // Only 2 left:
        $this->assertCount(2, $result);

        foreach ($result as $index => $entry) {
            $this->assertIsInt($index);
            $this->assertGreaterThanOrEqual(0, $index);

            $this->assertIsArray($entry);

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
     * @group API-99
     * @throws Exception on error
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

        $result = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__IP
        );

        $this->assertIsArray($result);
        $this->assertCount($numberOfEntries, $result);

        // Archive the first one:
        $archivedEntryID = $entryIDs[0];
        $this->useCMDBCategory()->archive($objectID, Category::CATG__IP, $archivedEntryID);

        // Delete the second one:
        $deletedEntryID = $entryIDs[1];
        $this->useCMDBCategory()->delete($objectID, Category::CATG__IP, $deletedEntryID);

        // Read all of them:
        $result = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__IP,
            -1 // All
        );

        $this->assertIsArray($result);
        $this->assertCount($numberOfEntries, $result);

        foreach ($result as $index => $entry) {
            $this->assertIsInt($index);
            $this->assertGreaterThanOrEqual(0, $index);

            $this->assertIsArray($entry);

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
     * @throws Exception on error
     */
    public function testReadNormalEntryInMultiValueCategoryWithMixedStates() {
        // Create data:
        $objectID = $this->createServer();
        $this->isID($objectID);

        $numberOfEntries = 3;
        $entryIDs = [];

        for ($index = 0; $index < $numberOfEntries; $index++) {
            $entryIDs[] = $entryID = $this->addIPv4($objectID);
            $this->isID($entryID);
        }

        $this->useCMDBCategory()->archive($objectID, Category::CATG__IP, $entryIDs[1]);
        $this->useCMDBCategory()->delete($objectID, Category::CATG__IP, $entryIDs[2]);

        // Run tests:
        $result = $this->useCMDBCategory()->read($objectID, Category::CATG__IP, 2);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);

        foreach ($result as $index => $entry) {
            $this->assertIsInt($index);
            $this->assertGreaterThanOrEqual(0, $index);

            $this->assertIsArray($entry);

            $this->assertArrayHasKey('id', $entry);
            $this->isIDAsString($entry['id']);
            $this->assertSame($entryIDs[0], (int) $entry['id']);

            $this->assertArrayHasKey('objID', $entry);
            $this->isIDAsString($entry['objID']);
            $this->assertSame($objectID, (int) $entry['objID']);
        }
    }

    /**
     * @throws Exception on error
     */
    public function testReadArchivedEntryInMultiValueCategoryWithMixedStates() {
        // Create data:
        $objectID = $this->createServer();
        $this->isID($objectID);

        $numberOfEntries = 3;
        $entryIDs = [];

        for ($index = 0; $index < $numberOfEntries; $index++) {
            $entryIDs[] = $entryID = $this->addIPv4($objectID);
            $this->isID($entryID);
        }

        $this->useCMDBCategory()->archive($objectID, Category::CATG__IP, $entryIDs[1]);
        $this->useCMDBCategory()->delete($objectID, Category::CATG__IP, $entryIDs[2]);

        // Run tests:
        $result = $this->useCMDBCategory()->read($objectID, Category::CATG__IP, 3);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);

        foreach ($result as $index => $entry) {
            $this->assertIsInt($index);
            $this->assertGreaterThanOrEqual(0, $index);

            $this->assertIsArray($entry);

            $this->assertArrayHasKey('id', $entry);
            $this->isIDAsString($entry['id']);
            $this->assertSame($entryIDs[1], (int) $entry['id']);

            $this->assertArrayHasKey('objID', $entry);
            $this->isIDAsString($entry['objID']);
            $this->assertSame($objectID, (int) $entry['objID']);
        }
    }

    /**
     * @throws Exception on error
     */
    public function testReadDeletedEntryInMultiValueCategoryWithMixedStates() {
        // Create data:
        $objectID = $this->createServer();
        $this->isID($objectID);

        $numberOfEntries = 3;
        $entryIDs = [];

        for ($index = 0; $index < $numberOfEntries; $index++) {
            $entryIDs[] = $entryID = $this->addIPv4($objectID);
            $this->isID($entryID);
        }

        $this->useCMDBCategory()->archive($objectID, Category::CATG__IP, $entryIDs[1]);
        $this->useCMDBCategory()->delete($objectID, Category::CATG__IP, $entryIDs[2]);

        // Run tests:
        $result = $this->useCMDBCategory()->read($objectID, Category::CATG__IP, 4);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);

        foreach ($result as $index => $entry) {
            $this->assertIsInt($index);
            $this->assertGreaterThanOrEqual(0, $index);

            $this->assertIsArray($entry);

            $this->assertArrayHasKey('id', $entry);
            $this->isIDAsString($entry['id']);
            $this->assertSame($entryIDs[2], (int) $entry['id']);

            $this->assertArrayHasKey('objID', $entry);
            $this->isIDAsString($entry['objID']);
            $this->assertSame($objectID, (int) $entry['objID']);
        }
    }

    /**
     * @throws Exception on error
     */
    public function testReadNormalEntriesInMultiValueCategoryWithMixedStates() {
        // Create data:
        $objectID = $this->createServer();
        $this->isID($objectID);

        $numberOfEntries = 3;
        $entryIDs = [];

        for ($index = 0; $index < $numberOfEntries; $index++) {
            $entryIDs[] = $entryID = $this->addIPv4($objectID);
            $this->isID($entryID);
        }

        $this->useCMDBCategory()->archive($objectID, Category::CATG__IP, $entryIDs[1]);
        $this->useCMDBCategory()->delete($objectID, Category::CATG__IP, $entryIDs[2]);

        // Run tests:
        $result = $this->useCMDBCategory()->read($objectID, Category::CATG__IP);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);

        foreach ($result as $index => $entry) {
            $this->assertIsInt($index);
            $this->assertGreaterThanOrEqual(0, $index);

            $this->assertIsArray($entry);

            $this->assertArrayHasKey('id', $entry);
            $this->isIDAsString($entry['id']);
            $this->assertSame($entryIDs[0], (int) $entry['id']);

            $this->assertArrayHasKey('objID', $entry);
            $this->isIDAsString($entry['objID']);
            $this->assertSame($objectID, (int) $entry['objID']);
        }
    }

    /**
     * @throws Exception on error
     */
    public function testReadOneSingleValueCategoryByItsIdentifier() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);

        $result = $this->useCMDBCategory()->readOneByID(
            $objectID,
            Category::CATG__MODEL,
            $entryID
        );

        $this->assertIsArray($result);

        $this->assertArrayHasKey('id', $result);
        $this->isIDAsString($result['id']);
        $this->assertSame($entryID, (int) $result['id']);

        $this->assertArrayHasKey('objID', $result);
        $this->isIDAsString($result['objID']);
        $this->assertSame($objectID, (int) $result['objID']);
    }

    /**
     * @throws Exception on error
     */
    public function testReadOneEntryInMultiValueCategoryByItsIdentifier() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);

        $result = $this->useCMDBCategory()->readOneByID(
            $objectID,
            Category::CATG__IP,
            $entryID
        );

        $this->assertIsArray($result);

        $this->assertArrayHasKey('id', $result);
        $this->isIDAsString($result['id']);
        $this->assertSame($entryID, (int) $result['id']);

        $this->assertArrayHasKey('objID', $result);
        $this->isIDAsString($result['objID']);
        $this->assertSame($objectID, (int) $result['objID']);
    }

    /**
     * @throws Exception on error
     */
    public function testReadFirst() {
        $objectID = $this->createServer();
        $this->defineModel($objectID);

        // Test single-value category:
        $result = $this->useCMDBCategory()->readFirst(
            $objectID,
            Category::CATG__MODEL
        );

        $this->assertIsArray($result);
        $this->assertNotCount(0, $result);
        $this->assertArrayHasKey('id', $result);

        $this->addIPv4($objectID);

        // Test multi-value category:
        $result = $this->useCMDBCategory()->readFirst(
            $objectID,
            Category::CATG__IP
        );

        $this->assertIsArray($result);
        $this->assertNotCount(0, $result);
        $this->assertArrayHasKey('id', $result);

        // Test empty category (no entry for object):
        $result = $this->useCMDBCategory()->readFirst(
            $objectID,
            Category::CATG__ACCESS
        );

        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testUpdate() {
        $objectID = $this->createServer();

        // Test single-value category:
        $itself = $this->useCMDBCategory()->update(
            $objectID,
            Category::CATG__GLOBAL,
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
            $itself = $this->useCMDBCategory()->update($objectID, Category::CATG__IP, [
                'ipv4_address' => $this->generateIPv4Address()
            ], $entryIDs[$i]);

            $this->assertInstanceOf(CMDBCategory::class, $itself);
        }
    }

    /**
     * @throws Exception on error
     */
    public function testBatchCreate() {
        $objectID1 = $this->createServer();
        $objectID2 = $this->createServer();

        // Single-valued category:
        $result = $this->useCMDBCategory()->batchCreate(
            [$objectID1, $objectID2],
            Category::CATG__MODEL,
            [
                [
                    'manufacturer' => $this->generateRandomString(),
                    'title' => $this->generateRandomString(),
                    'serial' => $this->generateRandomString(),
                    'description' => $this->generateDescription()
                ]
            ]
        );

        $this->assertIsArray($result);
        $this->assertCount(2, $result);

        foreach ($result as $entryID) {
            $this->assertIsInt($entryID);
            $this->assertGreaterThan(0, $entryID);
        }

        // Multi-valued category:
        $result = $this->useCMDBCategory()->batchCreate(
            [$objectID1, $objectID2],
            Category::CATG__IP,
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

        $this->assertIsArray($result);
        $this->assertCount(4, $result);

        foreach ($result as $entryID) {
            $this->assertIsInt($entryID);
            $this->assertGreaterThan(0, $entryID);
        }
    }

    /**
     * @throws Exception on error
     */
    public function testBatchRead() {
        $objectID1 = $this->createServer();
        $objectID2 = $this->createServer();
        $this->addIPv4($objectID1);
        $this->addIPv4($objectID2);
        $this->defineModel($objectID1);
        $this->defineModel($objectID2);

        $batchResult = $this->useCMDBCategory()->batchRead(
            [$objectID1, $objectID2],
            [Category::CATG__IP, Category::CATG__MODEL]
        );

        $this->assertIsArray($batchResult);
        $this->assertCount(4, $batchResult);

        if (is_array($batchResult)) {
            foreach ($batchResult as $result) {
                $this->assertIsArray($result);
                $this->assertNotCount(0, $result);
            }
        }
    }

    /**
     * @throws Exception on error
     */
    public function testBatchUpdate() {
        $objectID1 = $this->createServer();
        $objectID2 = $this->createServer();

        $entryIDs = [];

        $entryIDs[] = $this->defineModel($objectID1);
        $entryIDs[] = $this->defineModel($objectID2);

        $itself = $this->useCMDBCategory()->batchUpdate(
            [$objectID1, $objectID2],
            Category::CATG__MODEL,
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
