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
 * @group API-151
 * @see https://i-doit.atlassian.net/browse/API-151
 */
class API151Test extends BaseTest {

    const CAPACITY_BYTE = 1000;
    const CAPACITY_KILOBYTE = 1;
    const CAPACITY_MEGABYTE = 2;
    const CAPACITY_GIGABYTE = 3;
    const CAPACITY_TERABYTE = 4;

    const FREQUENCY_KILOHERTZ = 1;
    const FREQUENCY_MEGAHERTZ = 2;
    const FREQUENCY_GIGAHERTZ = 3;
    const FREQUENCY_TERAHERTZ = 4;

    const WEIGHT_GRAM = 1;
    const WEIGHT_KILOGRAM = 2;
    const WEIGHT_TON = 3;

    const BANDWIDTH_BIT_PER_SECOND = 1;
    const BANDWIDTH_KILOBIT_PER_SECOND = 2;
    const BANDWIDTH_MEGABIT_PER_SECOND = 3;
    const BANDWIDTH_GIGABIT_PER_SECOND = 4;

    protected function getCategoriesWithCapacity(): array {
        return [
            'C__CATG__MEMORY',
            'C__CATG__DRIVE'
        ];
    }

    protected function getCategoriesWithFrequency(): array {
        return [
            'C__CATG__CPU'
        ];
    }

    protected function getCategoriesWithWeight(): array {
        return [
            'C__CATG__FORMFACTOR'
        ];
    }

    protected function getCategoriesWithBandwidth(): array {
        return [
            'C__CATG__NETWORK_PORT'
        ];
    }

    protected function getCapacityUnits(): array {
        return [
            self::CAPACITY_BYTE => 'byte',
            self::CAPACITY_KILOBYTE => 'kilobyte',
            self::CAPACITY_MEGABYTE => 'megabyte',
            self::CAPACITY_GIGABYTE => 'gigabyte',
            self::CAPACITY_TERABYTE => 'terabyte'
        ];
    }

    protected function getFrequencyUnits(): array {
        return [
            self::FREQUENCY_KILOHERTZ => 'KHz',
            self::FREQUENCY_MEGAHERTZ => 'MHz',
            self::FREQUENCY_GIGAHERTZ => 'GHz',
            self::FREQUENCY_TERAHERTZ => 'THz'
        ];
    }

    protected function getWeightUnits(): array {
        return [
            self::WEIGHT_GRAM => 'g',
            self::WEIGHT_KILOGRAM => 'kg',
            self::WEIGHT_TON => 't'
        ];
    }

    protected function getBandwidthUnits(): array {
        return [
            self::BANDWIDTH_BIT_PER_SECOND => 'bit/s',
            self::BANDWIDTH_KILOBIT_PER_SECOND => 'kbit/s',
            self::BANDWIDTH_MEGABIT_PER_SECOND => 'mbit/s',
            self::BANDWIDTH_GIGABIT_PER_SECOND => 'gbit/s'
        ];
    }

    public function provideCapacities(): array {
        $providedCapacities = [];

        foreach ($this->getCategoriesWithCapacity() as $categoryConstant) {
            foreach ($this->getCapacityUnits() as $unit => $unitTitle) {
                $capacity = $this->generatePositiveInteger();

                $key = sprintf(
                    '%s with %s %ss',
                    $categoryConstant,
                    $capacity,
                    $unitTitle
                );

                $providedCapacities[$key] = [
                    $categoryConstant,
                    $capacity,
                    $unit,
                    'capacity',
                    'unit'
                ];
            }
        }

        return $providedCapacities;
    }

    public function provideFrequencies(): array {
        $providedFrequencies = [];

        foreach ($this->getCategoriesWithFrequency() as $categoryConstant) {
            foreach ($this->getFrequencyUnits() as $unit => $unitTitle) {
                $frequency = $this->generatePositiveInteger();

                $key = sprintf(
                    '%s with %s %s',
                    $categoryConstant,
                    $frequency,
                    $unitTitle
                );

                $providedFrequencies[$key] = [
                    $categoryConstant,
                    $frequency,
                    $unit,
                    'frequency',
                    'frequency_unit'
                ];
            }
        }

        return $providedFrequencies;
    }

    public function provideWeights(): array {
        $providedWeights = [];

        foreach ($this->getCategoriesWithWeight() as $categoryConstant) {
            foreach ($this->getWeightUnits() as $unit => $unitTitle) {
                $weight = $this->generatePositiveInteger();

                $key = sprintf(
                    '%s with %s %s',
                    $categoryConstant,
                    $weight,
                    $unitTitle
                );

                $providedWeights[$key] = [
                    $categoryConstant,
                    $weight,
                    $unit,
                    'weight',
                    'weight_unit'
                ];
            }
        }

        return $providedWeights;
    }

    public function provideBandwidths(): array {
        $providedBandwidths = [];

        foreach ($this->getCategoriesWithBandwidth() as $categoryConstant) {
            foreach ($this->getBandwidthUnits() as $unit => $unitTitle) {
                $bandwidth = $this->generatePositiveInteger();

                $key = sprintf(
                    '%s with %s %s',
                    $categoryConstant,
                    $bandwidth,
                    $unitTitle
                );

                $providedBandwidths[$key] = [
                    $categoryConstant,
                    $bandwidth,
                    $unit,
                    'speed',
                    'speed_type'
                ];
            }
        }

        return $providedBandwidths;
    }

    protected function generatePositiveInteger(): int {
        return mt_rand(1, 20000);
    }

    /**
     * @dataProvider provideCapacities
     * @dataProvider provideFrequencies
     * @dataProvider provideWeights
     * @dataProvider provideBandwidths
     * @param string $categoryConstant
     * @param float $value
     * @param int $unit
     * @param string $valueAttribute
     * @param string $unitAttribute
     * @throws Exception on error
     */
    public function testIssue(
        string $categoryConstant,
        float $value,
        int $unit,
        string $valueAttribute,
        string $unitAttribute
    ) {
        /**
         * Create test data:
         */

        $objectID = $this->createServer();
        $this->isID($objectID);

        $entryID = $this->cmdbCategory->save(
            $objectID,
            $categoryConstant,
            [
                $valueAttribute => $value,
                $unitAttribute => $unit
            ]
        );
        $this->isID($entryID);

        /**
         * Check data:
         */

        $entries = $this->cmdbCategory->read(
            $objectID,
            $categoryConstant
        );

        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->isCategoryEntry($entries[0]);
        $this->assertSame($entryID, (int) $entries[0]['id']);
        $this->assertSame($objectID, (int) $entries[0]['objID']);

        $this->assertArrayHasKey($valueAttribute, $entries[0]);
        $this->assertIsArray($entries[0][$valueAttribute]);
        $this->assertArrayHasKey('title', $entries[0][$valueAttribute]);

        $this->assertIsNotString($entries[0][$valueAttribute]['title']);
        switch (gettype($entries[0][$valueAttribute]['title'])) {
            case 'int':
                $this->assertSame((int) $value, (int) $entries[0][$valueAttribute]['title']);
                break;
            case 'float':
                $this->assertSame((float) $value, (float) $entries[0][$valueAttribute]['title']);
                break;
        }

        $this->assertArrayHasKey($unitAttribute, $entries[0]);
        $this->assertIsArray($entries[0][$unitAttribute]);
        $this->isDialog($entries[0][$unitAttribute]);
        $this->assertSame($unit, (int) $entries[0][$unitAttribute]['id']);
    }

}
