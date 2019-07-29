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

use bheisig\idoitapi\tests\Constants\Category;
use \Exception;
use bheisig\idoitapi\CMDBDialog;

/**
 * @coversDefaultClass \bheisig\idoitapi\CMDBDialog
 */
class CMDBDialogTest extends BaseTest {

    /**
     * @var CMDBDialog
     */
    protected $cmdbDialog;

    /**
     * @throws Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->cmdbDialog = new CMDBDialog($this->api);
    }

    /**
     * @throws Exception on error
     */
    public function testCreate() {
        $entryTitle = $this->generateRandomString();

        $entryID = $this->cmdbDialog->create(
            Category::CATG__CPU,
            'manufacturer',
            $entryTitle
        );

        $this->isID($entryID);

        $entries = $this->cmdbDialog->read(
            Category::CATG__CPU,
            'manufacturer'
        );

        $entry = end($entries);
        $this->isDialog($entry);

        $this->assertArrayHasKey('id', $entry);
        $this->isIDAsString($entry['id']);
        $this->assertSame($entryID, (int) $entry['id']);

        $this->assertArrayHasKey('title', $entry);
        $this->assertSame($entryTitle, $entry['title']);
    }

    /**
     * @group API-32
     * @throws Exception on error
     */
    public function testCreateWithParentTitle() {
        $parentTitle = $this->generateRandomString();

        $parentID = $this->cmdbDialog->create(
            Category::CATG__MODEL,
            'manufacturer',
            $parentTitle
        );
        $this->isID($parentID);

        $entryTitle = $this->generateRandomString();

        $entryID = $this->cmdbDialog->create(
            Category::CATG__MODEL,
            'title',
            $entryTitle,
            $parentTitle
        );
        $this->isID($entryID);

        $result = $this->cmdbDialog->read(Category::CATG__MODEL, 'title');
        $this->assertIsArray($result);
        $this->assertNotCount(0, $result);

        $entry = end($result);
        $this->isDialog($entry);

        $this->assertArrayHasKey('id', $entry);
        $this->isIDAsString($entry['id']);
        $this->assertSame($entryID, (int) $entry['id']);

        $this->assertArrayHasKey('title', $entry);
        $this->assertSame($entryTitle, $entry['title']);

        $this->assertArrayHasKey('parent', $entry);
        $this->assertIsArray($entry['parent']);

        $this->assertArrayHasKey('id', $entry['parent']);
        $this->isIDAsString($entry['parent']['id']);
        $this->assertSame($parentID, (int) $entry['parent']['id']);

        $this->assertArrayHasKey('title', $entry['parent']);
        $this->isOneLiner($entry['parent']['title']);
        $this->assertSame($parentTitle, $entry['parent']['title']);
    }

    /**
     * @group API-32
     * @throws Exception on error
     */
    public function testCreateWithParentIdentifier() {
        $parentTitle = $this->generateRandomString();

        $parentID = $this->cmdbDialog->create(
            Category::CATG__MODEL,
            'manufacturer',
            $parentTitle
        );
        $this->isID($parentID);

        $entryTitle = $this->generateRandomString();

        $entryID = $this->cmdbDialog->create(
            Category::CATG__MODEL,
            'title',
            $entryTitle,
            $parentID
        );
        $this->isID($entryID);

        $result = $this->cmdbDialog->read(Category::CATG__MODEL, 'title');
        $this->assertIsArray($result);
        $this->assertNotCount(0, $result);

        $entry = end($result);
        $this->isDialog($entry);

        $this->assertArrayHasKey('id', $entry);
        $this->isIDAsString($entry['id']);
        $this->assertSame($entryID, (int) $entry['id']);

        $this->assertArrayHasKey('title', $entry);
        $this->assertSame($entryTitle, $entry['title']);

        $this->assertArrayHasKey('parent', $entry);
        $this->assertIsArray($entry['parent']);

        $this->assertArrayHasKey('id', $entry['parent']);
        $this->isIDAsString($entry['parent']['id']);
        $this->assertSame($parentID, (int) $entry['parent']['id']);

        $this->assertArrayHasKey('title', $entry['parent']);
        $this->isOneLiner($entry['parent']['title']);
        $this->assertSame($parentTitle, $entry['parent']['title']);
    }

    /**
     * @group API-32
     * @throws Exception on error
     */
    public function testCreateWithParentIdentifierAsTitle() {
        $this->expectException(Exception::class);

        $parentTitle = $this->generateRandomString();

        $parentID = $this->cmdbDialog->create(
            Category::CATG__MODEL,
            'manufacturer',
            $parentTitle
        );
        $this->isID($parentID);

        $entryTitle = $this->generateRandomString();

        // This must fail because parent is unknown:
        $entryID = $this->cmdbDialog->create(
            Category::CATG__MODEL,
            'title',
            $entryTitle,
            "$parentID"
        );
        $this->isID($entryID);
    }

    /**
     * @group API-32
     * @throws Exception on error
     */
    public function testCreateWithUnknownParent() {
        $this->expectException(Exception::class);

        $entryTitle = $this->generateRandomString();

        // This must fail because parent is unknown:
        $entryID = $this->cmdbDialog->create(
            Category::CATG__MODEL,
            'title',
            $entryTitle,
            $this->generateRandomString()
        );
        $this->isID($entryID);
    }

