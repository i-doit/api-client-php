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

use bheisig\idoitapi\tests\Constants\Category;
use bheisig\idoitapi\tests\Constants\ObjectType;
use \Exception;
use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @group API-194
 * @see https://i-doit.atlassian.net/browse/API-194
 */
class API194Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testIssue() {
        /**
         * Create test data:
         */

        $wanID = $this->useCMDBObject()->create(
            ObjectType::WAN,
            $this->generateRandomString()
        );
        $this->isID($wanID);

        $routerID = $this->useCMDBObject()->create(
            ObjectType::ROUTER,
            $this->generateRandomString()
        );
        $this->isID($routerID);

        $networkID = $this->useCMDBObject()->create(
            ObjectType::LAYER3_NET,
            $this->generateRandomString()
        );
        $this->isID($networkID);

        $wanConnectionID = $this->useCMDBCategory()->save(
            $wanID,
            Category::CATG__WAN,
            [
                'title' => $this->generateRandomString()
            ]
        );
        $this->isID($wanConnectionID);

        /**
         * Run tests:
         */

        $entries = $this->useCMDBCategory()->read(
            $wanID,
            Category::CATG__WAN
        );

        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);
        $this->assertIsArray($entries[0]);
        $this->isCategoryEntry($entries[0]);

        $this->assertArrayHasKey('router', $entries[0]);
        $this->assertNull($entries[0]['router']);

        $this->assertArrayHasKey('net', $entries[0]);
        $this->assertNull($entries[0]['net']);

        /**
         * Update test data:
         */

        $updatedWANConnectionID = $this->useCMDBCategory()->save(
            $wanID,
            Category::CATG__WAN,
            [
                'title' => $this->generateRandomString()
            ]
        );
        $this->assertSame($wanConnectionID, $updatedWANConnectionID);

        /**
         * Run tests again:
         */

        $entries = $this->useCMDBCategory()->read(
            $wanID,
            Category::CATG__WAN
        );

        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);
        $this->assertIsArray($entries[0]);
        $this->isCategoryEntry($entries[0]);

        $this->assertArrayHasKey('router', $entries[0]);
        $this->assertNull($entries[0]['router']);

        $this->assertArrayHasKey('net', $entries[0]);
        $this->assertNull($entries[0]['net']);


        /**
         * Update test data again:
         */

        $updatedWANConnectionID = $this->useCMDBCategory()->save(
            $wanID,
            Category::CATG__WAN,
            [
                'title' => $this->generateRandomString(),
                'router' => [$routerID],
                'net' => [$networkID]
            ]
        );
        $this->assertSame($wanConnectionID, $updatedWANConnectionID);

        /**
         * Run final tests:
         */

        $entries = $this->useCMDBCategory()->read(
            $wanID,
            Category::CATG__WAN
        );

        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertArrayHasKey(0, $entries);
        $this->assertIsArray($entries[0]);
        $this->isCategoryEntry($entries[0]);

        $this->assertArrayHasKey('router', $entries[0]);
        $this->assertIsArray($entries[0]['router']);
        $this->assertCount(1, $entries[0]['router']);
        $this->assertArrayHasKey(0, $entries[0]['router']);
        $this->assertIsArray($entries[0]['router'][0]);
        $this->isAssignedObject($entries[0]['router'][0]);
        $this->assertArrayHasKey('id', $entries[0]['router'][0]);
        $this->assertSame($routerID, (int) $entries[0]['router'][0]['id']);

        $this->assertArrayHasKey('net', $entries[0]);
        $this->assertIsArray($entries[0]['net']);
        $this->assertCount(1, $entries[0]['net']);
        $this->assertArrayHasKey(0, $entries[0]['net']);
        $this->assertIsArray($entries[0]['net'][0]);
        $this->isAssignedObject($entries[0]['net'][0]);
        $this->assertArrayHasKey('id', $entries[0]['net'][0]);
        $this->assertSame($networkID, (int) $entries[0]['net'][0]['id']);
    }

}
