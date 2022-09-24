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
 * @author    Benjamin Heisig <https://benjamin.heisig.name/>
 * @copyright Copyright (C) 2022 synetics GmbH
 * @copyright Copyright (C) 2016-2022 Benjamin Heisig
 * @license   http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link      https://github.com/i-doit/api-client-php
 */

declare(strict_types=1);

namespace Idoit\APIClient;

use \Exception;

/**
 * Conditional helper for more readable code
 */
class Condition
{

    /**
     * Operator: AND
     */
    const AND = 'AND';

    /**
     * Operator: URL
     */
    const OR = 'OR';

    public $property;

    public $comparison;

    public $value;

    public $operator;

    public function where($const, $property):self
    {
        $this->property = $const . "-" . $property;
        return $this;
    }

    public function andWhere($const, $property):self
    {
        $this->operator = self::AND;
        $this->where($const, $property);
        return $this;
    }

    public function orWhere($const, $property):self
    {
        $this->operator = self::OR;
        $this->where($const, $property);
        return $this;
    }

    public function isLike($value):self
    {
        $this->comparison = 'like';
        $this->value = $value;
        return $this;
    }

    public function isNotLike($value):self
    {
        $this->comparison = 'not like';
        $this->value = $value;
        return $this;
    }

    public function isEqualTo($value):self
    {
        $this->comparison = '=';
        $this->value = $value;
        return $this;
    }

    public function isNotEqualTo($value):self
    {
        $this->comparison = '!=';
        $this->value = $value;
        return $this;
    }

    public function isGreaterThan($value):self
    {
        $this->comparison = '>';
        $this->value = $value;
        return $this;
    }

    public function isGreaterOrEqaulThan($value):self
    {
        $this->comparison = '>=';
        $this->value = $value;
        return $this;
    }

    public function isLowerThan($value):self
    {
        $this->comparison = '<';
        $this->value = $value;
        return $this;
    }

    public function isLowerOrEaqualThan($value):self
    {
        $this->comparison = '<=';
        $this->value = $value;
        return $this;
    }

    public function isLowerOrGreaterThan($value):self
    {
        $this->comparison = '<>';
        $this->value = $value;
        return $this;
    }

    public function __construct($const = null, $property = null, $comparison = null, $value = null, $operator = null)
    {

        if (!is_null($const) && !is_null($property)) {
            $this->property = $const . "-" . $property;
        }

        $allowedComparison = ['=', '!=', 'like', 'not like', '>', '>=', '<', '<=', '<>'];
        if (!is_null($comparison) && !is_null($value) && in_array($comparison, $allowedComparison)) {
            $this->comparison = $comparison;
            $this->value = $value;
        }
        
        $allowedOperators = [self::AND, self::OR];
        if (!is_null($operator) && in_array(strtoupper($operator), $allowedOperators)) {
             $this->operator = strtoupper($operator);
        }
    }

    public function toArray(): array
    {

        $condition = [
            'property' => $this->property,
            'comparison' => $this->comparison,
            'value' => $this->value
        ];

        if (isset($this->operator)) {
            $condition['operator'] = $this->operator;
        }

        return $condition;
    }

}
