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

namespace bheisig\idoitapi;

use \Exception;

/**
 * Requests for API namespace 'cmdb.status'
 */
class CMDBStatus extends Request {

    const ATTRIBUTE_ID = 'id';
    const ATTRIBUTE_TITLE = 'title';
    const ATTRIBUTE_CONSTANT = 'constant';
    const ATTRIBUTE_COLOR = 'color';
    const ATTRIBUTE_EDITABLE = 'editable';

    /**
     * Get list of available CMDB states
     *
     * @return array Indexed array of associative arrays
     *
     * @throws Exception on error
     */
    public function read(): array {
        return $this->api->request(
            'cmdb.status.read'
        );
    }

    /**
     * Create new or update existing CMDB status
     *
     * @param string $title Title
     * @param string $constant Constant
     * @param string $color
     * @param int $identifier Set identifier to update existing status, otherwise a new one will be created
     *
     * @return int
     *
     * @throws Exception on error
     */
    public function save(string $title, string $constant, string $color, int $identifier = null): int {
        $params = [
            self::ATTRIBUTE_TITLE => $title,
            self::ATTRIBUTE_CONSTANT => $constant,
            self::ATTRIBUTE_COLOR => $color
        ];

        if (isset($identifier)) {
            $params[self::ATTRIBUTE_ID] = $identifier;
        }

        $result = $this->api->request(
            'cmdb.status.save',
            $params
        );

        return $this->requireSuccessFor($result);
    }

    /**
     * Purge CMDB status from database
     *
     * @param int $statusID Identifier
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function delete(int $statusID): self {
        $result = $this->api->request(
            'cmdb.status.delete',
            [
                'id' => $statusID
            ]
        );

        $this->requireSuccessWithoutIdentifier($result);

        return $this;
    }

}
