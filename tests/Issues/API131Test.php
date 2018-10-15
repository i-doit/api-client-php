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
 * @group unreleased
 * @see https://i-doit.atlassian.net/browse/API-131
 */
class API131Test extends BaseTest {

    /**
     * @throws \Exception on error
     */
    public function testPerson() {
        $firstName = 'John';
        $lastName = 'Doe';
        $title = $firstName . ' ' . $lastName;
        $categoryConstant = 'C__CATS__PERSON_MASTER';

        $objectID = $this->cmdbObject->create('C__OBJTYPE__PERSON', $title);

        // Original:

        $entry = $this->cmdbCategory->readFirst($objectID, $categoryConstant);

        $this->assertInternalType('array', $entry);
        $this->assertArrayHasKey('id', $entry);
        $this->isIDAsString($entry['id']);
        $this->assertArrayHasKey('objID', $entry);
        $this->isIDAsString($entry['objID']);
        $this->assertSame($objectID, (int) $entry['objID']);

        $this->assertArrayHasKey('title', $entry);
        $this->assertInternalType('string', $entry['title']);
        $this->assertSame($title, $entry['title']);

        $this->assertArrayHasKey('first_name', $entry);
        $this->assertInternalType('string', $entry['first_name']);
        $this->assertSame($firstName, $entry['first_name']);

        $this->assertArrayHasKey('last_name', $entry);
        $this->assertInternalType('string', $entry['last_name']);
        $this->assertSame($lastName, $entry['last_name']);

        // Update by "cmdb.object.update":

        $newFirstName = 'Jane';
        $newLastName = 'Doedoe';
        $newTitle = $newFirstName . ' ' . $newLastName;

        $this->assertNotSame($firstName, $newFirstName);
        $this->assertNotSame($lastName, $newLastName);
        $this->assertNotSame($title, $newTitle);

        $this->cmdbObject->update($objectID, ['title' => $newTitle]);

        $updatedEntry = $this->cmdbCategory->readFirst($objectID, $categoryConstant);

        $this->assertInternalType('array', $updatedEntry);
        $this->assertArrayHasKey('id', $updatedEntry);
        $this->isIDAsString($updatedEntry['id']);
        $this->assertArrayHasKey('objID', $updatedEntry);
        $this->isIDAsString($updatedEntry['objID']);
        $this->assertSame($objectID, (int) $updatedEntry['objID']);

        $this->assertArrayHasKey('title', $updatedEntry);
        $this->assertInternalType('string', $updatedEntry['title']);
        $this->assertSame($newTitle, $updatedEntry['title']);

        $this->assertArrayHasKey('first_name', $updatedEntry);
        $this->assertInternalType('string', $updatedEntry['first_name']);
        $this->assertSame($newFirstName, $updatedEntry['first_name']);

        $this->assertArrayHasKey('last_name', $updatedEntry);
        $this->assertInternalType('string', $updatedEntry['last_name']);
        $this->assertSame($newLastName, $updatedEntry['last_name']);

        // Update by "cmdb.category.update" (go back to old name):

        $this->cmdbCategory->update($objectID, $categoryConstant, [
            'first_name' => $firstName,
            'last_name' => $lastName
        ]);

        $entry = $this->cmdbCategory->readFirst($objectID, $categoryConstant);

        $this->assertInternalType('array', $entry);
        $this->assertArrayHasKey('id', $entry);
        $this->isIDAsString($entry['id']);
        $this->assertArrayHasKey('objID', $entry);
        $this->isIDAsString($entry['objID']);
        $this->assertSame($objectID, (int) $entry['objID']);

        $this->assertArrayHasKey('title', $entry);
        $this->assertInternalType('string', $entry['title']);
        $this->assertSame($title, $entry['title']);

        $this->assertArrayHasKey('first_name', $entry);
        $this->assertInternalType('string', $entry['first_name']);
        $this->assertSame($firstName, $entry['first_name']);

        $this->assertArrayHasKey('last_name', $entry);
        $this->assertInternalType('string', $entry['last_name']);
        $this->assertSame($lastName, $entry['last_name']);

        $object = $this->cmdbObject->read($objectID);
        $this->assertInternalType('array', $object);
        $this->assertArrayHasKey('id', $object);
        $this->isID($object['id']);
        $this->assertArrayHasKey('title', $object);
        $this->assertInternalType('string', $object['title']);
        $this->assertSame($title, $object['title']);
    }

