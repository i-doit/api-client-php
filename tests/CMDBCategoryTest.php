<?php

/**
 * Copyright (C) 2016 Benjamin Heisig
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
 * @copyright Copyright (C) 2016 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

use PHPUnit\Framework\TestCase;
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBCategory;

class CMDBCategoryTest extends TestCase {

    /**
     * @var \bheisig\idoitapi\API
     */
    protected $api;

    /**
     * @var \bheisig\idoitapi\CMDBCategory
     */
    protected $category;

    public function setUp() {
        $this->api = new API([
            'url' => 'https://demo.i-doit.com/src/jsonrpc.php',
            'key' => 'c1ia5q',
            'username' => 'admin',
            'password' => 'admin'
        ]);

        $this->category = new CMDBCategory($this->api);
    }

    public function testCreate() {
        $entryID = $this->category->create(
            1000,
            'C__CATG__IP',
            [
                'net' => 632,
                'active' => false,
                'primary' => false,
                'net_type' => 1,
                'ipv4_assignment' => 2,
                "ipv4_address" =>  "10.20.10.100",
                'description' => 'API TEST'
            ]
        );

        $this->assertGreaterThanOrEqual(1, $entryID);
    }

    public function testRead() {
        $result = $this->category->read(
            1000,
            'C__CATG__MODEL'
        );

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
    }

    public function testUpdate() {
        $itself = $this->category->update(
            1000,
            'C__CATG__GLOBAL',
            [
                'cmdb_status' => 10
            ]
        );

        $this->assertInstanceOf(CMDBCategory::class, $itself);
    }

    public function testArchive() {
        $itself = $this->category->archive(
            1000,
            'C__CATG__CPU',
            3
        );

        $this->assertInstanceOf(CMDBCategory::class, $itself);
    }

    public function testDelete() {
        $itself = $this->category->delete(
            1000,
            'C__CATG__CPU',
            4
        );

        $this->assertInstanceOf(CMDBCategory::class, $itself);
    }

    public function testPurge() {
        $itself = $this->category->purge(
            1000,
            'C__CATG__ACCESS',
            3
        );

        $this->assertInstanceOf(CMDBCategory::class, $itself);
    }

    public function testBatchCreate() {
        $batchResult = $this->category->batchRead(
            [1000, 1005],
            ['C__CATG__FORMFACTOR', 'C__CATG__ACCOUNTING']
        );

        $this->assertInternalType('array', $batchResult);
        $this->assertCount(4, $batchResult);

        if (is_array($batchResult)) {
            foreach ($batchResult as $result) {
                $this->assertInternalType('array', $result);
                $this->assertNotCount(0, $result);
            }
        }
    }

    public function testBatchRead() {
        // @todo Implement me!
    }

    public function testBatchUpdate() {
        // @todo Implement me!
    }

    public function testBatchArchive() {
        // @todo Implement me!
    }

    public function testBatchDelete() {
        // @todo Implement me!
    }

    public function testBatchPurge() {
        // @todo Implement me!
    }

}
