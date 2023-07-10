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
 * @group API-131
 * @see https://i-doit.atlassian.net/browse/API-131
 */
class API131Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testPerson() {
        $firstName = 'John';
        $lastName = 'Doe';
        $title = $firstName . ' ' . $lastName;
        $categoryConstant = Category::CATS__PERSON_MASTER;

        $objectID = $this->useCMDBObject()->create(ObjectType::PERSON, $title);

        // Original:

        $entry = $this->useCMDBCategory()->readFirst($objectID, $categoryConstant);

        $this->assertIsArray($entry);
        $this->assertArrayHasKey('id', $entry);
        $this->isIDAsString($entry['id']);
        $this->assertArrayHasKey('objID', $entry);
        $this->isIDAsString($entry['objID']);
        $this->assertSame($objectID, (int) $entry['objID']);

        $this->assertArrayHasKey('first_name', $entry);
        $this->assertIsString($entry['first_name']);
        $this->assertSame($firstName, $entry['first_name']);

        $this->assertArrayHasKey('last_name', $entry);
        $this->assertIsString($entry['last_name']);
        $this->assertSame($lastName, $entry['last_name']);

        // Update by "cmdb.object.update":

        $newFirstName = 'Jane';
        $newLastName = 'Doedoe';
        $newTitle = $newFirstName . ' ' . $newLastName;

        $this->assertNotSame($firstName, $newFirstName);
        $this->assertNotSame($lastName, $newLastName);
        $this->assertNotSame($title, $newTitle);

        $this->useCMDBObject()->update($objectID, ['title' => $newTitle]);

        $updatedEntry = $this->useCMDBCategory()->readFirst($objectID, $categoryConstant);

        $this->assertIsArray($updatedEntry);
        $this->assertArrayHasKey('id', $updatedEntry);
        $this->isIDAsString($updatedEntry['id']);
        $this->assertArrayHasKey('objID', $updatedEntry);
        $this->isIDAsString($updatedEntry['objID']);
        $this->assertSame($objectID, (int) $updatedEntry['objID']);

        $this->assertArrayHasKey('first_name', $updatedEntry);
        $this->assertIsString($updatedEntry['first_name']);
        $this->assertSame($newFirstName, $updatedEntry['first_name']);

        $this->assertArrayHasKey('last_name', $updatedEntry);
        $this->assertIsString($updatedEntry['last_name']);
        $this->assertSame($newLastName, $updatedEntry['last_name']);

        // Update by "cmdb.category.update" (go back to old name):

        $this->useCMDBCategory()->update($objectID, $categoryConstant, [
            'first_name' => $firstName,
            'last_name' => $lastName
        ]);

        $entry = $this->useCMDBCategory()->readFirst($objectID, $categoryConstant);

        $this->assertIsArray($entry);
        $this->assertArrayHasKey('id', $entry);
        $this->isIDAsString($entry['id']);
        $this->assertArrayHasKey('objID', $entry);
        $this->isIDAsString($entry['objID']);
        $this->assertSame($objectID, (int) $entry['objID']);

        $this->assertArrayHasKey('first_name', $entry);
        $this->assertIsString($entry['first_name']);
        $this->assertSame($firstName, $entry['first_name']);

        $this->assertArrayHasKey('last_name', $entry);
        $this->assertIsString($entry['last_name']);
        $this->assertSame($lastName, $entry['last_name']);

