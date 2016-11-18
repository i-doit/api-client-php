<?php

/**
 * Copyright (C) 2016 Benjamin Heisig
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
 * @copyright Copyright (C) 2016 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

namespace bheisig\idoitapi;

/**
 * Selector for objects
 */
class Select extends Request {

    /**
     * Find objects by attribute
     *
     * @param string $category
     * @param string $attribute
     * @param mixed $value
     *
     * @return int[] List of object identifiers
     *
     * @throws \Exception on error
     */
    public function find($category, $attribute, $value) {
        $cmdbObjects = new CMDBObjects($this->api);

        $objects = $cmdbObjects->read();

        $objectIDs = [];

        foreach ($objects as $object) {
            $objectIDs[] = (int) $object['id'];
        }

        unset($objects);

        $cmdbCategory = new CMDBCategory($this->api);

        $result = $cmdbCategory->batchRead(
            $objectIDs,
            $category
        );

        $objectIDs = [];

        foreach ($result as $categoryEntries) {
            foreach ($categoryEntries as $categoryEntry) {
                if (!array_key_exists($attribute, $categoryEntry)) {
                    continue;
                }

                // @todo This is way more complicated:
                if ($categoryEntry[$attribute] != $value) {
                    continue;
                }

                if (!array_key_exists('objID', $categoryEntry)) {
                    throw new \Exception('Found attribute for unknown object');
                }

                $objectIDs[] = (int) $categoryEntry['objID'];
            }
        }

        return $objectIDs;
    }

}
