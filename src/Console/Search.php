<?php

/**
 * Copyright (C) 2022 synetics GmbH
 * Copyright (C) 2016-2022 Benjamin Heisig
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
 * @copyright Copyright (C) 2022 synetics GmbH
 * @copyright Copyright (C) 2016-2022 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

declare(strict_types=1);

namespace bheisig\idoitapi\Console;

use \Exception;

/**
 * Requests for API namespace 'console.search'
 */
class Search extends Console {

    /**
     * Create search index from scratch
     *
     * @return array Output (one value per line)
     *
     * @throws Exception on error
     */
    public function createIndex(): array {
        return $this->execute(
            'console.search.index'
        );
    }

    /**
     * Update existing search index
     *
     * @return array Output (one value per line)
     *
     * @throws Exception on error
     */
    public function updateIndex(): array {
        return $this->execute(
            'console.search.index',
            [
                'update' => true
            ]
        );
    }

    /**
     * Search for query
     *
     * @param string $query Query
     *
     * @return array Output (one value per line)
     *
     * @throws Exception on error
     */
    public function query(string $query): array {
        return $this->execute(
            'console.search.query',
            [
                'searchString' => $query
            ]
        );
    }

}
