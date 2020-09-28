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

namespace bheisig\idoitapi\Issues;

use \Exception;
use bheisig\idoitapi\BaseTest;
use bheisig\idoitapi\Constants\Category;
use bheisig\idoitapi\Constants\ObjectType;

/**
 * @group issues
 * @group ID-6996
 * @see https://i-doit.atlassian.net/browse/ID-6996
 */
class ID6996Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testWithLocation() {
        /**
         * Create test data:
         */

        $wanID = $this->useCMDBObject()->create(
            ObjectType::WAN,
            $this->generateRandomString()
        );
        $this->isID($wanID);

        $cityID = $this->useCMDBObject()->create(
            ObjectType::CITY,
            $this->generateRandomString()
        );
        $this->isID($cityID);

        $locationID = $this->addObjectToLocation($cityID, $this->getRootLocation());
        $this->isID($locationID);

        $entryID = $this->useCMDBCategory()->save(
            $wanID,
            Category::CATG__WAN,
            [
                'title' => $this->generateDescription(),
                'description' => $this->generateDescription(),
                'connection_location' => $cityID
            ]
        );
        $this->isID($entryID);

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
        $this->assertSame($entryID, (int) $entries[0]['id']);
        // This failed:
        $this->assertSame($wanID, (int) $entries[0]['objID']);
        $this->assertArrayHasKey('connection_location', $entries[0]);
        $this->assertIsArray($entries[0]['connection_location']);
        $this->isAssignedObject($entries[0]['connection_location']);
        $this->assertSame($cityID, (int) $entries[0]['connection_location']['id']);
    }

    /**
     * @throws Exception on error
     */
    public function testWithoutLocation() {
        /**
         * Create test data:
         */

        $wanID = $this->useCMDBObject()->create(
            ObjectType::WAN,
            $this->generateRandomString()
        );
        $this->isID($wanID);

        $entryID = $this->useCMDBCategory()->save(
            $wanID,
            Category::CATG__WAN,
            [
                'title' => $this->generateDescription(),
                'description' => $this->generateDescription()
            ]
        );
        $this->isID($entryID);

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
        $this->assertSame($entryID, (int) $entries[0]['id']);
        // This failed:
        $this->assertSame($wanID, (int) $entries[0]['objID']);
        $this->assertArrayHasKey('connection_location', $entries[0]);
        $this->assertNull($entries[0]['connection_location']);
    }

}
