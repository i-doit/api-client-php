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
use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @group API-115
 * @group unreleased
 * @see https://i-doit.atlassian.net/browse/API-115
 */
class API115Test extends BaseTest {

    public function provideClientTypes(): array {
        return [
            'Other' => [1, 'Other', 'C__CLIENT_TYPE__OTHER'],
            'PDA' => [2, 'PDA', 'C__CLIENT_TYPE__PDA'],
            'PC' => [3, 'PC', 'C__CLIENT_TYPE__PC'],
            'Notebook' => [4, 'Notebook', 'C__CLIENT_TYPE__NOTEBOOK']
        ];
    }

    /**
     * @dataProvider provideClientTypes
     * @param int $identifier
     * @param string $title
     * @param string $constant
     * @throws Exception on error
     */
    public function testSelectByIdentifier(int $identifier, string $title, string $constant) {
        /**
         * Create test data:
         */

        $objectID = $this->cmdbObject->create(
            'C__OBJTYPE__CLIENT',
            $this->generateRandomString()
        );
        $this->isID($objectID);

        $entryID = $this->cmdbCategory->save(
            $objectID,
            'C__CATS__CLIENT',
            [
                'type' => $identifier
            ]
        );
        $this->isID($entryID);

        /**
         * Run tests:
         */

        $entries = $this->cmdbCategory->read(
            $objectID,
            'C__CATS__CLIENT'
        );

        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->isCategoryEntry($entries[0]);
        $this->assertSame($entryID, (int) $entries[0]['id']);
        $this->assertSame($objectID, (int) $entries[0]['objID']);

        $this->assertArrayHasKey('type', $entries[0]);
        $this->assertIsArray($entries[0]['type']);
        $this->isDialog($entries[0]['type']);

        $this->assertArrayHasKey('id', $entries[0]['type']);
        $this->assertArrayHasKey('title', $entries[0]['type']);
        $this->assertArrayHasKey('const', $entries[0]['type']);

        $this->assertSame($identifier, (int) $entries[0]['type']['id']);
        $this->assertSame($title, $entries[0]['type']['title']);
        $this->assertSame($constant, $entries[0]['type']['const']);
    }

    /**
     * @dataProvider provideClientTypes
     * @param int $identifier
     * @param string $title
     * @param string $constant
     * @throws Exception on error
     */
    public function testSelectByTitle(int $identifier, string $title, string $constant) {
        /**
         * Create test data:
         */

        $objectID = $this->cmdbObject->create(
            'C__OBJTYPE__CLIENT',
            $this->generateRandomString()
        );
        $this->isID($objectID);

        $entryID = $this->cmdbCategory->save(
            $objectID,
            'C__CATS__CLIENT',
            [
                'type' => $title
            ]
        );
        $this->isID($entryID);

        /**
         * Run tests:
         */

        $entries = $this->cmdbCategory->read(
            $objectID,
            'C__CATS__CLIENT'
        );

        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->isCategoryEntry($entries[0]);
        $this->assertSame($entryID, (int) $entries[0]['id']);
        $this->assertSame($objectID, (int) $entries[0]['objID']);

        $this->assertArrayHasKey('type', $entries[0]);
        $this->assertIsArray($entries[0]['type']);
        $this->isDialog($entries[0]['type']);

        $this->assertArrayHasKey('id', $entries[0]['type']);
        $this->assertArrayHasKey('title', $entries[0]['type']);
        $this->assertArrayHasKey('const', $entries[0]['type']);

        $this->assertSame($identifier, (int) $entries[0]['type']['id']);
        $this->assertSame($title, $entries[0]['type']['title']);
        $this->assertSame($constant, $entries[0]['type']['const']);
    }

    /**
     * @dataProvider provideClientTypes
     * @param int $identifier
     * @param string $title
     * @param string $constant
     * @throws Exception on error
     */
    public function testSelectByConstant(int $identifier, string $title, string $constant) {
        /**
         * Create test data:
         */

        $objectID = $this->cmdbObject->create(
            'C__OBJTYPE__CLIENT',
            $this->generateRandomString()
        );
        $this->isID($objectID);

        $entryID = $this->cmdbCategory->save(
            $objectID,
            'C__CATS__CLIENT',
            [
                'type' => $constant
            ]
        );
        $this->isID($entryID);

        /**
         * Run tests:
         */

        $entries = $this->cmdbCategory->read(
            $objectID,
            'C__CATS__CLIENT'
        );

        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->isCategoryEntry($entries[0]);
        $this->assertSame($entryID, (int) $entries[0]['id']);
        $this->assertSame($objectID, (int) $entries[0]['objID']);

        $this->assertArrayHasKey('type', $entries[0]);
        $this->assertIsArray($entries[0]['type']);
        $this->isDialog($entries[0]['type']);

        $this->assertArrayHasKey('id', $entries[0]['type']);
        $this->assertArrayHasKey('title', $entries[0]['type']);
        $this->assertArrayHasKey('const', $entries[0]['type']);

        $this->assertSame($identifier, (int) $entries[0]['type']['id']);
        $this->assertSame($title, $entries[0]['type']['title']);
        $this->assertSame($constant, $entries[0]['type']['const']);
    }

}
