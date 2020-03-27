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

namespace bheisig\idoitapi\tests\AttributeTypes;

use bheisig\idoitapi\tests\Constants\Category;
use \Exception;
use bheisig\idoitapi\tests\BaseTest;

class DialogPlusTest extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testCreateAndReferenceNewValue() {
        $value = $this->generateRandomString();
        $objectID = $this->createServer();

        $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__GLOBAL,
            [
                'purpose' => $value
            ]
        );

        // Make sure value is unique:
        $valueCounter = $this->countValue($value);
        $this->assertEquals(1, $valueCounter);

        $this->validateAttribute($objectID, $value);
    }

    /**
     * @throws Exception on error
     */
    public function testReferenceExistingValue() {
        $value = $this->generateRandomString();
        $objectID = $this->createServer();

        $dialogID = $this->useCMDBDialog()->create(
            Category::CATG__GLOBAL,
            'purpose',
            $value
        );

        // Make sure value is unique:
        $valueCounter = $this->countValue($value);
        $this->assertEquals(1, $valueCounter);

        $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__GLOBAL,
            [
                'purpose' => $value
            ]
        );

        $this->validateAttribute($objectID, $value);
        $this->validateIDAndValueCombination($objectID, $dialogID, $value);
    }

    /**
     * @throws Exception on error
     */
    public function testReferenceExistingIdentifier() {
        $value = $this->generateRandomString();
        $objectID = $this->createServer();

        $dialogID = $this->useCMDBDialog()->create(
            Category::CATG__GLOBAL,
            'purpose',
            $value
        );

        // Make sure value is unique:
        $valueCounter = $this->countValue($value);
        $this->assertEquals(1, $valueCounter);

        $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__GLOBAL,
            [
                'purpose' => $dialogID
            ]
        );

        $this->validateAttribute($objectID, $value);
        $this->validateIDAndValueCombination($objectID, $dialogID, $value);
    }

    /**
     * @throws Exception on error
     */
    public function testReferenceUnknownIdentifier() {
        $this->expectException(Exception::class);

        $objectID = $this->createServer();

        $dialogID = $this->generateRandomID();

        // Make sure ID does not exist:
        $idCounter = $this->countID($dialogID);
        $this->assertEquals(0, $idCounter);

        $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__GLOBAL,
            [
                'purpose' => $dialogID
            ]
        );
    }

    public function provideNumericStrings(): array {
        return [
            '1' => ['1'],
            '0' => ['0'],
            '-1' => ['-1'],
        ];
    }

    /**
     * @dataProvider provideNumericStrings
     * @param string $numericString
     * @throws Exception on error
     */
    public function testNumericStringsAsValues(string $numericString) {
        $objectID = $this->createServer();

        $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__GLOBAL,
            [
                'purpose' => $numericString
            ]
        );

        // Make sure value exists:
        $valueCounter = $this->countValue($numericString);
        $this->assertGreaterThanOrEqual(1, $valueCounter, $numericString);

        $this->validateAttribute($objectID, $numericString);
    }

    /**
     * @param string $value
     *
     * @return int
     *
     * @throws Exception on error
     */
    protected function countValue(string $value): int {
        $dialogEntries = $this->useCMDBDialog()->read(
            Category::CATG__GLOBAL,
            'purpose'
        );
        $this->assertIsArray($dialogEntries);

        $counter = 0;

        foreach ($dialogEntries as $dialogEntry) {
            $this->assertIsArray($dialogEntry);
            $this->assertArrayHasKey('title', $dialogEntry);
            $this->assertIsString($dialogEntry['title']);

            if ($dialogEntry['title'] === $value) {
                $counter++;
            }
        }

        return $counter;
    }

    /**
     * @param int $dialogID
     *
     * @return int
     *
     * @throws Exception on error
     */
    protected function countID(int $dialogID): int {
        $dialogEntries = $this->useCMDBDialog()->read(
            Category::CATG__GLOBAL,
            'purpose'
        );
        $this->assertIsArray($dialogEntries);

        $counter = 0;

        foreach ($dialogEntries as $dialogEntry) {
            $this->assertIsArray($dialogEntry);
            $this->assertArrayHasKey('title', $dialogEntry);
            $this->assertIsString($dialogEntry['id']);

            if ((int) $dialogEntry['id'] === $dialogID) {
                $counter++;
            }
        }

        return $counter;
    }

    /**
     * @param int $objectID
     * @param string $value
     *
     * @throws Exception on error
     */
    protected function validateAttribute(int $objectID, string $value) {
        $entry = $this->useCMDBCategory()->readFirst(
            $objectID,
            Category::CATG__GLOBAL
        );

        $this->assertArrayHasKey('purpose', $entry);
        $this->assertIsArray($entry['purpose']);
        $this->assertArrayHasKey('id', $entry['purpose']);
        $this->assertIsString($entry['purpose']['id']);
        $dialogID = (int) $entry['purpose']['id'];
        $this->assertGreaterThan(0, $dialogID);
        $this->assertArrayHasKey('title', $entry['purpose']);
        $this->assertIsString($entry['purpose']['title']);
        $this->assertSame($value, $entry['purpose']['title']);
    }

    /**
     * @param int $objectID
     * @param int $dialogID
     * @param string $value
     *
     * @throws Exception on error
     */
    protected function validateIDAndValueCombination(int $objectID, int $dialogID, string $value) {
        $entry = $this->useCMDBCategory()->readFirst(
            $objectID,
            Category::CATG__GLOBAL
        );

        $actualID = (int) $entry['purpose']['id'];
        $actualValue = $entry['purpose']['title'];

        $this->assertSame($dialogID, $actualID);
        $this->assertSame($value, $actualValue);
    }

}
