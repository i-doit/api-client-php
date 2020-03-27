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

use bheisig\idoitapi\tests\Constants\Category;
use \Exception;
use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @group API-127
 * @see https://i-doit.atlassian.net/browse/API-127
 */
class API127Test extends BaseTest {

    /**
     * @throws Exception
     */
    public function testIssue() {
        $objectID = $this->createServer();
        $entryID = $this->addIPv4($objectID);

        $result = $this->useCMDBCategory()->readOneByID($objectID, Category::CATG__IP, $entryID);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('ipv4_address', $result);
        $this->assertIsArray($result['ipv4_address']);
        // This failed because of an empty PHP array/JSON object:
        $this->assertArrayHasKey('id', $result['ipv4_address']);
        $this->assertArrayHasKey('type', $result['ipv4_address']);
        $this->assertArrayHasKey('title', $result['ipv4_address']);
        $this->assertArrayHasKey('sysid', $result['ipv4_address']);
        $this->assertArrayHasKey('ref_id', $result['ipv4_address']);
        $this->assertArrayHasKey('ref_title', $result['ipv4_address']);
        $this->assertArrayHasKey('ref_type', $result['ipv4_address']);
    }

}
