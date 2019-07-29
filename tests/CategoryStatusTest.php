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

use \Exception;
use bheisig\idoitapi\CMDBCategory;

class CategoryStatusTest extends BaseTest {

    /**
     * @group API-94
     * @throws Exception on error
     */
    public function testArchiveSingleValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->archive($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchivedEntry($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group API-94
     * @throws Exception on error
     */
    public function testArchiveMultiValueCategoryEntry() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->archive($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchivedEntry($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group API-94
     * @throws Exception on error
     */
    public function testArchiveAlreadyArchivedSingleValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->archive($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchivedEntry($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->archive($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchivedEntry($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group API-94
     * @throws Exception on error
     */
    public function testArchiveAlreadyArchivedEntryInMultiValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->archive($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchivedEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->archive($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchivedEntry($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group API-94
     * @throws Exception on error
     */
    public function testArchiveDeletedSingleValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->delete($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeletedEntry($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->archive($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchivedEntry($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group API-94
     * @throws Exception on error
     */
    public function testArchiveDeletedEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->delete($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeletedEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->archive($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchivedEntry($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group API-94
     * @throws Exception on error
     */
    public function testArchivePurgedSingleValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->purge($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->archive($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchivedEntry($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group API-94
     * @throws Exception on error
     */
    public function testArchivePurgedEntryInMultiValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->purge($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->archive($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchivedEntry($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group API-94
     * @throws Exception on error
     */
    public function testArchiveNonExistingSingleValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->generateRandomID();
        $this->isID($entryID);

        $result = $this->useCMDBCategory()->archive($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchivedEntry($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group API-94
     * @throws Exception on error
     */
    public function testArchiveNonExistingEntryInMultiValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->generateRandomID();
        $this->isID($entryID);

        $result = $this->useCMDBCategory()->archive($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchivedEntry($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group API-94
     * @throws Exception on error
     */
//    public function testArchiveEmptySingleValueCategory() {
//        // @todo Not testable because we need an entry ID!
//        $this->expectException(Exception::class);
//    }

    /**
     * @group API-94
     * @throws Exception on error
     */
//    public function testArchiveUnknownEntryInMultiValueCategory() {
//        // @todo Not testable because we need an entry ID!
//        $this->expectException(Exception::class);
//    }

    /**
     * @group API-94
     * @throws Exception on error
     */
    public function testArchiveEntryInUnknownCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->generateRandomID();
        $this->isID($entryID);

        $result = $this->useCMDBCategory()->archive($objectID, 'C__CATG__UNKNOWN', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchivedEntry($objectID, 'C__CATG__UNKNOWN', $entryID);
    }

    /**
     * @group API-95
     * @throws Exception on error
     */
    public function testDeleteSingleValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->delete($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeletedEntry($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group API-95
     * @throws Exception on error
     */
    public function testDeleteEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->delete($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeletedEntry($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group API-95
     * @throws Exception on error
     */
    public function testDeleteArchivedSingleValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->archive($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchivedEntry($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->delete($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeletedEntry($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group API-95
     * @throws Exception on error
     */
    public function testDeleteArchivedEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->archive($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchivedEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->delete($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeletedEntry($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group API-95
     * @throws Exception on error
     */
    public function testDeleteAlreadyDeletedSingleValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->delete($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeletedEntry($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->delete($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeletedEntry($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group API-95
     * @throws Exception on error
     */
    public function testDeleteAlreadyDeletedEntryInMultiValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->delete($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeletedEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->delete($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeletedEntry($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group API-95
     * @throws Exception on error
     */
    public function testDeletePurgedSingleValue() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->purge($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->delete($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeletedEntry($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group API-95
     * @throws Exception on error
     */
    public function testDeletePurgedEntryInMultiValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->purge($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->delete($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeletedEntry($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group API-95
     * @throws Exception on error
     */
    public function testDeleteNonExistingSingleValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->generateRandomID();
        $this->isID($entryID);

        $result = $this->useCMDBCategory()->delete($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeletedEntry($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group API-95
     * @throws Exception on error
     */
    public function testDeleteNonExistingEntryInMultiValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->generateRandomID();
        $this->isID($entryID);

        $result = $this->useCMDBCategory()->delete($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeletedEntry($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group API-95
     * @throws Exception on error
     */
//    public function testDeleteEmptySingleValueCategory() {
//        // @todo Not testable because we need an entry ID!
//        $this->expectException(Exception::class);
//    }

    /**
     * @group API-95
     * @throws Exception on error
     */
//    public function testDeleteUnknownEntryInMultiValueCategory() {
//        // @todo Not testable because we need an entry ID!
//        $this->expectException(Exception::class);
//    }

    /**
     * @group API-95
     * @throws Exception on error
     */
    public function testDeleteEntryInUnknownCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->generateRandomID();
        $this->isID($entryID);

        $result = $this->useCMDBCategory()->delete($objectID, 'C__CATG__UNKNOWN', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeletedEntry($objectID, 'C__CATG__UNKNOWN', $entryID);
    }

    /**
     * @group API-96
     * @throws Exception on error
     */
    public function testPurgeSingleValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->purge($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group API-96
     * @throws Exception on error
     */
    public function testPurgeEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->purge($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group API-96
     * @throws Exception on error
     */
    public function testPurgeArchivedSingleValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->archive($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchivedEntry($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->purge($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group API-96
     * @throws Exception on error
     */
    public function testPurgeArchivedEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->archive($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchivedEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->purge($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group API-96
     * @throws Exception on error
     */
    public function testPurgeDeletedSingleValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->delete($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeletedEntry($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->purge($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group API-96
     * @throws Exception on error
     */
    public function testPurgeDeletedEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->delete($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeletedEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->purge($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group API-96
     * @throws Exception on error
     */
    public function testPurgeAlreadyPurgedSingleValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->purge($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->purge($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group API-96
     * @throws Exception on error
     */
    public function testPurgeAlreadyPurgedEntryInMultiValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->purge($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->purge($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group API-96
     * @throws Exception on error
     */
    public function testPurgeNonExistingSingleValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->generateRandomID();
        $this->isID($entryID);

        $result = $this->useCMDBCategory()->purge($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group API-96
     * @throws Exception on error
     */
    public function testPurgeNonExistingEntryInMultiValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->generateRandomID();
        $this->isID($entryID);

        $result = $this->useCMDBCategory()->purge($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group API-96
     * @throws Exception on error
     */
//    public function testPurgeEmptySingleValueCategory() {
//        // @todo Not testable because we need an entry ID!
//        $this->expectException(Exception::class);
//    }

    /**
     * @group API-96
     * @throws Exception on error
     */
//    public function testPurgeUnknownEntryInMultiValueCategory() {
//        // @todo Not testable because we need an entry ID!
//        $this->expectException(Exception::class);
//    }

    /**
     * @group API-96
     * @throws Exception on error
     */
    public function testPurgeEntryInUnknownCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->generateRandomID();
        $this->isID($entryID);

        $result = $this->useCMDBCategory()->purge($objectID, 'C__CATG__UNKNOWN', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__UNKNOWN', $entryID);
    }

    /**
     * @group API-97
     * @throws Exception on error
     */
    public function testRecycleSingleValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->recycle($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNormalEntry($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group API-97
     * @throws Exception on error
     */
    public function testRecycleEntryInMultiValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->recycle($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group API-97
     * @throws Exception on error
     */
    public function testRecycleArchivedSingleValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->archive($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchivedEntry($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->recycle($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNormalEntry($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group API-97
     * @throws Exception on error
     */
    public function testRecycleArchivedEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->archive($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isArchivedEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->recycle($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group API-97
     * @throws Exception on error
     */
    public function testRecycleDeletedSingleValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->delete($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeletedEntry($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->recycle($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNormalEntry($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group API-97
     * @throws Exception on error
     */
    public function testRecycleDeletedEntryInMultiValueCategory() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->delete($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isDeletedEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->recycle($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group API-97
     * @throws Exception on error
     */
    public function testRecyclePurgedSingleValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->purge($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__MODEL', $entryID);

        $result = $this->useCMDBCategory()->recycle($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNormalEntry($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group API-97
     * @throws Exception on error
     */
    public function testRecyclePurgedEntryInMultiValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->purge($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->recycle($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group API-97
     * @throws Exception on error
     */
    public function testRecycleNonExistingSingleValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->generateRandomID();
        $this->isID($entryID);

        $result = $this->useCMDBCategory()->recycle($objectID, 'C__CATG__MODEL', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNormalEntry($objectID, 'C__CATG__MODEL', $entryID);
    }

    /**
     * @group API-97
     * @throws Exception on error
     */
    public function testRecycleNonExistingEntryInMultiValueCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->generateRandomID();
        $this->isID($entryID);

        $result = $this->useCMDBCategory()->recycle($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @group API-97
     * @throws Exception on error
     */
//    public function testRecycleEmptySingleValueCategory() {
//        // @todo Not testable because we need an entry ID!
//        $this->expectException(Exception::class);
//    }

    /**
     * @group API-97
     * @throws Exception on error
     */
//    public function testRecycleUnknownEntryInMultiValueCategory() {
//        // @todo Not testable because we need an entry ID!
//        $this->expectException(Exception::class);
//    }

    /**
     * @group API-97
     * @throws Exception on error
     */
    public function testRecycleEntryInUnknownCategory() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->generateRandomID();
        $this->isID($entryID);

        $result = $this->useCMDBCategory()->recycle($objectID, 'C__CATG__UNKNOWN', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNormalEntry($objectID, 'C__CATG__UNKNOWN', $entryID);
    }

    /**
     * @throws Exception on error
     */
    public function testClear() {
        // Create data:
        $objectID = $this->createServer();
        $this->addIPv4($objectID);
        $this->addIPv4($objectID);
        $this->addContact($objectID, 9, 1);

        // Run tests:
        $result = $this->useCMDBCategory()->clear($objectID, [
            'C__CATG__IP',
            'C__CATG__CONTACT'
        ]);

        $this->assertIsInt($result);
        $this->assertSame(3, $result);

        // Check:
        $entries = $this->useCMDBCategory()->read($objectID, 'C__CATG__IP', 2);

        $this->assertIsArray($entries);
        $this->assertCount(0, $entries);

        $entries = $this->useCMDBCategory()->read($objectID, 'C__CATG__CONTACT', 2);

        $this->assertIsArray($entries);
        $this->assertCount(0, $entries);
    }

    /**
     * @throws Exception on error
     */
    public function testClearWithMixedStates() {
        // Create data:
        $objectID = $this->createServer();
        $this->isID($objectID);

        $numberOfEntries = 3;
        $entryIDs = [];

        for ($index = 0; $index < $numberOfEntries; $index++) {
            $entryIDs[] = $entryID = $this->addIPv4($objectID);
            $this->isID($entryID);
        }

        $this->useCMDBCategory()->archive($objectID, 'C__CATG__IP', $entryIDs[1]);
        $this->useCMDBCategory()->delete($objectID, 'C__CATG__IP', $entryIDs[2]);

        // Run tests:
        $result = $this->useCMDBCategory()->clear($objectID, ['C__CATG__IP']);

        $this->assertIsInt($result);
        $this->assertSame(1, $result);

        // Check:
        $entries = $this->useCMDBCategory()->read($objectID, 'C__CATG__IP', 2);

        $this->assertIsArray($entries);
        $this->assertCount(0, $entries);
    }

    /**
     * @throws Exception on error
     */
    public function testClearEmptyCategory() {
        $objectID = $this->createServer();

        $result = $this->useCMDBCategory()->clear($objectID, [
            'C__CATG__IP',
            'C__CATG__CONTACT'
        ]);

        $this->assertIsInt($result);
        $this->assertSame(0, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testQuickPurge() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);

        $result = $this->useCMDBCategory()->quickPurge($objectID, 'C__CATG__IP', $entryID);
        $this->assertInstanceOf(CMDBCategory::class, $result);
        $this->isNotAvailable($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @throws Exception on error
     */
    public function testLegacyArchive() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);

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

        $this->isArchivedEntry($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @throws Exception on error
     */
    public function testLegacyDelete() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);

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

        $this->isDeletedEntry($objectID, 'C__CATG__IP', $entryID);
    }

    /**
     * @throws Exception on error
     */
    public function testLegacyPurge() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->addIPv4($objectID);
        $this->isID($entryID);
        $this->isNormalEntry($objectID, 'C__CATG__IP', $entryID);

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

        $this->isNotAvailable($objectID, 'C__CATG__IP', $entryID);
    }

}
