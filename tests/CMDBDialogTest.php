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
use bheisig\idoitapi\CMDBDialog;

class CMDBDialogTest extends TestCase {

    /**
     * @var \bheisig\idoitapi\API
     */
    protected $api;

    /**
     * @var \bheisig\idoitapi\CMDBDialog
     */
    protected $instance;

    public function setUp() {
        $this->api = new API([
            'url' => $GLOBALS['url'],
            'key' => $GLOBALS['key'],
            'username' => $GLOBALS['username'],
            'password' => $GLOBALS['password']
        ]);

        $this->instance = new CMDBDialog($this->api);
    }

    public function testCreate() {
        $result = $this->instance->create(
            'C__CATG__CPU',
            'manufacturer',
            'ACME Semiconductor, Inc.'
        );

        $this->assertInternalType('int', $result);
        $this->assertGreaterThanOrEqual(1, $result);
    }

    public function testBatchCreate() {
        $result = $this->instance->batchCreate([
            'C__CATG__CPU' => [
                'manufacturer' => 'ACME Semiconductor, Inc.'
            ],
            'C__CATG__GLOBAL' => [
                'category' => [
                    'cat 1',
                    'cat 2',
                    'cat 3'
                ],
                'purpose' => 'API TEST'
            ]
        ]);

        $this->assertInternalType('array', $result);
        $this->assertCount(5, $result);

        foreach ($result as $entryID) {
            $this->assertInternalType('int', $entryID);
            $this->assertGreaterThanOrEqual(1, $entryID);
        }
    }

    public function testRead() {
        $result = $this->instance->read(
            'C__CATG__MODEL',
            'manufacturer'
        );

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
    }

    public function testBatchRead() {
        $result = $this->instance->batchRead([
            'C__CATG__GLOBAL' => 'purpose',
            'C__CATG__MODEL' => [
                'manufacturer',
                'model'
            ]
        ]);

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
    }

}
