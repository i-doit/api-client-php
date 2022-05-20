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
 * Requests for API namespace 'cmdb.object_type_groups'
 */
class CMDBObjectTypeGroups extends Request {

    const ORDER_BY_TITLE = 'title';
    const ORDER_BY_STATUS = 'status';
    const ORDER_BY_CONSTANT = 'constant';
    const ORDER_BY_ID = 'id';

    const SORT_ASCENDING = 'asc';
    const SORT_DESCENDING = 'desc';

    /**
     * Fetches object type groups
     *
     * @param string $orderBy Order by 'title', 'status', 'constant' or 'id' optionally; use class constants ORDER_*
     * @param string $sortDirection Sort ascending or descending optionally; use class constants SORT_*
     * @param int $limit Limit result set optionally
     *
     * @return array
     *
     * @throws Exception on error
     */
    public function read(string $orderBy = null, string $sortDirection = null, int $limit = null): array {
        $params = [];

        if (isset($orderBy)) {
            $params['order_by'] = $orderBy;
        }

        if (isset($sortDirection)) {
            $params['sort'] = $sortDirection;
        }

        if (isset($limit)) {
            $params['limit'] = $limit;
        }

        return $this->api->request(
            'cmdb.object_type_groups.read',
            $params
        );
    }

}
