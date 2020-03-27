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

use bheisig\idoitapi\tests\Constants\Category;
use bheisig\idoitapi\tests\Constants\ObjectType;
use \Exception;
use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @group API-190
 * @see https://i-doit.atlassian.net/browse/API-190
 */
class API190Test extends BaseTest {

    const RACK_UNITS = 42;

    public function provideRackSettings(): array {
        return [
            'ascending at pos 1' => ['asc', 1, 1],
            'descending at pos 1' => ['desc', 1, 42],
            'ascending at pos ' . self::RACK_UNITS => ['asc', self::RACK_UNITS, 42],
            'descending at pos ' . self::RACK_UNITS => ['desc', self::RACK_UNITS, 1]
        ];
    }

    /**
     * @dataProvider provideRackSettings
     * @param string $sort Sort asc|desc
     * @param int $expectedPosition Position in rack
     * @param int $internalPosition Internal position stored in backend
     * @throws Exception on error
     */
    public function testIssue(string $sort, int $expectedPosition, int $internalPosition) {
        /**
         * Create test data:
         */

        $rackID = $this->useCMDBObject()->create(
            ObjectType::ENCLOSURE,
            $this->generateRandomString()
        );
        $this->isID($rackID);

        $rackFormFactorID = $this->useCMDBCategory()->save(
            $rackID,
            Category::CATG__FORMFACTOR,
            [
                'formfactor' => 1, // 19"
                'rackunits' => self::RACK_UNITS,
                'description' => $this->generateDescription()
            ]
        );
        $this->isID($rackFormFactorID);

        $rackSettingsID = $this->useCMDBCategory()->save(
            $rackID,
            Category::CATS__ENCLOSURE,
            [
                'slot_sorting' => $sort
            ]
        );
        $this->isID($rackSettingsID);

        $this->addObjectToLocation(
            $rackID,
            $this->getRootLocation()
        );

        $hostID = $this->createServer();
        $this->isID($hostID);

        $hostFormFactorID = $this->useCMDBCategory()->save(
            $hostID,
            Category::CATG__FORMFACTOR,
            [
                'formfactor' => 1, // 19"
                'rackunits' => 1,
                'description' => $this->generateDescription()
            ]
        );
        $this->isID($hostFormFactorID);

        $locationID = $this->useCMDBCategory()->save(
            $hostID,
            Category::CATG__LOCATION,
            [
                'parent' => $rackID,
                'option' => 3, // Horizontal
                'insertion' => 2, // Front/back
                'pos' => $expectedPosition,
                'description' => $this->generateDescription()
            ]
        );
        $this->isID($locationID);

        /**
         * Run tests:
         */

        $location = $this->useCMDBCategory()->read(
            $hostID,
            Category::CATG__LOCATION
        );

        $this->assertIsArray($location);
        $this->assertCount(1, $location);
        $this->assertArrayHasKey(0, $location);
        $this->assertIsArray($location[0]);
        $this->isCategoryEntry($location[0]);

        $this->assertArrayHasKey('pos', $location[0]);
        $this->assertIsArray($location[0]['pos']);

        $this->assertArrayHasKey('title', $location[0]['pos']);
        $this->assertSame($internalPosition, (int) $location[0]['pos']['title']);

        $this->assertArrayHasKey('obj_id', $location[0]['pos']);
        $this->assertSame($hostID, (int) $location[0]['pos']['obj_id']);

        // This failed in the past:
        $this->assertArrayHasKey('visually_from', $location[0]['pos']);
        $this->assertSame($expectedPosition, $location[0]['pos']['visually_from']);

        // …and this, too:
        $this->assertArrayHasKey('visually_to', $location[0]['pos']);
        $this->assertSame($expectedPosition, $location[0]['pos']['visually_to']);
    }

}
