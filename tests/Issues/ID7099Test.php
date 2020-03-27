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

namespace bheisig\idoitapi\tests\Issues;

use bheisig\idoitapi\tests\Constants\ObjectType;
use \Exception;
use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @group ID-7099
 * @see https://i-doit.atlassian.net/browse/ID-7099
 */
class ID7099Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testCustomCategory() {
        $categoryConstant = 'C__CATG__CUSTOM_FIELDS_ID_7099';
        $attributeID = 'f_text_c_1570460865700';
        $assignedObjectType = ObjectType::SERVER;

        $this->markTestSkipped(
            'Custom category needed!'
        );

        /**
         * Create test data:
         */

        $result = $this->useCMDBObject()->createWithCategories(
            $assignedObjectType,
            $this->generateRandomString(),
            [
                $categoryConstant => [
                    [
                        $attributeID => $this->generateRandomString()
                    ]
                ]
            ]
        );

        /**
         * Run tests:
         */

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->isID($result['id']);
        $this->assertArrayHasKey('message', $result);
        $this->assertIsString($result['message']);
        $this->assertArrayHasKey('success', $result);
        $this->assertIsBool($result['success']);
        $this->assertSame(true, $result['success']);

        $this->assertArrayHasKey('categories', $result);
        $this->assertIsArray($result['categories']);
        // This failed in the past:
        $this->assertCount(1, $result['categories']);
        $this->assertArrayHasKey($categoryConstant, $result['categories']);
        $this->assertIsArray($result['categories'][$categoryConstant]);
        $this->assertCount(1, $result['categories'][$categoryConstant]);
        $this->assertArrayHasKey(0, $result['categories'][$categoryConstant]);
        $this->isID($result['categories'][$categoryConstant][0]);
    }

}
