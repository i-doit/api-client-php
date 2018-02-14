<?php

/**
 * Copyright (C) 2016-18 Benjamin Heisig
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
 * @copyright Copyright (C) 2016-18 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

use bheisig\idoitapi\CMDBDialog;

class CMDBDialogTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\CMDBDialog
     */
    protected $instance;

    /**
     * @throws \Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->instance = new CMDBDialog($this->api);
    }

    /**
     * @throws \Exception on error
     */
    public function testCreate() {
        $result = $this->instance->create(
            'C__CATG__CPU',
            'manufacturer',
            'ACME Semiconductor, Inc.'
        );

        $this->assertInternalType('int', $result);
        $this->assertGreaterThanOrEqual(1, $result);
    }

    /**
     * @throws \Exception on error
     */
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
                'purpose' => 'for reasons'
            ]
        ]);

        $this->assertInternalType('array', $result);
        $this->assertCount(5, $result);

        foreach ($result as $entryID) {
            $this->assertInternalType('int', $entryID);
            $this->assertGreaterThanOrEqual(1, $entryID);
        }
    }

    /**
     * @throws \Exception on error
     */
    public function testRead() {
        $result = $this->instance->read(
            'C__CATG__MODEL',
            'manufacturer'
        );

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
    }

    /**
     * @throws \Exception on error
     */
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