    /**
     * @throws \Exception on error
     */
    public function testPersonGroup() {
        $title = $this->generateRandomString();
        $categoryConstant = 'C__CATS__PERSON_GROUP_MASTER';

        $objectID = $this->cmdbObject->create('C__OBJTYPE__PERSON_GROUP', $title);

        // Original:

        $entry = $this->cmdbCategory->readFirst($objectID, $categoryConstant);

        $this->assertInternalType('array', $entry);
        $this->assertArrayHasKey('id', $entry);
        $this->isIDAsString($entry['id']);
        $this->assertArrayHasKey('objID', $entry);
        $this->isIDAsString($entry['objID']);
        $this->assertSame($objectID, (int) $entry['objID']);

        $this->assertArrayHasKey('title', $entry);
        $this->assertInternalType('string', $entry['title']);
        $this->assertSame($title, $entry['title']);

        // Update by "cmdb.object.update":

        $newTitle = $this->generateRandomString();

        $this->assertNotSame($title, $newTitle);

        $this->cmdbObject->update($objectID, ['title' => $newTitle]);

        $updatedEntry = $this->cmdbCategory->readFirst($objectID, $categoryConstant);

        $this->assertInternalType('array', $updatedEntry);
        $this->assertArrayHasKey('id', $updatedEntry);
        $this->isIDAsString($updatedEntry['id']);
        $this->assertArrayHasKey('objID', $updatedEntry);
        $this->isIDAsString($updatedEntry['objID']);
        $this->assertSame($objectID, (int) $updatedEntry['objID']);

        $this->assertArrayHasKey('title', $updatedEntry);
        $this->assertInternalType('string', $updatedEntry['title']);
        $this->assertSame($newTitle, $updatedEntry['title']);

        // Update by "cmdb.category.update" (go back to old name):

        $this->cmdbCategory->update($objectID, $categoryConstant, [
            'title' => $title
        ]);

        $entry = $this->cmdbCategory->readFirst($objectID, $categoryConstant);

        $this->assertInternalType('array', $entry);
        $this->assertArrayHasKey('id', $entry);
        $this->isIDAsString($entry['id']);
        $this->assertArrayHasKey('objID', $entry);
        $this->isIDAsString($entry['objID']);
        $this->assertSame($objectID, (int) $entry['objID']);

        $this->assertArrayHasKey('title', $entry);
        $this->assertInternalType('string', $entry['title']);
        $this->assertSame($title, $entry['title']);

        $object = $this->cmdbObject->read($objectID);
        $this->assertInternalType('array', $object);
        $this->assertArrayHasKey('id', $object);
        $this->isID($object['id']);
        $this->assertArrayHasKey('title', $object);
        $this->assertInternalType('string', $object['title']);
        $this->assertSame($title, $object['title']);
    }

    /**
     * @throws \Exception on error
     */
    public function testOrganization() {
        $title = $this->generateRandomString();
        $categoryConstant = 'C__CATS__ORGANIZATION_MASTER_DATA';

        $objectID = $this->cmdbObject->create('C__OBJTYPE__ORGANIZATION', $title);

        // Original:

        $entry = $this->cmdbCategory->readFirst($objectID, $categoryConstant);

        $this->assertInternalType('array', $entry);
        $this->assertArrayHasKey('id', $entry);
        $this->isIDAsString($entry['id']);
        $this->assertArrayHasKey('objID', $entry);
        $this->isIDAsString($entry['objID']);
        $this->assertSame($objectID, (int) $entry['objID']);

        $this->assertArrayHasKey('title', $entry);
        $this->assertInternalType('string', $entry['title']);
        $this->assertSame($title, $entry['title']);

        // Update by "cmdb.object.update":

        $newTitle = $this->generateRandomString();

        $this->assertNotSame($title, $newTitle);

        $this->cmdbObject->update($objectID, ['title' => $newTitle]);

        $updatedEntry = $this->cmdbCategory->readFirst($objectID, $categoryConstant);

        $this->assertInternalType('array', $updatedEntry);
        $this->assertArrayHasKey('id', $updatedEntry);
        $this->isIDAsString($updatedEntry['id']);
        $this->assertArrayHasKey('objID', $updatedEntry);
        $this->isIDAsString($updatedEntry['objID']);
        $this->assertSame($objectID, (int) $updatedEntry['objID']);

        $this->assertArrayHasKey('title', $updatedEntry);
        $this->assertInternalType('string', $updatedEntry['title']);
        $this->assertSame($newTitle, $updatedEntry['title']);

        // Update by "cmdb.category.update" (go back to old name):

        $this->cmdbCategory->update($objectID, $categoryConstant, [
            'title' => $title
        ]);

        $entry = $this->cmdbCategory->readFirst($objectID, $categoryConstant);

        $this->assertInternalType('array', $entry);
        $this->assertArrayHasKey('id', $entry);
        $this->isIDAsString($entry['id']);
        $this->assertArrayHasKey('objID', $entry);
        $this->isIDAsString($entry['objID']);
        $this->assertSame($objectID, (int) $entry['objID']);

        $this->assertArrayHasKey('title', $entry);
        $this->assertInternalType('string', $entry['title']);
        $this->assertSame($title, $entry['title']);

        $object = $this->cmdbObject->read($objectID);
        $this->assertInternalType('array', $object);
        $this->assertArrayHasKey('id', $object);
        $this->isID($object['id']);
        $this->assertArrayHasKey('title', $object);
        $this->assertInternalType('string', $object['title']);
        $this->assertSame($title, $object['title']);
    }

}
