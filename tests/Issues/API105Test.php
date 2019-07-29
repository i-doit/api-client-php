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

use \Exception;
use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @group API-105
 * @see https://i-doit.atlassian.net/browse/API-105
 */
class API105Test extends BaseTest {

    /**
     * @throws Exception on error
     */
    public function testIssue() {
        $hostID = $this->useCMDBObject()->create('C__OBJTYPE__SERVER', 'My little server');
        $osID = $this->useCMDBObject()->create('C__OBJTYPE__OPERATING_SYSTEM', 'My little os');

        // Install OS:

        $versionID = $this->useCMDBCategory()->create($osID, 'C__CATG__VERSION', [
            'title' => '1.0.0'
        ]);
        $variantID = $this->useCMDBCategory()->create($osID, 'C__CATS__APPLICATION_VARIANT', [
            'title' => 'home',
            'variant' => 'home'
        ]);
        $this->useCMDBCategory()->create($hostID, 'C__CATG__OPERATING_SYSTEM', [
            'application' => $osID,
            'assigned_version' => $versionID,
            'assigned_variant' => $variantID
        ]);
        $entries = $this->useCMDBCategory()->read($hostID, 'C__CATG__OPERATING_SYSTEM');

        $this->assertIsArray($entries);
        $this->assertArrayHasKey(0, $entries);
        $this->assertIsArray($entries[0]);

        $origEntry = $entries[0];
        $this->checkEntry($origEntry, $osID, $versionID, $variantID);

        // Update OS:

        $versionID = $this->useCMDBCategory()->create($osID, 'C__CATG__VERSION', [
            'title' => '2.0.0'
        ]);
        $variantID = $this->useCMDBCategory()->create($osID, 'C__CATS__APPLICATION_VARIANT', [
            'title' => 'enterprise',
            'variant' => 'enterprise'
        ]);
        $this->useCMDBCategory()->update($hostID, 'C__CATG__OPERATING_SYSTEM', [
            'application' => $osID,
            'assigned_version' => $versionID,
            'assigned_variant' => $variantID
        ]);
        $entries = $this->useCMDBCategory()->read($hostID, 'C__CATG__OPERATING_SYSTEM');

        $this->assertIsArray($entries);
        $this->assertArrayHasKey(0, $entries);
        $this->assertIsArray($entries[0]);

        $updatedEntry = $entries[0];
        $this->checkEntry($updatedEntry, $osID, $versionID, $variantID);

        $this->assertSame($origEntry['objID'], $updatedEntry['objID']);
        $this->assertSame($origEntry['id'], $updatedEntry['id']);
        $this->assertNotSame($origEntry['assigned_version']['ref_id'], $updatedEntry['assigned_version']['ref_id']);
        $this->assertNotSame($origEntry['assigned_variant']['ref_id'], $updatedEntry['assigned_variant']['ref_id']);
    }

    protected function checkEntry(array $entry, int $appID, int $versionID, int $variantID) {
        $this->assertArrayHasKey('id', $entry);
        $this->isIDAsString($entry['id']);

        $this->assertArrayHasKey('objID', $entry);
        $this->isIDAsString($entry['objID']);

        $this->assertArrayHasKey('application', $entry);
        $this->assertIsArray($entry['application']);
        $this->assertArrayHasKey('id', $entry['application']);
        $this->isIDAsString($entry['application']['id']);
        $id = (int) $entry['application']['id'];
        $this->assertSame($appID, $id);

        $this->assertArrayHasKey('assigned_version', $entry);
        $this->assertIsArray($entry['assigned_version']);
        $this->assertArrayHasKey('ref_id', $entry['assigned_version']);
        $this->isIDAsString($entry['assigned_version']['ref_id']);
        $refID = (int) $entry['assigned_version']['ref_id'];
        $this->assertSame($versionID, $refID);

        $this->assertArrayHasKey('assigned_variant', $entry);
        $this->assertIsArray($entry['assigned_variant']);
        $this->assertArrayHasKey('ref_id', $entry['assigned_variant']);
        $this->isIDAsString($entry['assigned_variant']['ref_id']);
        $refID = (int) $entry['assigned_variant']['ref_id'];
        $this->assertSame($variantID, $refID);
    }

}
