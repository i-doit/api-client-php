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

namespace bheisig\idoitapi;

use \Exception;
use \RuntimeException;

/**
 * Requests for API namespace 'cmdb.dialog'
 */
class CMDBDialog extends Request {

    /**
     * Create a new entry for a drop-down menu
     *
     * @param string $category Category constant
     * @param string $attribute Attribute
     * @param mixed $value Value
     * @param string|integer $parent Reference parent entry by its title (string) or by its identifier (integer)
     *
     * @return int Entry identifier
     *
     * @throws Exception on error
     */
    public function create($category, $attribute, $value, $parent = null) {
        $params = [
            'category' => $category,
            'property' => $attribute,
            'value' => $value
        ];

        if (isset($parent)) {
            $params['parent'] = $parent;
        }

        $result = $this->api->request(
            'cmdb.dialog.create',
            $params
        );

        if (!array_key_exists('entry_id', $result) ||
            !is_numeric($result['entry_id'])) {
            throw new RuntimeException('Bad result');
        }

        return (int) $result['entry_id'];
    }

    /**
     * Create one or more entries for a drow-down menu
     *
     * @param array $values Values:
     *  [
     *      'cat' => [
     *          'attr' => 'value', 'attr' => 'value'
     *      ],
     *      'cat' => [
     *         'attr' => ['value 1', 'value 2']
     *      ]
     * ]
     *
     * @return array List of entry identifiers
     *
     * @throws Exception on error
     *
     * @todo Add support for parameter "parent"!
     */
    public function batchCreate(array $values) {
        $requests = [];

        foreach ($values as $category => $keyValuePair) {
            foreach ($keyValuePair as $attribute => $mixed) {
                $values = [];

                if (is_array($mixed)) {
                    $values = $mixed;
                } else {
                    $values[] = $mixed;
                }

                foreach ($values as $value) {
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
        }

        $entryIDs = [];

        $entries = $this->api->batchRequest($requests);

        foreach ($entries as $entry) {
            if (!array_key_exists('entry_id', $entry) ||
                !is_numeric($entry['entry_id'])) {
                throw new RuntimeException('Bad result');
            }

            $entryIDs[] = (int) $entry['entry_id'];
        }

        return $entryIDs;
    }

    /**
     * Fetch values from drop-down menu
     *
     * @param string $category Category constant
     * @param string $attribute Attribute
     *
     * @return array Indexed array of associative arrays
     *
     * @throws Exception on error
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
     * Fetch values from one or more drop-down menus
     *
     * @param array $attributes Attributes: ['cat' => 'attr', 'cat' => ['attr 1', 'attr 2']]
     *
     * @return array Indexed array of associative arrays
     *
     * @throws Exception on error
     */
    public function batchRead(array $attributes) {
        $requests = [];

        foreach ($attributes as $category => $mixed) {
            $attributes = [];

            if (is_array($mixed)) {
                $attributes = $mixed;
            } else {
                $attributes[] = $mixed;
            }

            foreach ($attributes as $attribute) {
                $requests[] = [
                    'method' => 'cmdb.dialog.read',
                    'params' => [
                        'category' => $category,
                        'property' => $attribute
                    ]
                ];
            }
        }

        return $this->api->batchRequest($requests);
    }

    /**
     * Purge value from drop-down menu
     *
     * @param string $category Category constant
     * @param string $attribute Attribute
     * @param int $entryID Entry identifier
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function delete($category, $attribute, $entryID) {
        $this->api->request(
            'cmdb.dialog.delete',
            [
                'category' => $category,
                'property' => $attribute,
                'entry_id' => $entryID
            ]
        );

        return $this;
    }

}
