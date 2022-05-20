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
use Idoit\APIClient\Constants\ObjectType;

/**
 * @group issues
 * @group API-39
 * @see https://i-doit.atlassian.net/browse/API-39
 */
class API39Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testIssue() {
        $hostID = $this->createServer();
        $this->isID($hostID);

        // Install primary operating system:
        $primaryOSID = $this->useCMDBObject()->create(
            ObjectType::OPERATING_SYSTEM,
            $this->generateRandomString()
        );
        $this->isID($primaryOSID);

        $firstEntryID = $this->useCMDBCategory()->create(
            $hostID,
            Category::CATG__APPLICATION,
            [
                'application' => $primaryOSID,
                'application_type' => 2, // 2 = operating system
                'application_priority' => 1
            ]
        );
        $this->isID($firstEntryID);

        // Validate primary operating system:
        $firstEntry = $this->useCMDBCategory()->readOneByID(
            $hostID,
            Category::CATG__APPLICATION,
            $firstEntryID
        );
        $this->assertIsArray($firstEntry);
        $this->validateCategoryEntry($hostID, $firstEntryID, $firstEntry);

        $this->hasApplication($primaryOSID, $firstEntry);

        $this->hasPriority(1, $firstEntry);

        // Install secondary operating system:
        $secondaryOSID = $this->useCMDBObject()->create(
            ObjectType::OPERATING_SYSTEM,
            $this->generateRandomString()
        );
        $this->isID($secondaryOSID);

        $secondEntryID = $this->useCMDBCategory()->create(
            $hostID,
            Category::CATG__APPLICATION,
            [
                'application' => $secondaryOSID,
                'application_type' => 2, // 2 = operating system
                'application_priority' => 2
            ]
        );
        $this->isID($secondEntryID);

        // Validate secondary operating system:
        $secondEntry = $this->useCMDBCategory()->readOneByID(
            $hostID,
            Category::CATG__APPLICATION,
            $secondEntryID
        );
        $this->assertIsArray($secondEntry);
        $this->validateCategoryEntry($hostID, $secondEntryID, $secondEntry);

        $this->hasApplication($secondaryOSID, $secondEntry);

        $this->hasPriority(2, $secondEntry);

        // Switch priority:
        $this->useCMDBCategory()->update(
            $hostID,
            Category::CATG__APPLICATION,
            [
                'application_priority' => 2
            ],
            $firstEntryID
        );

        $this->useCMDBCategory()->update(
            $hostID,
            Category::CATG__APPLICATION,
            [
                'application_priority' => 1
            ],
            $secondEntryID
        );

        // Validate that former primary operating system is now the secondary one:
        $firstEntry = $this->useCMDBCategory()->readOneByID(
            $hostID,
            Category::CATG__APPLICATION,
            $firstEntryID
        );
        $this->assertIsArray($firstEntry);
        $this->validateCategoryEntry($hostID, $firstEntryID, $firstEntry);

        $this->hasApplication($primaryOSID, $firstEntry);

        $this->hasPriority(2, $firstEntry);

        // Validate that former secondary operating system is now the primary one:
        $secondEntry = $this->useCMDBCategory()->readOneByID(
            $hostID,
            Category::CATG__APPLICATION,
            $secondEntryID
        );
        $this->assertIsArray($secondEntry);
        $this->validateCategoryEntry($hostID, $secondEntryID, $secondEntry);
        $this->hasApplication($secondaryOSID, $secondEntry);
        $this->hasPriority(1, $secondEntry);
    }

    protected function validateCategoryEntry(int $objectID, int $entryID, array $entry) {
        $this->assertArrayHasKey('id', $entry);
        $this->isIDAsString($entry['id']);
        $this->assertSame($entryID, (int) $entry['id']);
        $this->assertArrayHasKey('objID', $entry);
        $this->isIDAsString($entry['objID']);
        $this->assertSame($objectID, (int) $entry['objID']);
    }

    protected function hasApplication(int $applicationID, array $entry) {
        $this->assertArrayHasKey('application', $entry);
        $this->assertIsArray($entry['application']);
        $this->assertArrayHasKey('id', $entry['application']);
        $this->isIDAsString($entry['application']['id']);
        $this->assertSame($applicationID, (int) $entry['application']['id']);
    }

    protected function hasPriority(int $priority, array $entry) {
        $this->assertArrayHasKey('application_priority', $entry);
        $this->assertIsArray($entry['application_priority']);

        $this->assertArrayHasKey('id', $entry['application_priority']);
        $this->isIDAsString($entry['application_priority']['id']);
        $this->assertSame($priority, (int) $entry['application_priority']['id']);

        $this->assertArrayHasKey('title', $entry['application_priority']);
        $this->assertIsString($entry['application_priority']['title']);
        $this->isOneLiner($entry['application_priority']['title']);

        $this->assertArrayHasKey('const', $entry['application_priority']);
        $this->assertIsString($entry['application_priority']['const']);
        $this->isConstant($entry['application_priority']['const']);

        $this->assertArrayHasKey('title_lang', $entry['application_priority']);
        $this->assertIsString($entry['application_priority']['title_lang']);
        $this->isOneLiner($entry['application_priority']['title_lang']);
    }

}
