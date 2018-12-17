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

namespace bheisig\idoitapi\tests\Issues;

use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @group API-76
 * @see https://i-doit.atlassian.net/browse/API-76
 */
class API76Test extends BaseTest {

    /**
     * @throws \Exception on error
     */
    public function testIssue() {
        $objectID = $this->createServer();
        $this->isID($objectID);

        $subnetID = $this->createSubnet();
        $this->isID($subnetID);

        $entryID = $this->addIPv4($objectID, $subnetID);
        $this->isID($entryID);

        $result = $this->cmdbCategory->readOneByID($objectID, 'C__CATG__IP', $entryID);
        $this->assertInternalType('array', $result);

        // This failed because these arrays share the same content:
        $this->assertArrayHasKey('ipv4_address', $result);
        $this->assertArrayNotHasKey('ipv6_address', $result);
    }

}
