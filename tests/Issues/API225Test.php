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

namespace bheisig\idoitapi\tests\Issues;

use bheisig\idoitapi\tests\Constants\Category;
use bheisig\idoitapi\tests\Constants\ObjectType;
use \Exception;
use bheisig\idoitapi\tests\BaseTest;

/**
 * @group unreleased
 * @group issues
 * @group API-225
 * @see https://i-doit.atlassian.net/browse/API-225
 */
class API225Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testSave() {
        $adminRole = 1;
        $userRole = 2;

        /**
         * Create test data:
         */

        $hostID = $this->createServer();
        $this->isID($hostID);

        $personID = $this->cmdbObject->create(
            ObjectType::PERSON,
            $this->generateRandomString()
        );
        $this->isID($personID);

        $entryID = $this->useCMDBCategory()->save(
            $hostID,
            Category::CATG__CONTACT,
            [
                'contact' => $personID,
                'role' => $adminRole,
                'description' => $this->generateDescription()
            ]
        );

        /**
         * Run tests:
         */

        $entries = $this->useCMDBCategory()->read(
            $hostID,
            Category::CATG__CONTACT
        );

        $this->assertIsArray($entries);
        $this->validateEntries($entries, $hostID, $personID, $adminRole, $entryID);

        /**
         * Update entry:
         */

        $sameEntryID = $this->useCMDBCategory()->save(
            $hostID,
            Category::CATG__CONTACT,
            [
                'contact' => $personID,
                'role' => $userRole
            ],
            $entryID
        );

        $this->assertSame($entryID, $sameEntryID);

        /**
         * Run tests again:
         */

        $entries = $this->useCMDBCategory()->read(
            $hostID,
            Category::CATG__CONTACT
        );

        $this->assertIsArray($entries);
        $this->validateEntries($entries, $hostID, $personID, $userRole, $entryID);
    }

    /**
     * @throws Exception on error
     */
    public function testCreateAndUpdate() {
        $adminRole = 1;
        $userRole = 2;

        /**
         * Create test data:
         */

        $hostID = $this->createServer();
        $this->isID($hostID);

        $personID = $this->cmdbObject->create(
            ObjectType::PERSON,
            $this->generateRandomString()
        );
        $this->isID($personID);

        $entryID = $this->useCMDBCategory()->create(
            $hostID,
            Category::CATG__CONTACT,
            [
                'contact' => $personID,
                'role' => $adminRole,
                'description' => $this->generateDescription()
            ]
        );

        /**
         * Run tests:
         */

        $entries = $this->useCMDBCategory()->read(
            $hostID,
            Category::CATG__CONTACT
        );

        $this->assertIsArray($entries);
        $this->validateEntries($entries, $hostID, $personID, $adminRole, $entryID);

        /**
         * Update entry:
         */

        $this->useCMDBCategory()->update(
            $hostID,
            Category::CATG__CONTACT,
            [
                'contact' => $personID,
                'role' => $userRole
            ],
            $entryID
        );

        /**
         * Run tests again:
         */

        $entries = $this->useCMDBCategory()->read(
            $hostID,
            Category::CATG__CONTACT
        );

        $this->assertIsArray($entries);
        $this->validateEntries($entries, $hostID, $personID, $userRole, $entryID);
    }

    protected function validateEntries(array $entries, int $hostID, int $personID, int $roleID, int $entryID) {
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);
        $this->assertIsArray($entries[0]);
        $this->isCategoryEntry($entries[0]);

        $this->assertSame($entryID, (int) $entries[0]['id']);

        $this->assertSame($hostID, (int) $entries[0]['objID']);

        $this->assertArrayHasKey('contact', $entries[0]);
        $this->assertIsArray($entries[0]['contact']);
        $this->assertArrayHasKey('id', $entries[0]['contact']);
        $this->assertSame($personID, (int) $entries[0]['contact']['id']);
        $this->assertArrayHasKey('type', $entries[0]['contact']);
        $this->assertSame(ObjectType::PERSON, $entries[0]['contact']['type']);

        $this->assertArrayHasKey('contact_object', $entries[0]);
        $this->assertIsArray($entries[0]['contact_object']);
        $this->assertArrayHasKey('id', $entries[0]['contact_object']);
        $this->assertSame($personID, (int) $entries[0]['contact_object']['id']);
        $this->assertArrayHasKey('type', $entries[0]['contact_object']);
        $this->assertSame(ObjectType::PERSON, $entries[0]['contact_object']['type']);

        $this->assertArrayHasKey('role', $entries[0]);
        $this->assertIsArray($entries[0]['role']);
        $this->assertArrayHasKey('id', $entries[0]['role']);
        $this->assertSame($roleID, (int) $entries[0]['role']['id']);
    }

}
