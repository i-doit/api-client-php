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
use bheisig\idoitapi\Select;

class SelectTest extends TestCase {

    /**
     * @var \bheisig\idoitapi\API
     */
    protected $api;

    /**
     * @var \bheisig\idoitapi\Select
     */
    protected $instance;

    public function setUp() {
        $this->api = new API([
            'url' => $GLOBALS['url'],
            'key' => $GLOBALS['key'],
            'username' => $GLOBALS['username'],
            'password' => $GLOBALS['password']
        ]);

        $this->instance = new Select($this->api);
    }

    public function testFind() {
        $result = $this->instance->find('C__CATG__GLOBAL', 'title', 'ESXi1');

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);

        $result = $this->instance->find('C__CATG__MODEL', 'title', 'T-450s');

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);

        $result = $this->instance->find('C__CATG__IP', 'hostaddress', '8.8.8.8');

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);

        $result = $this->instance->find(
            'C__CATG__GLOBAL', 'title', 'This is not the object you are looking for'
        );

        $this->assertInternalType('array', $result);
        $this->assertCount(0, $result);
    }

}
