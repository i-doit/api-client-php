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

namespace bheisig\idoitapi\tests\Issues;

use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @group API-45
 * @see https://i-doit.atlassian.net/browse/API-45
 */
class API45Test extends BaseTest {

    /**
     * @throws \Exception on error
     */
    public function testVerifyStatusOfTemplate() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $this->cmdbObject->markAsTemplate($objectID);

        $result = $this->cmdbCategory->readFirst($objectID, 'C__CATG__GLOBAL');
        $this->assertInternalType('array', $result);

        $this->assertArrayHasKey('status', $result);
        // This failed because "status" was null:
        $this->assertInternalType('array', $result['status']);

        $this->assertArrayHasKey('id', $result['status']);
        $this->assertInternalType('string', $result['status']['id']);
        $this->assertSame(6, (int) $result['status']['id']);

        $this->assertArrayHasKey('title', $result['status']);
        $this->assertInternalType('string', $result['status']['title']);
        $this->assertSame('Template', $result['status']['title']);

        $this->assertArrayHasKey('const', $result['status']);
        $this->assertInternalType('string', $result['status']['const']);
        $this->assertEmpty($result['status']['const']);

        $this->assertArrayHasKey('title_lang', $result['status']);
        $this->assertInternalType('string', $result['status']['title_lang']);
        $this->assertSame('LC__CMDB_STATUS__IDOIT_STATUS_TEMPLATE', $result['status']['title_lang']);
    }

    /**
     * @throws \Exception on error
     */
    public function testVerifyStatusOfMassChangeTemplate() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $this->cmdbObject->markAsMassChangeTemplate($objectID);

        $result = $this->cmdbCategory->readFirst($objectID, 'C__CATG__GLOBAL');
        $this->assertInternalType('array', $result);

        $this->assertArrayHasKey('status', $result);
        // This failed because "status" was null:
        $this->assertInternalType('array', $result['status']);

        $this->assertArrayHasKey('id', $result['status']);
        $this->assertInternalType('string', $result['status']['id']);
        $this->assertSame(7, (int) $result['status']['id']);

        $this->assertArrayHasKey('title', $result['status']);
        $this->assertInternalType('string', $result['status']['title']);
        $this->assertSame('Mass change template', $result['status']['title']);

        $this->assertArrayHasKey('const', $result['status']);
        $this->assertInternalType('string', $result['status']['const']);
        $this->assertEmpty($result['status']['const']);

        $this->assertArrayHasKey('title_lang', $result['status']);
        $this->assertInternalType('string', $result['status']['title_lang']);
        $this->assertSame('LC__MASS_CHANGE__CHANGE_TEMPLATE', $result['status']['title_lang']);
    }

    /**
     * @throws \Exception on error
     */
    public function testAddSingleValueCategoryEntryToTemplate() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $this->cmdbObject->markAsTemplate($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);

        $result = $this->cmdbCategory->readFirst($objectID, 'C__CATG__MODEL');
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
    public function testAddMultiValueCategoryEntryToTemplate() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $this->cmdbObject->markAsTemplate($objectID);

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
    public function testAddSingleValueCategoryEntryToMassChangeTemplate() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $this->cmdbObject->markAsMassChangeTemplate($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);

        $result = $this->cmdbCategory->readFirst($objectID, 'C__CATG__MODEL');
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
    public function testAddMultiValueCategoryEntryToMassChangeTemplate() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $this->cmdbObject->markAsMassChangeTemplate($objectID);

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

}