    /**
     * @group API-32
     * @throws Exception on error
     */
    public function testCreateWithoutParent() {
        $value = $this->generateRandomString();

        $dialogID = $this->cmdbDialog->create(Category::CATG__MODEL, 'title', $value);
        $this->isID($dialogID);

        $result = $this->cmdbDialog->read(Category::CATG__MODEL, 'title');
        $this->assertIsArray($result);
        $this->assertNotCount(0, $result);

        $entry = end($result);
        $this->assertIsArray($entry);
        $this->isDialog($entry);
        $this->assertArrayHasKey('parent', $entry);
        $this->assertIsArray($entry['parent']);
        $this->assertArrayHasKey('id', $entry['parent']);
        $this->assertNull($entry['parent']['id']);
        $this->assertArrayHasKey('const', $entry['parent']);
        $this->assertEmpty($entry['parent']['const']);
        $this->assertArrayHasKey('title', $entry['parent']);
        $this->assertNull($entry['parent']['title']);
    }

//    /**
//     * @throws Exception on error
//     * @todo There must exist a custom category before the tests are running
//     * because it's not possible to create custom categories via API.
//     */
//    public function testCreateCustomMultiDialog() {
//        // Category "Firewall Rules":
//        $customCategoryConst = 'C__CATG__CUSTOM_FIELDS__FIREWALL_RULES';
//        // Attribute "IP Protocol":
//        $customAttributeKey = 'f_popup_c_1504172658823';
//        // Random transport protocol:
//        $customAttributeValue = $this->generateRandomString();
//
//        $result = $this->cmdbDialog->create(
//            $customCategoryConst,
//            $customAttributeKey,
//            $customAttributeValue
//        );
//
//        $this->assertIsInt($result);
//        $this->assertGreaterThanOrEqual(1, $result);
//
//        $values = $this->cmdbDialog->read(
//            $customCategoryConst,
//            $customAttributeKey
//        );
//
//        $this->assertIsArray($values);
//        $this->assertNotCount(0, $values);
//
//        // Look for new value:
//        $found = false;
//
//        foreach ($values as $value) {
//            $this->assertIsArray($value);
//            $this->assertArrayHasKey('id', $value);
//            $this->assertIsString($value['id']);
//            $this->assertArrayHasKey('title', $value);
//            $this->assertIsString($value['title']);
//
//            if ($value['title'] === $customAttributeValue) {
//                $found = true;
//                $id = (int) $value['id'];
//                $this->assertSame($result, $id);
//                break;
//            }
//        }
//
//        $this->assertTrue($found);
//    }

    /**
     * @throws Exception on error
     */
    public function testBatchCreate() {
        $result = $this->cmdbDialog->batchCreate([
            Category::CATG__CPU => [
                'manufacturer' => 'ACME Semiconductor, Inc.'
            ],
            Category::CATG__GLOBAL => [
                'category' => [
                    'cat 1',
                    'cat 2',
                    'cat 3'
                ],
                'purpose' => 'for reasons'
            ]
        ]);

        $this->assertIsArray($result);
        $this->assertCount(5, $result);

        foreach ($result as $entryID) {
            $this->assertIsInt($entryID);
            $this->assertGreaterThanOrEqual(1, $entryID);
        }
    }

    /**
     * @throws Exception on error
     */
    public function testRead() {
        $result = $this->cmdbDialog->read(
            Category::CATG__CPU,
            'manufacturer'
        );

        $this->assertIsArray($result);

        foreach ($result as $index => $entry) {
            $this->assertIsInt($index);
            $this->assertGreaterThanOrEqual(0, $index);

            $this->assertIsArray($entry);
            $this->isDialog($entry);
        }
    }

    /**
     * @throws Exception on error
     */
    public function testBatchRead() {
        $result = $this->cmdbDialog->batchRead([
            Category::CATG__GLOBAL => 'purpose',
            Category::CATG__MODEL => [
                'manufacturer',
                'model'
            ]
        ]);

        $this->assertIsArray($result);
        $this->assertNotCount(0, $result);
    }

    /**
     * @throws Exception on error
     */
    public function testDelete() {
        $entryTitle = $this->generateRandomString();

        $entryID = $this->cmdbDialog->create(
            Category::CATG__CPU,
            'manufacturer',
            $entryTitle
        );

        $this->isID($entryID);

        $result = $this->cmdbDialog->delete(
            Category::CATG__CPU,
            'manufacturer',
            $entryID
        );

        $this->assertInstanceOf(CMDBDialog::class, $result);

        // Verify
        $entries = $this->cmdbDialog->read(
            Category::CATG__CPU,
            'manufacturer'
        );

        foreach ($entries as $entry) {
            $this->isDialog($entry);

            $this->assertArrayHasKey('id', $entry);
            $this->isIDAsString($entry['id']);
            $this->assertNotSame($entryID, (int) $entry['id']);

            $this->assertArrayHasKey('title', $entry);
            $this->assertNotSame($entryTitle, $entry['title']);
        }
    }

}
