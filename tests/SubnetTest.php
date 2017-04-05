<?php

/**
 * Copyright (C) 2016-17 Benjamin Heisig
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
 * @copyright Copyright (C) 2016-17 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

use PHPUnit\Framework\TestCase;
use bheisig\idoitapi\API;
use bheisig\idoitapi\Subnet;

class SubnetTest extends TestCase {

    /**
     * @var \bheisig\idoitapi\API
     */
    protected $api;

    /**
     * @var \bheisig\idoitapi\Subnet
     */
    protected $instance;

    public function setUp() {
        $this->api = new API([
            'url' => $GLOBALS['url'],
            'key' => $GLOBALS['key'],
            'username' => $GLOBALS['username'],
            'password' => $GLOBALS['password']
        ]);

        $this->instance = new Subnet($this->api);
    }

    public function testLoad() {
        // "Global v4"
        $result = $this->instance->load(20);

        $this->assertInstanceOf(Subnet::class, $result);
    }

    public function testHasNext() {
        // "Global v4"
        $result = $this->instance->load(20)->hasNext();

        $this->assertTrue($result);
    }

    public function testNext() {
        // "Global v4"
        $result = $this->instance->load(20)->next();

        $this->assertInternalType('string', $result);
    }

    public function testIsFree() {
        // "Global v4"
        $result = $this->instance->load(20)->isFree('123.234.111.222');

        $this->assertTrue($result);
    }

}
