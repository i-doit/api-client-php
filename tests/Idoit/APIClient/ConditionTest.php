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
 * @copyright Copyright (C) 2022 synetics GmbH
 * @copyright Copyright (C) 2016-2022 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/i-doit/api-client-php
 */

declare(strict_types=1);

namespace Idoit\APIClient;

use \Exception;
use Idoit\APIClient\Constants\Category;

class ConditionTest extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testConditionByConstructor() {

        $condition = [
            'property'   => "C__CATG__ACCOUNTING-inventory_no",
            'comparison' => "=",
            'value'      => "INV4711",
        ];

        $entry = (new Condition("C__CATG__ACCOUNTING", "inventory_no", "=", "INV4711"))->toArray();

        $this->assertIsArray($entry);
        $this->assertArrayHasKey('property', $entry);
        $this->assertArrayHasKey('comparison', $entry);
        $this->assertArrayHasKey('value', $entry);

        $this->assertIsString($entry['property']);
        $this->assertIsString($entry['comparison']);
        $this->assertIsString($entry['value']);

        $this->assertSame($condition, $entry);
    }

    /**
     * @throws Exception on error
     */
    public function testConditionByConstructorWithAndOperator() {

        $condition = [
            'property'   => "C__CATG__ACCOUNTING-inventory_no",
            'comparison' => "=",
            'value'      => "INV4711",
            'operator'   => "AND"
        ];

        $entry = (new Condition("C__CATG__ACCOUNTING", "inventory_no", "=", "INV4711", Condition::AND))->toArray();

        $this->assertIsArray($entry);
        $this->assertArrayHasKey('operator', $entry);
        $this->assertIsString($entry['operator']);

        $this->assertSame($condition, $entry);
    }

    /**
     * @throws Exception on error
     */
    public function testConditionByConstructorWithOrOperator() {

        $condition = [
            'property'   => "C__CATG__ACCOUNTING-inventory_no",
            'comparison' => "=",
            'value'      => "INV4711",
            'operator'   => "OR"
        ];

        $entry = (new Condition("C__CATG__ACCOUNTING", "inventory_no", "=", "INV4711", Condition::OR))->toArray();

        $this->assertIsArray($entry);
        $this->assertArrayHasKey('operator', $entry);
        $this->assertIsString($entry['operator']);

        $this->assertSame($condition, $entry);
    }

    /**
     * @throws Exception on error
     */
    public function testWhereIsLike() {

        $condition = [
            'property'   => "C__CATG__ACCOUNTING-inventory_no",
            'comparison' => "like",
            'value'      => "INV4711",
        ];

        $entry = (new Condition())->where("C__CATG__ACCOUNTING", "inventory_no")->isLike("INV4711");

        $this->assertSame($condition, $entry->toArray());
    }

    /**
     * @throws Exception on error
     */
    public function testWhereIsNotLike() {

        $condition = [
            'property'   => "C__CATG__ACCOUNTING-inventory_no",
            'comparison' => "not like",
            'value'      => "INV4711",
        ];

        $entry = (new Condition())->where("C__CATG__ACCOUNTING", "inventory_no")->isNotLike("INV4711");

        $this->assertSame($condition, $entry->toArray());
    }

    /**
     * @throws Exception on error
     */
    public function testWhereIsEqualTo() {

        $condition = [
            'property'   => "C__CATG__ACCOUNTING-inventory_no",
            'comparison' => "=",
            'value'      => "INV4711",
        ];

        $entry = (new Condition())->where("C__CATG__ACCOUNTING", "inventory_no")->isEqualTo("INV4711");

        $this->assertSame($condition, $entry->toArray());
    }

    /**
     * @throws Exception on error
     */
    public function testAndWhereIsEqualTo() {

        $condition = [
            'property'   => "C__CATG__ACCOUNTING-inventory_no",
            'comparison' => "=",
            'value'      => "INV4711",
            'operator'   => "AND"
        ];

        $entry = (new Condition())->andWhere("C__CATG__ACCOUNTING", "inventory_no")->isEqualTo("INV4711");

        $this->assertSame($condition, $entry->toArray());
    }

    /**
     * @throws Exception on error
     */
    public function testOrWhereIsEqualTo() {

        $condition = [
            'property'   => "C__CATG__ACCOUNTING-inventory_no",
            'comparison' => "=",
            'value'      => "INV4711",
            'operator'   => "OR"
        ];

        $entry = (new Condition())->orWhere("C__CATG__ACCOUNTING", "inventory_no")->isEqualTo("INV4711");

        $this->assertSame($condition, $entry->toArray());
    }

    /**
     * @throws Exception on error
     */
    public function testWhereIsNotEqualTo() {

        $condition = [
            'property'   => "C__CATG__ACCOUNTING-inventory_no",
            'comparison' => "!=",
            'value'      => "INV4711",
        ];

        $entry = (new Condition())->where("C__CATG__ACCOUNTING", "inventory_no")->isNotEqualTo("INV4711");

        $this->assertSame($condition, $entry->toArray());
    }

    /**
     * @throws Exception on error
     */
    public function testWhereIsGreaterThan() {

        $condition = [
            'property'   => "C__CATG__ACCOUNTING-inventory_no",
            'comparison' => ">",
            'value'      => "INV4711",
        ];

        $entry = (new Condition())->where("C__CATG__ACCOUNTING", "inventory_no")->isGreaterThan("INV4711");

        $this->assertSame($condition, $entry->toArray());
    }

    /**
     * @throws Exception on error
     */
    public function testWhereIsGreaterOrEqaulThan() {

        $condition = [
            'property'   => "C__CATG__ACCOUNTING-inventory_no",
            'comparison' => ">=",
            'value'      => "INV4711",
        ];

        $entry = (new Condition())->where("C__CATG__ACCOUNTING", "inventory_no")->isGreaterOrEqaulThan("INV4711");

        $this->assertSame($condition, $entry->toArray());
    }

    /**
     * @throws Exception on error
     */
    public function testWhereIsLowerThan() {

        $condition = [
            'property'   => "C__CATG__ACCOUNTING-inventory_no",
            'comparison' => "<",
            'value'      => "INV4711",
        ];

        $entry = (new Condition())->where("C__CATG__ACCOUNTING", "inventory_no")->isLowerThan("INV4711");

        $this->assertSame($condition, $entry->toArray());
    }

    /**
     * @throws Exception on error
     */
    public function testWhereIsLowerOrEaqualThan() {

        $condition = [
            'property'   => "C__CATG__ACCOUNTING-inventory_no",
            'comparison' => "<=",
            'value'      => "INV4711",
        ];

        $entry = (new Condition())->where("C__CATG__ACCOUNTING", "inventory_no")->isLowerOrEaqualThan("INV4711");

        $this->assertSame($condition, $entry->toArray());
    }

    /**
     * @throws Exception on error
     */
    public function testWhereIsLowerOrGreaterThan() {

        $condition = [
            'property'   => "C__CATG__ACCOUNTING-inventory_no",
            'comparison' => "<>",
            'value'      => "INV4711",
        ];

        $entry = (new Condition())->where("C__CATG__ACCOUNTING", "inventory_no")->isLowerOrGreaterThan("INV4711");

        $this->assertSame($condition, $entry->toArray());
    }

    /**
     * @throws Exception on error
     */
    public function testReadObjectByConditionWhereIsEqualToWithAndOperator() {
        $objectID = $this->createServer();

        $attributes = [
            'inventory_no' => $this->generateRandomString(),
            'order_no'     => $this->generateRandomString(),
            'invoice_no'   => $this->generateRandomString()
        ];

        $entryID = $this->useCMDBCategory()->save($objectID, Category::CATG__ACCOUNTING, $attributes);

        $cmdbCondition = $this->useCMDBCondition();
        $conditions = [];
        foreach ($attributes as $attribute => $value) {
            $condition = new Condition();
            $conditions[] = $condition->where("C__CATG__ACCOUNTING", $attribute, Condition::AND)->isEqualTo($value);
        }
        $objects = $cmdbCondition->read($conditions);
        $this->assertSame($objectID, intval($objects[0]['id']));
    }

    /**
     * @throws Exception on error
     */
    public function testReadObjectByConditionWhereIsEqualToWithOrOperator() {
        $objectID = $this->createServer();

        $attributes = [
            'inventory_no' => $this->generateRandomString(),
            'order_no'     => $this->generateRandomString(),
            'invoice_no'   => $this->generateRandomString()
        ];

        $entryID = $this->useCMDBCategory()->save($objectID, Category::CATG__ACCOUNTING, $attributes);

        $cmdbCondition = $this->useCMDBCondition();
        $conditions = [];
        foreach ($attributes as $attribute => $value) {
            $condition = new Condition();
            $conditions[] = $condition->where("C__CATG__ACCOUNTING", $attribute, Condition::OR)->isEqualTo($value);
        }
        $objects = $cmdbCondition->read($conditions);
        $this->assertSame($objectID, intval($objects[0]['id']));
    }

}
