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

namespace Idoit\APIClient;

use \Exception;
use Idoit\APIClient\Constants\Category;

class CMDBConditionTest extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testReadObjectByCondition() {
        $objectID = $this->createServer();

        $attributes = [
            'inventory_no' => $this->generateRandomString(),
            'order_no'     => $this->generateRandomString(),
            'invoice_no'   => $this->generateRandomString()
        ];

        $entryID = $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__ACCOUNTING,
            $attributes
        );

        $this->assertIsInt($entryID);
        $this->isID($entryID);

        $entries = $this->useCMDBCategory()->read(
            $objectID,
            Category::CATG__ACCOUNTING
        );

        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);

        $entry = $entries[0];

        // Check attributes:
        foreach ($attributes as $attribute => $value) {
            $this->assertArrayHasKey($attribute, $entry);
            $this->assertIsString($entry[$attribute]);
            $this->assertSame($value, $entry[$attribute]);
        }

        $cmdbCondition = $this->useCMDBCondition();
        foreach ($attributes as $attribute => $value) {
            $conditions = [['property'   => "C__CATG__ACCOUNTING-".$attribute,
                            'comparison' => "=",
                            'value'      => $value,
                            ]];
            $objects = $cmdbCondition->read($conditions);
            $this->assertSame($objectID, intval($objects[0]['id']));
        }
    }

}
