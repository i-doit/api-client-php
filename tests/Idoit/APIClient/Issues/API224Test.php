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
 * @group API-224
 * @see https://i-doit.atlassian.net/browse/API-224
 */
class API224Test extends BaseTest {

    public function provideValues(): array {
        return [
            ['tag', ['tag0', 'tag1', 'tag2']]
        ];
    }

    /**
     * @param string $attribute Attribute name
     * @param array $values Values for a multi dialog+ attribute
     * @dataProvider provideValues
     * @throws Exception on error
     */
    public function testSaveCategoryEntry(string $attribute, array $values) {
        /**
         * Create test data:
         */

        $objectID = $this->createServer();
        $this->isID($objectID);

        // It's important to fill-out a dialog+ attribute:
        $entryID = $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__GLOBAL,
            [
                $attribute => $values
            ]
        );
        $this->isID($entryID);

        /**
         * Run tests:
         */

        // Just change another attribute from the same category.
        // This failed in the past because it removes all values from the attribute:
        $testEntryID = $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__GLOBAL,
            [
                'title' => $this->generateRandomString()
            ]
        );

        $this->assertSame($entryID, $testEntryID);

        /**
         * Double check:
         */

        $entries = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__GLOBAL
        );

        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);
        $this->assertIsArray($entries[0]);
        $this->isCategoryEntry($entries[0]);

        // Values should be still there…
        $this->assertArrayHasKey($attribute, $entries[0]);
        $this->assertCount(count($values), $entries[0][$attribute]);

        // …and still the same as before:
        foreach ($entries[0][$attribute] as $index => $value) {
            $this->assertIsArray($value);
            $this->isDialog($value);
            $this->assertSame($values[$index], $value['title']);
        }
    }

    /**
     * @param string $attribute Attribute name
     * @param array $values Values for a multi dialog+ attribute
     * @dataProvider provideValues
     * @throws Exception on error
     */
    public function testUpdateCategoryEntry(string $attribute, array $values) {
        /**
         * Create test data:
         */

        $objectID = $this->createServer();
        $this->isID($objectID);

        // It's important to fill-out a dialog+ attribute:
        $this->useCMDBCategory()->update(
            $objectID,
            Category::CATG__GLOBAL,
            [
                $attribute => $values
            ]
        );

        /**
         * Run tests:
         */

        // Just change another attribute from the same category.
        // This failed in the past because it removes all values from the attribute:
        $this->useCMDBCategory()->update(
            $objectID,
            Category::CATG__GLOBAL,
            [
                'title' => $this->generateRandomString()
            ]
        );

        /**
         * Double check:
         */

        $entries = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__GLOBAL
        );

        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);
        $this->assertIsArray($entries[0]);
        $this->isCategoryEntry($entries[0]);

        // Values should be still there…
        $this->assertArrayHasKey($attribute, $entries[0]);
        $this->assertCount(count($values), $entries[0][$attribute]);

        // …and still the same as before:
        foreach ($entries[0][$attribute] as $index => $value) {
            $this->assertIsArray($value);
            $this->isDialog($value);
            $this->assertSame($values[$index], $value['title']);
        }
    }

}
