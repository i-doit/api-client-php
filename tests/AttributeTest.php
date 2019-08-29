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

namespace bheisig\idoitapi\tests;

use bheisig\idoitapi\tests\Constants\Category;
use bheisig\idoitapi\tests\Constants\ObjectType;
use \Exception;
use \DomainException;

//use bheisig\idoitapi\CMDBCategoryInfo;

/**
 * @group open
 * @todo These tests won't work at all. Instead create tests for each category explicitly!
 */
class AttributeTest extends BaseTest {

    /**
     * @var array
     */
    protected $virtualCategories = [];

    /**
     * @var array
     */
    protected $categories = [];

    /**
     * @var int
     */
    protected $objectID;

    /**
     * @throws Exception on error
     */
    public function setUp() {
        parent::setUp();

//        $cmdbCategoryInfo = new CMDBCategoryInfo($this->api);
//
//        $this->categories = $cmdbCategoryInfo->readAll();
//        $this->virtualCategories = $cmdbCategoryInfo->getVirtualCategoryConstants();
//
//        unset($cmdbCategoryInfo);

        $this->objectID = $this->createTestObject();
    }

    /**
     * @return int Object identifier
     *
     * @throws Exception on error
     */
    protected function createTestObject(): int {
        return $this->useCMDBObject()->create(
            ObjectType::GENERIC_TEMPLATE,
            $this->generateRandomString()
        );
    }

    /**
     * @return array
     */
    public function provideCategories(): array {
        $parameters = [];

        $blacklistedCategories = array_merge([
            Category::CATG__GLOBAL,
            Category::CATG__LOGBOOK
        ], $this->virtualCategories);

        foreach ($this->categories as $categoryConst => $attributes) {
            if (in_array($categoryConst, $blacklistedCategories)) {
                continue;
            }

            $parameters[$categoryConst] = [$categoryConst, $attributes];
        }

        return $parameters;
    }

    public function provideCategoryConstants(): array {
        $parameters = [];

        $blacklistedCategories = array_merge([
            Category::CATG__GLOBAL,
            Category::CATG__LOGBOOK
        ], $this->virtualCategories);

        // @todo For a quick win we test popular categories at first:
        $whitlistedCategories = [
            Category::CATG__MODEL,
            Category::CATG__ACCESS,
            Category::CATG__ACCOUNTING,
            //Category::CATG__IP,
            //'C__CMDB__SUBCAT__NETWORK_PORT'
        ];

        foreach (array_keys($this->categories) as $categoryConst => $attributes) {
            if (in_array($categoryConst, $blacklistedCategories)) {
                continue;
            }

            if (!in_array($categoryConst, $whitlistedCategories)) {
                continue;
            }

            $parameters[$categoryConst] = [$categoryConst];
        }

        return $parameters;
    }

    /**
     * @throws Exception on error
     * @param string $categoryConst Category constant
     * @dataProvider provideCategories
     */
    public function testReadEmptyCategories(string $categoryConst) {
//        $entry = $this->cmdbCategory->readFirst($this->objectID, $categoryConst);
//        $this->assertIsArray($entry);
//        $this->assertCount(0, $entry, sprintf(
//            'Entry found for object %s in category %s: %s%s',
//            $this->objectID,
//            $categoryConst,
//            PHP_EOL,
//            var_export($entry, true)
//        ));
    }

    /**
     * @throws Exception on error
     * @param string $categoryConst Category constant
     * @param array $attributes Attributes
     * @dataProvider provideCategories
     */
    public function testCreateCategoryEntries(string $categoryConst, array $attributes) {
//        $values = $this->generateValues($attributes);
//
//        $entryID = $this->cmdbCategory->create(
//            $this->objectID,
//            $categoryConst,
//            $values
//        );
//
//        $this->assertIsInt($entryID);
//        $this->assertGreaterThan(0, $entryID);
    }

