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

use bheisig\idoitapi\tests\Constants\Category;
use \Exception;
use bheisig\idoitapi\tests\BaseTest;
use bheisig\idoitapi\CMDBDialog;

/**
 * @group issues
 * @group API-29
 * @see https://i-doit.atlassian.net/browse/API-29
 */
class API29Test extends BaseTest {

    /**
     * @var CMDBDialog
     */
    protected $cmdbDialog;

    /**
     * @throws Exception on error
     */
    public function setUp(): void {
        parent::setUp();

        $this->cmdbDialog = new CMDBDialog($this->api);
    }

    /**
     * @throws Exception on error
     */
    public function testIssue() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $person = $this->createPerson();
        $personID = $person['id'];
        $this->isID($personID);

        $roles = $this->cmdbDialog->read(Category::CATG__CONTACT, 'role');

        // This is a little overhead, because one role would be enough…
        foreach ($roles as $role) {
            // 1st test: Check whether defined roles are suitable:
            $this->assertIsArray($role);

            $this->assertArrayHasKey('id', $role);
            $this->isIDAsString($role['id']);

            $this->assertArrayHasKey('title', $role);
            $this->isOneLiner($role['title']);

            // 2nd test: Assign role by its title:
            $entryID = $this->useCMDBCategory()->create($objectID, Category::CATG__CONTACT, [
                'contact' => $personID,
                'role' => $role['title']
            ]);

            $result = $this->useCMDBCategory()->readOneByID($objectID, Category::CATG__CONTACT, $entryID);

            $this->assertIsArray($result);
            $this->assertArrayHasKey('id', $result);
            $this->isIDAsString($result['id']);
            $this->assertSame($entryID, (int) $result['id']);
            $this->assertArrayHasKey('objID', $result);
            $this->isIDAsString($result['objID']);
            $this->assertSame($objectID, (int) $result['objID']);
            $this->assertArrayHasKey('contact_object', $result);
            $this->assertIsArray($result['contact_object']);
            $this->isAssignedObject($result['contact_object']);
            $this->assertSame($personID, (int) $result['contact_object']['id']);

            $this->assertArrayHasKey('role', $result);
            $this->isDialog($result['role']);

            // This is the important part:
            $this->assertSame($role['id'], $result['role']['id']);
            $this->assertSame($role['title'], $result['role']['title']);

            // 3rd test: Assign role by its identifier (as integer!):
            $entryID = $this->useCMDBCategory()->create($objectID, Category::CATG__CONTACT, [
                'contact' => $personID,
                'role' => (int) $role['id']
            ]);

            $result = $this->useCMDBCategory()->readOneByID($objectID, Category::CATG__CONTACT, $entryID);

            $this->assertIsArray($result);
            $this->assertArrayHasKey('id', $result);
            $this->isIDAsString($result['id']);
            $this->assertSame($entryID, (int) $result['id']);
            $this->assertArrayHasKey('objID', $result);
            $this->isIDAsString($result['objID']);
            $this->assertSame($objectID, (int) $result['objID']);
            $this->assertArrayHasKey('contact_object', $result);
            $this->assertIsArray($result['contact_object']);
            $this->isAssignedObject($result['contact_object']);
            $this->assertSame($personID, (int) $result['contact_object']['id']);

            $this->assertArrayHasKey('role', $result);
            $this->isDialog($result['role']);

            // This is the important part:
            $this->assertSame($role['id'], $result['role']['id']);
            $this->assertSame($role['title'], $result['role']['title']);

            // 4th test: Assign role by its identifier (as string!):
            $entryID = $this->useCMDBCategory()->create($objectID, Category::CATG__CONTACT, [
                'contact' => $personID,
                'role' => $role['id']
            ]);

            $result = $this->useCMDBCategory()->readOneByID($objectID, Category::CATG__CONTACT, $entryID);

            $this->assertIsArray($result);
            $this->assertArrayHasKey('id', $result);
            $this->isIDAsString($result['id']);
            $this->assertSame($entryID, (int) $result['id']);
            $this->assertArrayHasKey('objID', $result);
            $this->isIDAsString($result['objID']);
            $this->assertSame($objectID, (int) $result['objID']);
            $this->assertArrayHasKey('contact_object', $result);
            $this->assertIsArray($result['contact_object']);
            $this->isAssignedObject($result['contact_object']);
            $this->assertSame($personID, (int) $result['contact_object']['id']);

            $this->assertArrayHasKey('role', $result);
            $this->isDialog($result['role']);

            // This is the important part:
            $this->assertNotSame($role['id'], $result['role']['id']);
            $this->assertNotSame($role['title'], $result['role']['title']);
            $this->assertSame($role['id'], $result['role']['title']);
        }
    }

}
