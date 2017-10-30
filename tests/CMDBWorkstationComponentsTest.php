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

use bheisig\idoitapi\CMDBWorkstationComponents;

class CMDBWorkstationComponentsTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\CMDBWorkstationComponents
     */
    protected $instance;

    public function setUp() {
        parent::setUp();

        $this->instance = new CMDBWorkstationComponents($this->api);
    }

    public function testRead() {
        $result = $this->instance->read(167);

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
    }

    public function testBatchRead() {
        $result = $this->instance->batchRead([167, 166]);

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
    }

    public function testReadByEMail() {
        $result = $this->instance->readByEMail('h.olo@acme-it.example');

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
    }

    public function testReadByEMails() {
        $result = $this->instance->readByEMails(['h.inbrunst@acme-it.example', 'h.olo@acme-it.example']);

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
    }

}
