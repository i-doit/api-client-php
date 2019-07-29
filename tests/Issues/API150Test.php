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

namespace bheisig\idoitapi\tests\Issues;

use \Exception;
use bheisig\idoitapi\CMDBCategory;
use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @group API-150
 * @see https://i-doit.atlassian.net/browse/API-150
 */
class API150Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testQuickPurgeExistingMember() {
        /**
         * Create test data:
         */

        $person = $this->createPerson();
        $this->isID($person['id']);

        $personGroupID = $this->useCMDBObject()->create(
            'C__OBJTYPE__PERSON_GROUP',
            $this->generateRandomString()
        );
        $this->isID($personGroupID);

        $entryID = $this->useCMDBCategory()->save(
            $personGroupID,
            'C__CATS__PERSON_GROUP_MEMBERS',
            [
                'connected_object' => $person['id']
            ]
        );
        $this->isID($entryID);

        /**
         * Verify test data:
         */

        $entries = $this->useCMDBCategory()->read(
            $personGroupID,
            'C__CATS__PERSON_GROUP_MEMBERS'
        );

        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->isCategoryEntry($entries[0]);
        $this->assertSame($entryID, (int) $entries[0]['id']);
        $this->assertSame($personGroupID, (int) $entries[0]['objID']);
        $this->assertArrayHasKey('connected_object', $entries[0]);
        $this->assertIsArray($entries[0]['connected_object']);
        $this->isAssignedObject($entries[0]['connected_object']);
        $this->assertSame($person['id'], (int) $entries[0]['connected_object']['id']);

        /**
         * Run actual tests:
         */

        $result = $this->useCMDBCategory()->quickPurge(
            $personGroupID,
            'C__CATS__PERSON_GROUP_MEMBERS',
            $entryID
        );

        $this->assertInstanceOf(CMDBCategory::class, $result);

        /**
         * Double check:
         */

        $entries = $this->useCMDBCategory()->read(
            $personGroupID,
            'C__CATS__PERSON_GROUP_MEMBERS'
        );

        $this->assertIsArray($entries);
        $this->assertCount(0, $entries);
    }

    /**
     * @throws Exception on error
     */
    public function testQuickPurgeNonExistingMemberFromEmptyGroup() {
        /**
         * Create test data:
         */

        $personGroupID = $this->useCMDBObject()->create(
            'C__OBJTYPE__PERSON_GROUP',
            $this->generateRandomString()
        );
        $this->isID($personGroupID);

        /**
         * Verify test data:
         */

        $entries = $this->useCMDBCategory()->read(
            $personGroupID,
            'C__CATS__PERSON_GROUP_MEMBERS'
        );

        $this->assertIsArray($entries);
        $this->assertCount(0, $entries);

        /**
         * Run actual tests:
         */

        $this->expectException(Exception::class);

        $this->useCMDBCategory()->quickPurge(
            $personGroupID,
            'C__CATS__PERSON_GROUP_MEMBERS',
            $this->generateRandomID()
        );
    }

    /**
     * @throws Exception on error
     */
    public function testQuickPurgeNonExistingMemberFromNonEmptyGroup() {
        /**
         * Create test data:
         */

        $person = $this->createPerson();
        $this->isID($person['id']);

        $personGroupID = $this->useCMDBObject()->create(
            'C__OBJTYPE__PERSON_GROUP',
            $this->generateRandomString()
        );
        $this->isID($personGroupID);

        $entryID = $this->useCMDBCategory()->save(
            $personGroupID,
            'C__CATS__PERSON_GROUP_MEMBERS',
            [
                'connected_object' => $person['id']
            ]
        );
        $this->isID($entryID);

        /**
         * Verify test data:
         */

        $entries = $this->useCMDBCategory()->read(
            $personGroupID,
            'C__CATS__PERSON_GROUP_MEMBERS'
        );

        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->isCategoryEntry($entries[0]);
        $this->assertSame($entryID, (int) $entries[0]['id']);
        $this->assertSame($personGroupID, (int) $entries[0]['objID']);
        $this->assertArrayHasKey('connected_object', $entries[0]);
        $this->assertIsArray($entries[0]['connected_object']);
        $this->isAssignedObject($entries[0]['connected_object']);
        $this->assertSame($person['id'], (int) $entries[0]['connected_object']['id']);

        /**
         * Run actual tests:
         */

        $this->expectException(Exception::class);

        $this->useCMDBCategory()->quickPurge(
            $personGroupID,
            'C__CATS__PERSON_GROUP_MEMBERS',
            $this->generateRandomID()
        );
    }

}
