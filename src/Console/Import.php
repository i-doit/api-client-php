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

namespace bheisig\idoitapi\Console;

use \Exception;

/**
 * Requests for API namespace 'console.import'
 */
class Import extends Console {

    /**
     * Import data from CSV file located on i-doit host
     *
     * @param array $options Options
     *
     * @return array Output (one value per line)
     *
     * @throws Exception on error
     */
    public function importFromCSVFile(array $options = []): array {
        return $this->execute(
            'console.import.csv',
            $options
        );
    }

    /**
     * List available CSV import profiles
     *
     * @return array Output (one value per line)
     *
     * @throws Exception on error
     */
    public function listCSVImportProfiles(): array {
        return $this->execute(
            'console.import.csvprofiles'
        );
    }

    /**
     * Import data from h-inventory output file located on i-doit host
     *
     * @param array $options Options
     *
     * @return array Output (one value per line)
     *
     * @throws Exception on error
     */
    public function importFromHInventory(array $options = []): array {
        return $this->execute(
            'console.import.hinventory',
            $options
        );
    }

    /**
     * Import data from JDisc Discovery instance
     *
     * @param array $options Options
     *
     * @return array Output (one value per line)
     *
     * @throws Exception on error
     */
    public function importFromJDiscDiscovery(array $options = []): array {
        return $this->execute(
            'console.import.jdisc',
            $options
        );
    }

    /**
     * Trigger discovery job on JDisc Discovery instance
     *
     * @param array $options Options
     *
     * @return array Output (one value per line)
     *
     * @throws Exception on error
     */
    public function triggerJDiscDiscovery(array $options = []): array {
        return $this->execute(
            'console.import.jdiscdiscovery',
            $options
        );
    }

    /**
     * Import data from OCS Inventory NG instance
     *
     * @param array $options Options
     *
     * @return array Output (one value per line)
     *
     * @throws Exception on error
     */
    public function importFromOCSInventoryNG(array $options = []): array {
        return $this->execute(
            'console.import.ocs',
            $options
        );
    }

    /**
     * Import data from syslog
     *
     * @param array $options Options
     *
     * @return array Output (one value per line)
     *
     * @throws Exception on error
     */
    public function importFromSyslog(array $options = []): array {
        return $this->execute(
            'console.import.syslog',
            $options
        );
    }

    /**
     * Import data from XML file located on i-doit host
     *
     * @param array $options Options
     *
     * @return array Output (one value per line)
     *
     * @throws Exception on error
     */
    public function importFromXMLFile(array $options = []): array {
        return $this->execute(
            'console.import.xml',
            $options
        );
    }

}
