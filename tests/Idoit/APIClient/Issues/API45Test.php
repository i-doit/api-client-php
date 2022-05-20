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

namespace Idoit\APIClient\Issues;

use \Exception;
use Idoit\APIClient\BaseTest;
use Idoit\APIClient\Constants\Category;

/**
 * @group issues
 * @group API-45
 * @see https://i-doit.atlassian.net/browse/API-45
 */
class API45Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testVerifyStatusOfTemplate() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $this->useCMDBObject()->markAsTemplate($objectID);

        $result = $this->useCMDBCategory()->readFirst($objectID, Category::CATG__GLOBAL);
        $this->assertIsArray($result);

        $this->assertArrayHasKey('status', $result);
        // This failed because "status" was null:
        $this->assertIsArray($result['status']);

        $this->assertArrayHasKey('id', $result['status']);
        $this->assertIsString($result['status']['id']);
        $this->assertSame(6, (int) $result['status']['id']);

        $this->assertArrayHasKey('title', $result['status']);
        $this->assertIsString($result['status']['title']);
        $this->assertSame('Template', $result['status']['title']);

        $this->assertArrayHasKey('const', $result['status']);
        $this->assertIsString($result['status']['const']);
        $this->assertEmpty($result['status']['const']);

        $this->assertArrayHasKey('title_lang', $result['status']);
        $this->assertIsString($result['status']['title_lang']);
        $this->assertSame('LC__CMDB_STATUS__IDOIT_STATUS_TEMPLATE', $result['status']['title_lang']);
    }

    /**
     * @throws Exception on error
     */
    public function testVerifyStatusOfMassChangeTemplate() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $this->useCMDBObject()->markAsMassChangeTemplate($objectID);

        $result = $this->useCMDBCategory()->readFirst($objectID, Category::CATG__GLOBAL);
        $this->assertIsArray($result);

        $this->assertArrayHasKey('status', $result);
        // This failed because "status" was null:
        $this->assertIsArray($result['status']);

        $this->assertArrayHasKey('id', $result['status']);
        $this->assertIsString($result['status']['id']);
        $this->assertSame(7, (int) $result['status']['id']);

        $this->assertArrayHasKey('title', $result['status']);
        $this->assertIsString($result['status']['title']);
        $this->assertSame('Mass change template', $result['status']['title']);

        $this->assertArrayHasKey('const', $result['status']);
        $this->assertIsString($result['status']['const']);
        $this->assertEmpty($result['status']['const']);

        $this->assertArrayHasKey('title_lang', $result['status']);
        $this->assertIsString($result['status']['title_lang']);
        $this->assertSame('LC__MASS_CHANGE__CHANGE_TEMPLATE', $result['status']['title_lang']);
    }

    /**
     * @throws Exception on error
     */
    public function testAddSingleValueCategoryEntryToTemplate() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $this->useCMDBObject()->markAsTemplate($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);

        $result = $this->useCMDBCategory()->readFirst($objectID, Category::CATG__MODEL);
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
    public function testAddMultiValueCategoryEntryToTemplate() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $this->useCMDBObject()->markAsTemplate($objectID);

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
    public function testAddSingleValueCategoryEntryToMassChangeTemplate() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $this->useCMDBObject()->markAsMassChangeTemplate($objectID);

        $entryID = $this->defineModel($objectID);
        $this->isID($entryID);

        $result = $this->useCMDBCategory()->readFirst($objectID, Category::CATG__MODEL);
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
    public function testAddMultiValueCategoryEntryToMassChangeTemplate() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $this->useCMDBObject()->markAsMassChangeTemplate($objectID);

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

}
