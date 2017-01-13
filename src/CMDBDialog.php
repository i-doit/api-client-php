<?php

/**
 * Copyright (C) 2016-17 Benjamin Heisig
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
 * @copyright Copyright (C) 2016-17 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

namespace bheisig\idoitapi;

/**
 * Requests for API namespace 'cmdb.dialog'
 */
class CMDBDialog extends Request {

    /**
     * Creates a new entry for a drop-down menu
     *
     * @param string $category Category constant
     * @param string $attribute Attribute
     * @param mixed $value Value
     *
     * @return int Entry identifier
     *
     * @throws \Exception on error
     */
    public function create($category, $attribute, $value) {
        $result = $this->api->request(
            'cmdb.dialog.create',
            [
                'category' => $category,
                'property' => $attribute,
                'value' => $value
            ]
        );

        if (!array_key_exists('id', $result) ||
            !is_numeric($result['id'])) {
            throw new \Exception('Bad result');
        }

        return (int) $result['id'];
    }

    /**
     * Creates one or more entries for a drow-down menu
     *
     * @param array $values Values: ['cat' => ['attr' => 'value', 'attr' => 'value'], 'cat' => ['attr' => 'value', 'attr' => 'value']]
     *
     * @return array List of entry identifiers
     *
     * @throws \Exception on error
     */
    public function batchCreate($values) {
        $requests = [];

        foreach ($values as $category => $keyValuePair) {
            foreach ($keyValuePair as $attribute => $value) {
                $requests[] = [
                    'method' => 'cmdb.dialog.create',
                    'params' => [
                        'category' => $category,
                        'property' => $attribute,
                        'value' => $value
                    ]
                ];
            }
        }

        $entryIDs = [];

        $entries = $this->api->batchRequest($requests);

        foreach ($entries as $entry) {
            if (!array_key_exists('id', $entry) ||
                !is_numeric($entry['id'])) {
                throw new \Exception('Bad result');
            }

            $entryIDs[] = (int) $entry['id'];
        }

        return $entryIDs;
    }

    /**
     * Fetches values from a specific drop-down menu
     *
     * @param string $category Category constant
     * @param string $attribute Attribute
     *
     * @return array Indexed array of associative arrays
     *
     * @throws \Exception on error
     */
    public function read($category, $attribute) {
        return $this->api->request(
            'cmdb.dialog.read',
            [
                'category' => $category,
                'property' => $attribute
            ]
        );
    }

    /**
     * Fetches values from one or more drop-down menus
     *
     * @param array $attributes Attributes: ['cat' => 'attr', 'cat' => 'attr']
     *
     * @return array Indexed array of associative arrays
     *
     * @throws \Exception on error
     */
    public function batchRead($attributes) {
        $requests = [];

        foreach ($attributes as $category => $attribute) {
            $requests[] = [
                'method' => 'cmdb.dialog.read',
                'params' => [
                    'category' => $category,
                    'property' => $attribute
                ]
            ];
        }

        return $this->api->batchRequest($requests);
    }

}
