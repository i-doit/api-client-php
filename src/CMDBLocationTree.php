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

namespace bheisig\idoitapi;

use \Exception;
use \RuntimeException;

/**
 * Requests for API namespace 'cmdb.location_tree'
 */
class CMDBLocationTree extends Request {

    /**
     * Reads objects located directly under an object
     *
     * This method does not run recursively. Use readRecursively() instead.
     *
     * @param int $objectID Object identifier
     * @param int $status Filter relations by status: 2 = normal, 3 = archived, 4 = deleted
     *
     * @return array
     *
     * @throws Exception on error
     */
    public function read(int $objectID, int $status = null): array {
        $params = [
            'id' => $objectID
        ];

        if (isset($status)) {
            $params['status'] = $status;
        }

        return $this->api->request(
            'cmdb.location_tree.read',
            $params
        );
    }

    /**
     * Reads recursively objects located under an object
     *
     * @param int $objectID Object identifier
     * @param int $status Filter relations by status: 2 = normal, 3 = archived, 4 = deleted
     *
     * @return array
     *
     * @throws Exception on error
     */
    public function readRecursively(int $objectID, int $status = null): array {
        $children = $this->read($objectID, $status);

        $tree = [];

        foreach ($children as $child) {
            if (!array_key_exists('id', $child)) {
                throw new RuntimeException('Broken result');
            }

            $node = $child;

            $childChildren = $this->readRecursively((int) $child['id'], $status);

            if (count($childChildren) > 0) {
                $node['children'] = $childChildren;
            }

            $tree[] = $node;
        }

        return $tree;
    }

}