    /**
     * @param array $attributes Attributes
     *
     * @return array Values
     *
     * @throws Exception on error
     */
    protected function generateValues(array $attributes): array {
        $values = [];

        foreach ($attributes as $attribute => $properties) {
            switch ($properties['info']['type']) {
                case 'dialog_plus':
                    $value = $this->generateRandomString();
                    break;
                case 'text':
                    if (array_key_exists('format', $properties) &&
                        !is_null($properties['format']) &&
                        array_key_exists('callback', $properties['format'])) {
                        if ($properties['format']['callback'][1] === 'exportIpReference') {
                            // IP address:
                            $value = $this->generateIPv4Address();
                        } elseif ($properties['format']['callback'][1] === 'exportHostname') {
                            // Hostname:
                            $value = substr($this->generateRandomString(), 0, 10);
                        } elseif ($properties['format']['callback'][1] === 'access_property_formatted_url') {
                            // URL:
                            $value = 'https://test.example.net/user/42/settings';
                        } elseif ($properties['format']['callback'][1] === 'get_guarantee_status') {
                            // Category "accounting":
                            $value = $this->generateRandomString();
                        } else {
                            throw new DomainException(sprintf(
                                'Unknown text format "%s" for attribute "%s" (%s)',
                                $properties['format']['callback'][0] . '::' . $properties['format']['callback'][1],
                                $properties['title'],
                                $attribute
                            ));
                        }
                    } else {
                        $value = $this->generateRandomString();
                    }
                    break;
                case 'commentary':
                    $value = $this->generateRandomString();
                    break;
                case 'dialog':
                    $value = $this->generateDialogValue($properties);
                    break;
                case 'date':
                    $value = $this->generateDate();
                    break;
                case 'object_browser':
                    $value = $this->generateObject($properties);
                    break;
                case 'money':
                    $value = $this->generateMoney();
                    break;
                case 'int':
                    $value = $this->generateRandomInteger();
                    break;
                default:
                    throw new DomainException(sprintf(
                        'Unknown type "%s" for attribute "%s" [%s]',
                        $properties['info']['type'],
                        $properties['title'],
                        $attribute
                    ));
            }

            $values[$attribute] = $value;
        }

        return $values;
    }

    /**
     * @param array $properties Information about attribute
     *
     * @return mixed
     *
     * @throws Exception on error
     */
    protected function generateDialogValue(array $properties) {
        $options = [];

        if ($properties['format']['callback'][1] === 'get_yes_or_no') {
            // Yes or no:
            $options = [
                0, // No
                1 // Yes
            ];
        } elseif (array_key_exists('p_strTable', $properties['ui']['params']) &&
            $properties['ui']['params']['p_strTable'] === 'isys_interval') {
            // Time interval:
            $options = [
                1, // Per day
                2, // Per week
                3, // Per month
                4, // Per year
            ];
        } elseif (array_key_exists('p_strTable', $properties['ui']['params']) &&
            $properties['ui']['params']['p_strTable'] === 'isys_guarantee_period_unit') {
            // Period:
            $options = [
                1, // Months
                2, // Days
                3, // Weeks
                4, // Years
            ];
        } elseif (array_key_exists('p_strTable', $properties['ui']['params']) &&
            $properties['ui']['params']['p_strTable'] === 'isys_net_type') {
            // IPv4/IPv6:
            $options = [
                1 // IPv4
                //2 @todo Support IPv6!
            ];
        } elseif (array_key_exists('p_arData', $properties['ui']['params']) &&
            is_array($properties['ui']['params']['p_arData']) &&
            count($properties['ui']['params']['p_arData']) > 0) {
            $options = array_keys($properties['ui']['params']['p_arData']);
        }

        if (count($options) === 0) {
            throw new DomainException(sprintf(
                'Unable to generate value for dialog attribute "%s"',
                $properties['title']
            ));
        }

        return $options[mt_rand(0, count($options) - 1)];
    }

    /**
     * @param array $properties Information about attribute
     *
     * @return int|array Object identifier(s) as integer(s)
     *
     * @throws Exception on error
     */
    protected function generateObject(array $properties) {
        switch ($properties['format']['callback'][1]) {
            case 'contact':
                $objectTypes = [ObjectType::PERSON, ObjectType::PERSON_GROUP, ObjectType::ORGANIZATION];
                $objectTypeConst = $objectTypes[array_rand($objectTypes)];
                break;
            default:
                throw new DomainException(sprintf(
                    'Unknown export helper "%s" for attribute "%s"',
                    $properties['format']['callback'][0] . '::' . $properties['format']['callback'][1],
                    $properties['title']
                ));
        }

        $objectID = $this->useCMDBObject()->create(
            $objectTypeConst,
            $this->generateRandomString()
        );

        if ($properties['ui']['params']['multiselection'] === true) {
            return [$objectID];
        }

        return $objectID;
    }

    protected function generateMoney(): float {
        return 1234.42;
    }

    protected function generateRandomInteger(): int {
        return 4;
    }

}