        $object = $this->useCMDBObject()->read($objectID);
        $this->assertIsArray($object);
        $this->assertArrayHasKey('id', $object);
        $this->isID($object['id']);
        $this->assertArrayHasKey('title', $object);
        $this->assertIsString($object['title']);
        $this->assertSame($title, $object['title']);
    }

    /**
     * @throws Exception on error
     */
    public function testPersonGroup() {
        $title = $this->generateRandomString();
        $categoryConstant = Category::CATS__PERSON_GROUP_MASTER;

        $objectID = $this->useCMDBObject()->create(ObjectType::PERSON_GROUP, $title);

        // Original:

        $entry = $this->useCMDBCategory()->readFirst($objectID, $categoryConstant);

        $this->assertIsArray($entry);
        $this->assertArrayHasKey('id', $entry);
        $this->isIDAsString($entry['id']);
        $this->assertArrayHasKey('objID', $entry);
        $this->isIDAsString($entry['objID']);
        $this->assertSame($objectID, (int) $entry['objID']);

        $this->assertArrayHasKey('title', $entry);
        $this->assertIsString($entry['title']);
        $this->assertSame($title, $entry['title']);

        // Update by "cmdb.object.update":

        $newTitle = $this->generateRandomString();

        $this->assertNotSame($title, $newTitle);

        $this->useCMDBObject()->update($objectID, ['title' => $newTitle]);

        $updatedEntry = $this->useCMDBCategory()->readFirst($objectID, $categoryConstant);

        $this->assertIsArray($updatedEntry);
        $this->assertArrayHasKey('id', $updatedEntry);
        $this->isIDAsString($updatedEntry['id']);
        $this->assertArrayHasKey('objID', $updatedEntry);
        $this->isIDAsString($updatedEntry['objID']);
        $this->assertSame($objectID, (int) $updatedEntry['objID']);

        $this->assertArrayHasKey('title', $updatedEntry);
        $this->assertIsString($updatedEntry['title']);
        $this->assertSame($newTitle, $updatedEntry['title']);

        // Update by "cmdb.category.update" (go back to old name):

        $this->useCMDBCategory()->update($objectID, $categoryConstant, [
            'title' => $title
        ]);

        $entry = $this->useCMDBCategory()->readFirst($objectID, $categoryConstant);

        $this->assertIsArray($entry);
        $this->assertArrayHasKey('id', $entry);
        $this->isIDAsString($entry['id']);
        $this->assertArrayHasKey('objID', $entry);
        $this->isIDAsString($entry['objID']);
        $this->assertSame($objectID, (int) $entry['objID']);

        $this->assertArrayHasKey('title', $entry);
        $this->assertIsString($entry['title']);
        $this->assertSame($title, $entry['title']);

        $object = $this->useCMDBObject()->read($objectID);
        $this->assertIsArray($object);
        $this->assertArrayHasKey('id', $object);
        $this->isID($object['id']);
        $this->assertArrayHasKey('title', $object);
        $this->assertIsString($object['title']);
        $this->assertSame($title, $object['title']);
    }

    /**
     * @throws Exception on error
     */
    public function testOrganization() {
        $title = $this->generateRandomString();
        $categoryConstant = Category::CATS__ORGANIZATION_MASTER_DATA;

        $objectID = $this->useCMDBObject()->create(ObjectType::ORGANIZATION, $title);

        // Original:

        $entry = $this->useCMDBCategory()->readFirst($objectID, $categoryConstant);

        print_r($entry);

        $this->assertIsArray($entry);
        $this->assertArrayHasKey('id', $entry);
        $this->isIDAsString($entry['id']);
        $this->assertArrayHasKey('objID', $entry);
        $this->isIDAsString($entry['objID']);
        $this->assertSame($objectID, (int) $entry['objID']);

        $this->assertArrayHasKey('title', $entry);
        $this->assertIsString($entry['title']);
        $this->assertSame($title, $entry['title']);

        // Update by "cmdb.object.update":

        $newTitle = $this->generateRandomString();

        $this->assertNotSame($title, $newTitle);

        $this->useCMDBObject()->update($objectID, ['title' => $newTitle]);

        $updatedEntry = $this->useCMDBCategory()->readFirst($objectID, $categoryConstant);

        $this->assertIsArray($updatedEntry);
        $this->assertArrayHasKey('id', $updatedEntry);
        $this->isIDAsString($updatedEntry['id']);
        $this->assertArrayHasKey('objID', $updatedEntry);
        $this->isIDAsString($updatedEntry['objID']);
        $this->assertSame($objectID, (int) $updatedEntry['objID']);

        $this->assertArrayHasKey('title', $updatedEntry);
        $this->assertIsString($updatedEntry['title']);
        $this->assertSame($newTitle, $updatedEntry['title']);

        // Update by "cmdb.category.update" (go back to old name):

        $this->useCMDBCategory()->update($objectID, $categoryConstant, [
            'title' => $title
        ]);

        $entry = $this->useCMDBCategory()->readFirst($objectID, $categoryConstant);

        $this->assertIsArray($entry);
        $this->assertArrayHasKey('id', $entry);
        $this->isIDAsString($entry['id']);
        $this->assertArrayHasKey('objID', $entry);
        $this->isIDAsString($entry['objID']);
        $this->assertSame($objectID, (int) $entry['objID']);

        $this->assertArrayHasKey('title', $entry);
        $this->assertIsString($entry['title']);
        $this->assertSame($title, $entry['title']);

        $object = $this->useCMDBObject()->read($objectID);
        $this->assertIsArray($object);
        $this->assertArrayHasKey('id', $object);
        $this->isID($object['id']);
        $this->assertArrayHasKey('title', $object);
        $this->assertIsString($object['title']);
        $this->assertSame($title, $object['title']);
    }

}
