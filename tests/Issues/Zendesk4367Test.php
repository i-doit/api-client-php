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

use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @see https://i-doit.zendesk.com/agent/tickets/4367
 */
class Zendesk4367Test extends BaseTest {

    /**
     * @throws \Exception
     */
    public function testIssue() {
        $objectID = $this->cmdbObject->create('C__OBJTYPE__SERVER', 'My host');
        $applicationID = $this->cmdbObject->create('C__OBJTYPE__APPLICATION', 'My app');
        $versionID = $this->cmdbCategory->create($applicationID, 'C__CATG__VERSION', [
            'title' => '1.0.0'
        ]);

        // This failed because of SQL query error:
        $entryID = $this->cmdbCategory->create($objectID, 'C__CATG__APPLICATION', [
            'application' => $applicationID,
            'assigned_version' => $versionID
        ]);

        $this->assertIsInt($entryID);
        $this->assertGreaterThan(0, $entryID);
    }

}
