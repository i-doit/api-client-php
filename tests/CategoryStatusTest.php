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

class CategoryStatusTest extends BaseTest {

    /**
     * @group unreleased
     * @group API-94
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testArchiveSingleValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->archive($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchived($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group unreleased
     * @group API-94
     * @throws \Exception on error
     */
    public function testArchiveMultiValueCategoryEntry() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->archive($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchived($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group unreleased
     * @group API-94
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testArchiveAlreadyArchivedSingleValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->archive($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchived($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->archive($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchived($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group unreleased
     * @group API-94
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testArchiveAlreadyArchivedEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->archive($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchived($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->archive($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchived($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group unreleased
     * @group API-94
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testArchiveDeletedSingleValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->delete($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeleted($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->archive($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchived($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group unreleased
     * @group API-94
     * @throws \Exception on error
     */
    public function testArchiveDeletedEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->delete($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeleted($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->archive($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchived($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group unreleased
     * @group API-94
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testArchivePurgedSingleValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->purge($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->archive($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchived($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group unreleased
     * @group API-94
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testArchivePurgedEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->purge($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->archive($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchived($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group unreleased
     * @group API-94
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testArchiveNonExistingSingleValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->generateRandomID();
        $this->isID($entryID);

        $result = $this->cmdbCategory->archive($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchived($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group unreleased
     * @group API-94
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testArchiveNonExistingEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->generateRandomID();
        $this->isID($entryID);

        $result = $this->cmdbCategory->archive($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchived($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group unreleased
     * @group API-94
     * @throws \Exception on error
     * @expectedException \Exception
     */
//    public function testArchiveEmptySingleValueCategory() {
//        // @todo Not testable because we need an entry ID!
//    }

    /**
     * @group unreleased
     * @group API-94
     * @throws \Exception on error
     * @expectedException \Exception
     */
//    public function testArchiveUnknownEntryInMultiValueCategory() {
//        // @todo Not testable because we need an entry ID!
//    }

    /**
     * @group unreleased
     * @group API-94
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testArchiveEntryInUnknownCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->generateRandomID();
        $this->isID($entryID);

        $result = $this->cmdbCategory->archive($objectID, 'C__CATG__UNKNOWN', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchived($objectID, 'C__CATG__UNKNOWN', $entryID);
    }

    /**
     * @group unreleased
     * @group API-95
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testDeleteSingleValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->delete($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeleted($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group unreleased
     * @group API-95
     * @throws \Exception on error
     */
    public function testDeleteEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->delete($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeleted($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group unreleased
     * @group API-95
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testDeleteArchivedSingleValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->archive($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchived($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->delete($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeleted($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group unreleased
     * @group API-95
     * @throws \Exception on error
     */
    public function testDeleteArchivedEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->archive($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchived($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->delete($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeleted($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group unreleased
     * @group API-95
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testDeleteAlreadyDeletedSingleValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->delete($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeleted($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->delete($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeleted($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group unreleased
     * @group API-95
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testDeleteAlreadyDeletedEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->delete($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeleted($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->delete($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeleted($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group unreleased
     * @group API-95
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testDeletePurgedSingleValue() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->purge($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->delete($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeleted($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group unreleased
     * @group API-95
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testDeletePurgedEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->purge($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->delete($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeleted($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group unreleased
     * @group API-95
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testDeleteNonExistingSingleValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->generateRandomID();
        $this->isID($entryID);

        $result = $this->cmdbCategory->delete($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeleted($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group unreleased
     * @group API-95
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testDeleteNonExistingEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->generateRandomID();
        $this->isID($entryID);

        $result = $this->cmdbCategory->delete($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeleted($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group unreleased
     * @group API-95
     * @throws \Exception on error
     * @expectedException \Exception
     */
//    public function testDeleteEmptySingleValueCategory() {
//        // @todo Not testable because we need an entry ID!
//    }

    /**
     * @group unreleased
     * @group API-95
     * @throws \Exception on error
     * @expectedException \Exception
     */
//    public function testDeleteUnknownEntryInMultiValueCategory() {
//        // @todo Not testable because we need an entry ID!
//    }

    /**
     * @group unreleased
     * @group API-95
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testDeleteEntryInUnknownCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->generateRandomID();
        $this->isID($entryID);

        $result = $this->cmdbCategory->delete($objectID, 'C__CATG__UNKNOWN', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeleted($objectID, 'C__CATG__UNKNOWN', $entryID);
    }

    /**
     * @group unreleased
     * @group API-96
     * @throws \Exception on error
     */
    public function testPurgeSingleValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->purge($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group unreleased
     * @group API-96
     * @throws \Exception on error
     */
    public function testPurgeEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->purge($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group unreleased
     * @group API-96
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testPurgeArchivedSingleValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->archive($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchived($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->purge($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group unreleased
     * @group API-96
     * @throws \Exception on error
     */
    public function testPurgeArchivedEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->archive($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchived($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->purge($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group unreleased
     * @group API-96
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testPurgeDeletedSingleValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->delete($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeleted($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->purge($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group unreleased
     * @group API-96
     * @throws \Exception on error
     */
    public function testPurgeDeletedEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->delete($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeleted($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->purge($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group unreleased
     * @group API-96
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testPurgeAlreadyPurgedSingleValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->purge($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->purge($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group unreleased
     * @group API-96
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testPurgeAlreadyPurgedEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->purge($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->purge($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group unreleased
     * @group API-96
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testPurgeNonExistingSingleValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->generateRandomID();
        $this->isID($entryID);

        $result = $this->cmdbCategory->purge($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group unreleased
     * @group API-96
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testPurgeNonExistingEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->generateRandomID();
        $this->isID($entryID);

        $result = $this->cmdbCategory->purge($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group unreleased
     * @group API-96
     * @throws \Exception on error
     * @expectedException \Exception
     */
//    public function testPurgeEmptySingleValueCategory() {
//        // @todo Not testable because we need an entry ID!
//    }

    /**
     * @group unreleased
     * @group API-96
     * @throws \Exception on error
     * @expectedException \Exception
     */
//    public function testPurgeUnknownEntryInMultiValueCategory() {
//        // @todo Not testable because we need an entry ID!
//    }

    /**
     * @group unreleased
     * @group API-96
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testPurgeEntryInUnknownCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->generateRandomID();
        $this->isID($entryID);

        $result = $this->cmdbCategory->purge($objectID, 'C__CATG__UNKNOWN', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__UNKNOWN', $entryID);
    }

    /**
     * @group unreleased
     * @group API-97
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testRecycleSingleValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->recycle($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNormal($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group unreleased
     * @group API-97
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testRecycleEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->recycle($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group unreleased
     * @group API-97
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testRecycleArchivedSingleValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->archive($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchived($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->recycle($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNormal($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group unreleased
     * @group API-97
     * @throws \Exception on error
     */
    public function testRecycleArchivedEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->archive($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchived($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->recycle($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group unreleased
     * @group API-97
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testRecycleDeletedSingleValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->delete($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeleted($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->recycle($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNormal($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group unreleased
     * @group API-97
     * @throws \Exception on error
     */
    public function testRecycleDeletedEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->delete($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeleted($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->recycle($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group unreleased
     * @group API-97
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testRecyclePurgedSingleValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->purge($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->cmdbCategory->recycle($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNormal($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group unreleased
     * @group API-97
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testRecyclePurgedEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->purge($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->recycle($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group unreleased
     * @group API-97
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testRecycleNonExistingSingleValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->generateRandomID();
        $this->isID($entryID);

        $result = $this->cmdbCategory->recycle($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNormal($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group unreleased
     * @group API-97
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testRecycleNonExistingEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->generateRandomID();
        $this->isID($entryID);

        $result = $this->cmdbCategory->recycle($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group unreleased
     * @group API-97
     * @throws \Exception on error
     * @expectedException \Exception
     */
//    public function testRecycleEmptySingleValueCategory() {
//        // @todo Not testable because we need an entry ID!
//    }

    /**
     * @group unreleased
     * @group API-97
     * @throws \Exception on error
     * @expectedException \Exception
     */
//    public function testRecycleUnknownEntryInMultiValueCategory() {
//        // @todo Not testable because we need an entry ID!
//    }

    /**
     * @group unreleased
     * @group API-97
     * @throws \Exception on error
     * @expectedException \Exception
     */
    public function testRecycleEntryInUnknownCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->generateRandomID();
        $this->isID($entryID);

        $result = $this->cmdbCategory->recycle($objectID, 'C__CATG__UNKNOWN', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNormal($objectID, 'C__CATG__UNKNOWN', $entryID);
    }

    /**
     * @group unreleased
     * @throws \Exception on error
     */
    public function testClear() {
        $objectID = $this->createServer();
        $this->addIPv4($objectID);
        $this->addIPv4($objectID);
        $this->addContact($objectID, 9, 1);

        $result = $this->cmdbCategory->clear($objectID, [
            'C__CATG__IP',
            'C__CATG__CONTACT'
        ]);

        $this->assertInternalType('int', $result);
        $this->assertSame(3, $result);
    }

    /**
     * @group unreleased
     * @throws \Exception on error
     */
    public function testClearEmptyCategory() {
        $objectID = $this->createServer();

        $result = $this->cmdbCategory->clear($objectID, [
            'C__CATG__IP',
            'C__CATG__CONTACT'
        ]);

        $this->assertInternalType('int', $result);
        $this->assertSame(0, $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testQuickPurge() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);

        $result = $this->cmdbCategory->quickPurge($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @throws \Exception on error
     */
    public function testLegacyArchive() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);

        $roundsNeeded = 1;

        for ($round = 0; $round < $roundsNeeded; $round++) {
            $this->api->request(
                'cmdb.category.delete',
                [
                    'objID' => $objectID,
                    'category' => 'C__CATG__IP',
                    'cateID' => $entryID
                ]
            );
        }

        $this->isArchived($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @throws \Exception on error
     */
    public function testLegacyDelete() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);

        $roundsNeeded = 2;

        for ($round = 0; $round < $roundsNeeded; $round++) {
            $this->api->request(
                'cmdb.category.delete',
                [
                    'objID' => $objectID,
                    'category' => 'C__CATG__IP',
                    'cateID' => $entryID
                ]
            );
        }

        $this->isArchived($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @throws \Exception on error
     */
    public function testLegacyPurge() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormal($objectID, 'C__CATG__IP', $entryID);

        $roundsNeeded = 3;

        for ($round = 0; $round < $roundsNeeded; $round++) {
            $this->api->request(
                'cmdb.category.delete',
                [
                    'objID' => $objectID,
                    'category' => 'C__CATG__IP',
                    'cateID' => $entryID
                ]
            );
        }

        $this->isArchived($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * Has category entry status "normal" (2)?
     *
     * If entry has another status this method will throw an \Exception.
     *
     * @param int $objectID Object identifier
     * @param string $categoryConstant Category constant
     * @param int $entryID Entry identifier
     *
     * @throws \Exception on error
     */
    protected function isNormal(int $objectID, string $categoryConstant, int $entryID) {
        $result = $this->cmdbCategory->readOneByID($objectID, $categoryConstant, $entryID, 2);
        $this->assertInternalType('array', $result);

        $this->assertArrayHasKey('id', $result);
        $this->isIDAsString($result['id']);
        $this->assertSame($entryID, (int) $result['id']);

        $this->assertArrayHasKey('objID', $result);
        $this->isIDAsString($result['objID']);
        $this->assertSame($objectID, (int) $result['objID']);
    }

    /**
     * Has category entry status "archived" (3)?
     *
     * If entry has another status this method will throw an \Exception.
     *
     * @param int $objectID Object identifier
     * @param string $categoryConstant Category constant
     * @param int $entryID Entry identifier
     *
     * @throws \Exception on error
     */
    protected function isArchived(int $objectID, string $categoryConstant, int $entryID) {
        $result = $this->cmdbCategory->readOneByID($objectID, $categoryConstant, $entryID, 3);
        $this->assertInternalType('array', $result);

        $this->assertArrayHasKey('id', $result);
        $this->isIDAsString($result['id']);
        $this->assertSame($entryID, (int) $result['id']);

        $this->assertArrayHasKey('objID', $result);
        $this->isIDAsString($result['objID']);
        $this->assertSame($objectID, (int) $result['objID']);
    }

    /**
     * Has category entry status "deleted" (4)?
     *
     * If entry has another status this method will throw an \Exception.
     *
     * @param int $objectID Object identifier
     * @param string $categoryConstant Category constant
     * @param int $entryID Entry identifier
     *
     * @throws \Exception on error
     */
    protected function isDeleted(int $objectID, string $categoryConstant, int $entryID) {
        $result = $this->cmdbCategory->readOneByID($objectID, $categoryConstant, $entryID, 4);
        $this->assertInternalType('array', $result);

        $this->assertArrayHasKey('id', $result);
        $this->isIDAsString($result['id']);
        $this->assertSame($entryID, (int) $result['id']);

        $this->assertArrayHasKey('objID', $result);
        $this->isIDAsString($result['objID']);
        $this->assertSame($objectID, (int) $result['objID']);
    }

    /**
     * Is category entry not available?
     *
     * @param int $objectID Object identifier
     * @param string $categoryConstant Category constant
     * @param int $entryID Entry identifier
     *
     * @throws \Exception on error
     */
    protected function isNotAvailable(int $objectID, string $categoryConstant, int $entryID) {
        $this->expectException(\Exception::class);
        $result = $this->cmdbCategory->readOneByID($objectID, $categoryConstant, $entryID);
        $this->assertInternalType('array', $result);
    }

}
