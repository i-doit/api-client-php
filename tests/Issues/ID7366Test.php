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
 * @group unreleased
 * @group issues
 * @group ID-7366
 * @see https://i-doit.atlassian.net/browse/ID-7366
 */
class ID7366Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testAssignSingleObject() {
        $this->markTestSkipped(
            'Custom category needed!'
        );

        /**
         * Prepare test environment:
         */

        // Category "ID-7366", single value, assigned to "Server":
        $customCategoryConst = 'C__CATG__CUSTOM_FIELDS_ID_7366';

        // Dialog+ attribute "Select":
        $customAttributeKey = 'f_popup_c_1584542930226';

        // Value in attribute:
        $customAttributeValue = 'First value';
        $customAttributeConst = 'FIRST_VALUE';

        /**
         * Create test data:
         */

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->useCMDBCategory()->save(
            $objectID,
            $customCategoryConst,
            [
                $customAttributeKey => $customAttributeConst
            ]
        );
        $this->isID($entryID);

        /**
         * Run tests:
         */

        $entries = $this->useCMDBCategory()->read(
            $objectID,
            $customCategoryConst
        );

        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);
        $this->assertIsArray($entries[0]);
        $this->assertArrayHasKey($customAttributeKey, $entries[0]);
        $this->assertIsArray($entries[0][$customAttributeKey]);
        $this->isDialog($entries[0][$customAttributeKey]);
        $this->assertSame($customAttributeConst, $entries[0][$customAttributeKey]['const']);
        $this->assertSame($customAttributeValue, $entries[0][$customAttributeKey]['title']);
    }

}
