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

declare(strict_types=1);

namespace bheisig\idoitapi\tests;

use bheisig\idoitapi\CMDBReports;

/**
 * @coversDefaultClass \bheisig\idoitapi\CMDBReports
 */
class CMDBReportsTest extends BaseTest {

    /**
     * @var \bheisig\idoitapi\CMDBReports
     */
    protected $instance;

    /**
     * @throws \Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->instance = new CMDBReports($this->api);
    }

    /**
     * @throws \Exception on error
     */
    public function testListReports() {
        $result = $this->instance->listReports();

        $this->assertInternalType('array', $result);
        $this->assertNotCount(0, $result);
    }

    /**
     * @throws \Exception on error
     */
    public function testRead() {
        $reports = $this->instance->listReports();

        foreach ($reports as $report) {
            $this->assertArrayHasKey('id', $report);

            $reportID = (int) $report['id'];

            $result = $this->instance->read($reportID);

            $this->assertInternalType('array', $result);
        }
    }

    /**
     * @throws \Exception on error
     */
    public function testBatchRead() {
        $reports = $this->instance->listReports();
        $reportIDs = [];

        foreach ($reports as $report) {
            $this->assertArrayHasKey('id', $report);

            $reportIDs[] = (int) $report['id'];
        }

        if (count($reportIDs) > 0) {
            $result = $this->instance->batchRead($reportIDs);

            $this->assertInternalType('array', $result);
        }
    }

}
