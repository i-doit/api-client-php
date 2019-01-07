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
use bheisig\idoitapi\CMDBCategoryInfo;

/**
 * @group issues
 * @group ID-934
 * @see https://i-doit.atlassian.net/browse/ID-934
 */
class ID934Test extends BaseTest {

    /**
     * @throws \Exception on error
     */
    public function testGetCategoryInfo() {
        $categoryConstants = [
            'C__CMDB__SUBCAT__NETWORK_PORT' => 'C__CATG__NETWORK_PORT',
            'C__CMDB__SUBCAT__NETWORK_INTERFACE_P' => 'C__CATG__NETWORK_INTERFACE',
            'C__CMDB__SUBCAT__NETWORK_INTERFACE_L' => 'C__CATG__NETWORK_LOG_PORT',
            'C__CMDB__SUBCAT__NETWORK_PORT_OVERVIEW' => 'C__CATG__NETWORK_PORT_OVERVIEW',
            'C__CMDB__SUBCAT__STORAGE__DEVICE' => 'C__CATG__STORAGE_DEVICE',
            'C__CMDB__SUBCAT__LICENCE_LIST' => 'C__CATS__LICENCE_LIST',
            'C__CMDB__SUBCAT__LICENCE_OVERVIEW' => 'C__CATS__LICENCE_OVERVIEW',
            'C__CMDB__SUBCAT__EMERGENCY_PLAN_LINKED_OBJECT_LIST' => 'C__CATS__EMERGENCY_PLAN_LINKED_OBJECTS',
            'C__CMDB__SUBCAT__EMERGENCY_PLAN' => 'C__CATS__EMERGENCY_PLAN_ATTRIBUTE',
            'C__CMDB__SUBCAT__WS_NET_TYPE' => 'C__CATS__WS_NET_TYPE',
            'C__CMDB__SUBCAT__WS_ASSIGNMENT' => 'C__CATS__WS_ASSIGNMENT',
            'C__CMDB__SUBCAT__FILE_OBJECTS' => 'C__CATS__FILE_OBJECTS',
            'C__CMDB__SUBCAT__FILE_VERSIONS' => 'C__CATS__FILE_VERSIONS',
            'C__CMDB__SUBCAT__FILE_ACTUAL' => 'C__CATS__FILE_ACTUAL',
        ];

        $cmdbCategoryInfo = new CMDBCategoryInfo($this->api);

        foreach ($categoryConstants as $oldConstant => $newConstant) {
            $resultOld = $cmdbCategoryInfo->read($newConstant);
            $this->assertIsArray(
                $resultOld,
                sprintf('Category "%s" has no result', $oldConstant)
            );
            $this->isValidCategoryInfo($oldConstant, $resultOld);

            $resultNew = $cmdbCategoryInfo->read($oldConstant);
            $this->assertIsArray(
                $resultNew,
                sprintf('Category "%s" has no result', $newConstant)
            );
            $this->isValidCategoryInfo($newConstant, $resultNew);
        }
    }

    protected function isValidCategoryInfo(string $categoryConstant, array $categoryInfo) {
        // There are some "view" categories in this list:
        // - C__CATG__NETWORK_PORT_OVERVIEW
        // - C__CATS__LICENCE_OVERVIEW
        // Ignore these categories for the following test:
        if (!in_array($categoryConstant, [
            'C__CATG__NETWORK_PORT_OVERVIEW',
            'C__CMDB__SUBCAT__NETWORK_PORT_OVERVIEW',
            'C__CATS__LICENCE_OVERVIEW',
            'C__CMDB__SUBCAT__LICENCE_OVERVIEW'
        ])) {
            $this->assertNotCount(
                0,
                $categoryInfo,
                sprintf('No information found for category "%s"', $categoryConstant)
            );
        }
    }

}
