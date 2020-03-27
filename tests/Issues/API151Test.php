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
use \Exception;
use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @group API-151
 * @group API-187
 * @group API-188
 * @group ID-6994
 * @group ID-6998
 * @see https://i-doit.atlassian.net/browse/API-151
 * @see https://i-doit.atlassian.net/browse/API-187
 * @see https://i-doit.atlassian.net/browse/API-188
 * @see https://i-doit.atlassian.net/browse/ID-6994
 * @see https://i-doit.atlassian.net/browse/ID-6998
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

    const LENGTH_MM = 1;
    const LENGTH_CM = 2;
    const LENGTH_M = 4;
    const LENGTH_KM = 6;
    const LENGTH_INCH = 3;
    const LENGTH_FOOT = 5;

    const GRAD_CELSIUS = 1;
    const GRAD_FAHRENHEIT = 2;

    const POWER_WATT = 3;
    const POWER_KILOWATT = 2;
    const POWER_MEGAWATT = 4;
    const POWER_GIGAWATT = 5;
    const POWER_BTU = 1;

    const AIR_QUANTITY_CBM_PER_HOUR = 1;

    const TIME_PERIOD_SECONDS = 1;
    const TIME_PERIOD_MINUTES = 2;
    const TIME_PERIOD_HOURS = 3;
    const TIME_PERIOD_DAYS = 4;
    const TIME_PERIOD_MONTHS = 5;
    const TIME_PERIOD_YEARS = 6;

    const VOLUME_MILILITER = 1;
    const VOLUME_LITER = 2;

    const CAPACITY_BIT = 1;
    const CAPACITY_KILOBIT = 2;
    const CAPACITY_GIGABIT = 3;

    const VALUE_ATTRIBUTE = 'valueAttribute';
    const UNIT_ATTRIBUTE = 'unitAttribute';

    protected function getCategoriesWithCapacity(): array {
        $attributes = [
            [
                self::VALUE_ATTRIBUTE => 'capacity',
                self::UNIT_ATTRIBUTE => 'unit'
            ]
        ];

        return [
            Category::CATG__MEMORY => $attributes,
            Category::CATG__DRIVE => $attributes,
            Category::CATG__STORAGE_DEVICE => $attributes,
            Category::CATG__GRAPHIC => [
                [
                    self::VALUE_ATTRIBUTE => 'memory',
                    self::UNIT_ATTRIBUTE => 'unit'
                ]
            ],
            Category::CATG__DATABASE_SA => [
                [
                    self::VALUE_ATTRIBUTE => 'size',
                    self::UNIT_ATTRIBUTE => 'size_unit'
                ],
                [
                    self::VALUE_ATTRIBUTE => 'max_size',
                    self::UNIT_ATTRIBUTE => 'max_size_unit'
                ]
            ],
            Category::CATG__DATABASE_TABLE => [
                [
                    self::VALUE_ATTRIBUTE => 'size',
                    self::UNIT_ATTRIBUTE => 'size_unit'
                ],
                [
                    self::VALUE_ATTRIBUTE => 'max_size',
                    self::UNIT_ATTRIBUTE => 'max_size_unit'
                ],
                [
                    self::VALUE_ATTRIBUTE => 'schema_size',
                    self::UNIT_ATTRIBUTE => 'schema_size_unit'
                ]
            ],
            Category::CATG__LDEV_SERVER => $attributes,
            Category::CATG__COMPUTING_RESOURCES => [
                [
                    self::VALUE_ATTRIBUTE => 'disc_space',
                    self::UNIT_ATTRIBUTE => 'disc_space_unit'
                ]
            ]
        ];
    }

    protected function getCategoriesWithFrequency(): array {
        return [
            Category::CATG__CPU => [
                [
                    self::VALUE_ATTRIBUTE => 'frequency',
                    self::UNIT_ATTRIBUTE => 'frequency_unit'
                ]
            ],
            Category::CATG__COMPUTING_RESOURCES => [
                [
                    self::VALUE_ATTRIBUTE => 'cpu',
                    self::UNIT_ATTRIBUTE => 'cpu_unit'
                ]
            ]
        ];
    }

    protected function getCategoriesWithWeight(): array {
        return [
            Category::CATG__FORMFACTOR => [
                [
                    self::VALUE_ATTRIBUTE => 'weight',
                    self::UNIT_ATTRIBUTE => 'weight_unit'
                ]
            ]
        ];
    }

    protected function getCategoriesWithBandwidth(): array {
        return [
            Category::CATG__NETWORK_PORT => [
                [
                    self::VALUE_ATTRIBUTE => 'speed',
                    self::UNIT_ATTRIBUTE => 'speed_type'
                ]
            ],
            Category::CATG__CONTROLLER_FC_PORT => [
                [
                    self::VALUE_ATTRIBUTE => 'speed',
                    self::UNIT_ATTRIBUTE => 'speed_unit'
                ]
            ],
            Category::CATG__COMPUTING_RESOURCES => [
                [
                    self::VALUE_ATTRIBUTE => 'network_bandwidth',
                    self::UNIT_ATTRIBUTE => 'network_bandwidth_unit'
                ]
            ],
            Category::CATG__WAN => [
                [
                    self::VALUE_ATTRIBUTE => 'capacity_up',
                    self::UNIT_ATTRIBUTE => 'capacity_up_unit'
                ],
                [
                    self::VALUE_ATTRIBUTE => 'capacity_down',
                    self::UNIT_ATTRIBUTE => 'capacity_down_unit'
                ],
                [
                    self::VALUE_ATTRIBUTE => 'max_capacity_up',
                    self::UNIT_ATTRIBUTE => 'max_capacity_up_unit'
                ],
                [
                    self::VALUE_ATTRIBUTE => 'max_capacity_down',
                    self::UNIT_ATTRIBUTE => 'max_capacity_down_unit'
                ]
            ]
        ];
    }

    protected function getCategoriesWithLength(): array {
        return [
            Category::CATG__CABLE => [
                [
                    self::VALUE_ATTRIBUTE => 'length',
                    self::UNIT_ATTRIBUTE => 'length_unit'
                ]
            ],
            Category::CATS__MONITOR => [
                [
                    self::VALUE_ATTRIBUTE => 'size',
                    self::UNIT_ATTRIBUTE => 'size_unit'
                ]
            ]
        ];
    }

    protected function getCategoriesWithGrad(): array {
        return [
            Category::CATS__AC => [
                [
                    self::VALUE_ATTRIBUTE => 'threshold',
                    self::UNIT_ATTRIBUTE => 'threshold_unit'
                ]
            ]
        ];
    }

    protected function getCategoriesWithPower(): array {
        return [
            Category::CATS__AC => [
                [
                    self::VALUE_ATTRIBUTE => 'capacity',
                    self::UNIT_ATTRIBUTE => 'capacity_unit'
                ]
            ]
        ];
    }

    protected function getCategoriesWithAirQuantity(): array {
        return [
            Category::CATS__AC => [
                [
                    self::VALUE_ATTRIBUTE => 'air_quantity',
                    self::UNIT_ATTRIBUTE => 'air_quantity_unit'
                ]
            ]
        ];
    }

    protected function getCategoriesWithTimePeriod(): array {
        return [
            Category::CATS__EPS => [
                [
                    self::VALUE_ATTRIBUTE => 'warmup_time',
                    self::UNIT_ATTRIBUTE => 'warmup_time_unit'
                ],
                [
                    self::VALUE_ATTRIBUTE => 'autonomy_time',
                    self::UNIT_ATTRIBUTE => 'autonomy_time_unit'
                ]
            ],
            Category::CATS__UPS => [
                [
                    self::VALUE_ATTRIBUTE => 'charge_time',
                    self::UNIT_ATTRIBUTE => 'charge_time_unit'
                ],
                [
                    self::VALUE_ATTRIBUTE => 'autonomy_time',
                    self::UNIT_ATTRIBUTE => 'autonomy_time_unit'
                ]
            ]
        ];
    }

    protected function getCategoriesWithVolume(): array {
        return [
            Category::CATS__EPS => [
                [
                    self::VALUE_ATTRIBUTE => 'fuel_tank',
                    self::UNIT_ATTRIBUTE => 'volume_unit'
                ]
            ]
        ];
    }

    protected function getCategoriesWithBit(): array {
        return [
            Category::CATS__LAYER2_NET => [
                [
                    self::VALUE_ATTRIBUTE => 'vrf_capacity',
                    self::UNIT_ATTRIBUTE => 'vrf_capacity_unit'
                ]
            ]
        ];
    }

    protected function getCapacityUnits(): array {
        return [
            self::CAPACITY_BYTE => 'bytes',
            self::CAPACITY_KILOBYTE => 'kilobytes',
            self::CAPACITY_MEGABYTE => 'megabytes',
            self::CAPACITY_GIGABYTE => 'gigabytes',
            self::CAPACITY_TERABYTE => 'terabytes'
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

    protected function getLengthUnits(): array {
        return [
            self::LENGTH_MM => 'mm',
            self::LENGTH_CM => 'cm',
            self::LENGTH_M => 'm',
            self::LENGTH_INCH => 'inch',
            self::LENGTH_FOOT => 'foot'
        ];
    }

    protected function getGradUnits(): array {
        return [
            self::GRAD_CELSIUS => '°C',
            self::GRAD_FAHRENHEIT => '°F'
        ];
    }

    protected function getPowerUnits(): array {
        return [
            self::POWER_WATT => 'W',
            self::POWER_KILOWATT => 'KW',
            self::POWER_MEGAWATT => 'MW',
            self::POWER_GIGAWATT => 'GW',
            self::POWER_BTU => 'BTU'
        ];
    }

    protected function getAirQuantityUnits(): array {
        return [
            self::AIR_QUANTITY_CBM_PER_HOUR => 'cbm/h'
        ];
    }

    protected function getTimePeriodUnits(): array {
        return [
            self::TIME_PERIOD_SECONDS => 'seconds',
            self::TIME_PERIOD_MINUTES => 'minutes',
            self::TIME_PERIOD_HOURS => 'hours',
            self::TIME_PERIOD_DAYS => 'days',
            self::TIME_PERIOD_MONTHS => 'months',
            self::TIME_PERIOD_YEARS => 'years'
        ];
    }

    protected function getVolumeUnits(): array {
        return [
            self::VOLUME_MILILITER => 'ml',
            self::VOLUME_LITER => 'l'
        ];
    }

    protected function getBitUnits(): array {
        return [
            self::CAPACITY_BIT => 'Bits',
            self::CAPACITY_KILOBIT => 'KBits',
            self::CAPACITY_GIGABIT => 'GBits'
        ];
    }

    protected function provide(array $categories, array $units) {
        $provided = [];

        foreach ($categories as $categoryConstant => $attributes) {
            foreach ($attributes as $keyValuePair) {
                foreach ($units as $unit => $unitTitle) {
                    $value = $this->generatePositiveInteger();

                    $key = sprintf(
                        '%s::%s with %s %s',
                        $categoryConstant,
                        $keyValuePair[self::VALUE_ATTRIBUTE],
                        $value,
                        $unitTitle
                    );

                    $provided[$key] = [
                        $categoryConstant,
                        $value,
                        $unit,
                        $keyValuePair[self::VALUE_ATTRIBUTE],
                        $keyValuePair[self::UNIT_ATTRIBUTE]
                    ];
                }
            }
        }

        return $provided;
    }

    public function provideCapacities(): array {
        $categories = $this->getCategoriesWithCapacity();
        $units = $this->getCapacityUnits();
        return $this->provide($categories, $units);
    }

    public function provideFrequencies(): array {
        $categories = $this->getCategoriesWithFrequency();
        $units = $this->getFrequencyUnits();
        return $this->provide($categories, $units);
    }

    public function provideWeights(): array {
        $categories = $this->getCategoriesWithWeight();
        $units = $this->getWeightUnits();
        return $this->provide($categories, $units);
    }

    public function provideBandwidths(): array {
        $categories = $this->getCategoriesWithBandwidth();
        $units = $this->getBandwidthUnits();
        return $this->provide($categories, $units);
    }

    public function provideLengths(): array {
        $categories = $this->getCategoriesWithLength();
        $units = $this->getLengthUnits();
        return $this->provide($categories, $units);
    }

    public function provideGrads(): array {
        $categories = $this->getCategoriesWithGrad();
        $units = $this->getGradUnits();
        return $this->provide($categories, $units);
    }

    public function providePower(): array {
        $categories = $this->getCategoriesWithPower();
        $units = $this->getPowerUnits();
        return $this->provide($categories, $units);
    }

    public function provideAirQuantity(): array {
        $categories = $this->getCategoriesWithAirQuantity();
        $units = $this->getAirQuantityUnits();
        return $this->provide($categories, $units);
    }

    public function provideTimePeriod(): array {
        $categories = $this->getCategoriesWithTimePeriod();
        $units = $this->getTimePeriodUnits();
        return $this->provide($categories, $units);
    }

    public function provideVolume(): array {
        $categories = $this->getCategoriesWithVolume();
        $units = $this->getVolumeUnits();
        return $this->provide($categories, $units);
    }

    public function provideBit(): array {
        $categories = $this->getCategoriesWithBit();
        $units = $this->getBitUnits();
        return $this->provide($categories, $units);
    }

    protected function generatePositiveInteger(): int {
        return mt_rand(1, 20000);
    }

    /**
     * @dataProvider provideCapacities
     * @dataProvider provideFrequencies
     * @dataProvider provideWeights
     * @dataProvider provideBandwidths
     * @dataProvider provideLengths
     * @dataProvider provideGrads
     * @dataProvider providePower
     * @dataProvider provideAirQuantity
     * @dataProvider provideTimePeriod
     * @dataProvider provideVolume
     * @dataProvider provideBit
     * @param string $categoryConstant
     * @param float $value
     * @param int $unit
     * @param string $valueAttribute
     * @param string $unitAttribute
     * @throws Exception on error
     */
    public function testCreate(
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

        $entryID = $this->useCMDBCategory()->save(
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

        $entries = $this->useCMDBCategory()->read(
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

    /**
     * @dataProvider provideCapacities
     * @dataProvider provideFrequencies
     * @dataProvider provideWeights
     * @dataProvider provideBandwidths
     * @dataProvider provideLengths
     * @dataProvider provideGrads
     * @dataProvider providePower
     * @dataProvider provideAirQuantity
     * @dataProvider provideTimePeriod
     * @dataProvider provideVolume
     * @dataProvider provideBit
     * @param string $categoryConstant
     * @param float $value
     * @param int $unit
     * @param string $valueAttribute
     * @param string $unitAttribute
     * @throws Exception on error
     */
    public function testUpdate(
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

        $entryID = $this->useCMDBCategory()->save(
            $objectID,
            $categoryConstant,
            [
                $valueAttribute => $value,
                $unitAttribute => $unit
            ]
        );
        $this->isID($entryID);

        /**
         * Alter data:
         */

        $updatedID = $this->useCMDBCategory()->save(
            $objectID,
            $categoryConstant,
            [
                'description' => $this->generateDescription()
            ],
            $entryID
        );
        $this->assertSame($entryID, $updatedID);

        /**
         * Check data:
         */

        $entries = $this->useCMDBCategory()->read(
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
