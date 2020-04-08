<?php

/**
 * Copyright (C) 2016-2020 Benjamin Heisig
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
 * @copyright Copyright (C) 2016-2020 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

declare(strict_types=1);

namespace bheisig\idoitapi\tests\Issues;

use bheisig\idoitapi\Idoit;
use bheisig\idoitapi\tests\Constants\Category;
use \Exception;
use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @group API-149
 * @see https://i-doit.atlassian.net/browse/API-149
 */
class API149Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testSearchForMacAddressInAutoDeepMode() {
        /**
         * Create test data:
         */

        $macAddress = $this->generateMACAddress();

        $objectID = $this->createServer();
        $this->isID($objectID);

        $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__NETWORK_PORT,
            [
                'mac' => $macAddress
            ]
        );

        /**
         * Run tests:
         */

        $results = $this->useIdoit()->search(
            $macAddress,
            Idoit::AUTO_DEEP_SEARCH
        );

        $this->assertIsArray($results);
        $this->assertCount(1, $results);

        foreach ($results as $result) {
            $this->isSearchResult($result);
        }
    }

    /**
     * @throws Exception on error
     */
    public function testSearchForMacAddressInDeepMode() {
        /**
         * Create test data:
         */

        $macAddress = $this->generateMACAddress();

        $objectID = $this->createServer();
        $this->isID($objectID);

        $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__NETWORK_PORT,
            [
                'mac' => $macAddress
            ]
        );

        /**
         * Run tests:
         */

        $results = $this->useIdoit()->search(
            $macAddress,
            Idoit::DEEP_SEARCH
        );

        $this->assertIsArray($results);
        $this->assertCount(1, $results);

        foreach ($results as $result) {
            $this->isSearchResult($result);
        }
    }

    /**
     * @throws Exception on error
     */
    public function testSearchForMacAddressInNormalMode() {
        /**
         * Create test data:
         */

        $macAddress = $this->generateMACAddress();

        $objectID = $this->createServer();
        $this->isID($objectID);

        $this->useCMDBCategory()->save(
            $objectID,
            Category::CATG__NETWORK_PORT,
            [
                'mac' => $macAddress
            ]
        );

        /**
         * Run tests:
         */

        $results = $this->useIdoit()->search(
            $macAddress,
            Idoit::NORMAL_SEARCH
        );

        $this->assertIsArray($results);
        $this->assertCount(0, $results);
    }

}
