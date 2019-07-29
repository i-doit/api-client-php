<?php

/**
 * Copyright (C) 2016-19 Benjamin Heisig
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
 * @copyright Copyright (C) 2016-19 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

declare(strict_types=1);

namespace bheisig\idoitapi\tests\Issues;

use bheisig\idoitapi\tests\Constants\Category;
use \Exception;
use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @group API-140
 * @group ID-6259
 * @see https://i-doit.atlassian.net/browse/API-140
 * @see https://i-doit.atlassian.net/browse/ID-6259
 */
class API140Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testIssue() {
        $objectID = $this->createServer();
        $this->isID($objectID);
        $subnetID = $this->createSubnet();
        $this->isID($subnetID);
        $this->addIPv4($objectID, $subnetID);

        $amount = 1000;
        $categoryConsts = [];

        for ($index = 0; $index < $amount; $index++) {
            $categoryConsts[] = Category::CATG__IP;
        }

        // This was pretty slow:
        $results = $this->useCMDBCategory()->batchRead([$objectID], $categoryConsts);

        $this->assertIsArray($results);
        $this->assertCount($amount, $results);

        foreach ($results as $result) {
            $this->assertIsArray($result);
            $this->assertCount(1, $result);
        }
    }

}
