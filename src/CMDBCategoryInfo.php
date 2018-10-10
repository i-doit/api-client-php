<?php

/**
 * Copyright (C) 2016-18 Benjamin Heisig
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
 * @copyright Copyright (C) 2016-18 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

namespace bheisig\idoitapi;

/**
 * Requests for API namespace 'cmdb.category_info'
 */
class CMDBCategoryInfo extends Request {

    /**
     * Fetches information about a category
     *
     * @param string $categoryConst Category constant
     *
     * @return array Result set
     *
     * @throws \Exception on error
     */
    public function read($categoryConst) {
        return $this->api->request(
            'cmdb.category_info',
            array(
                'category' => $categoryConst
            )
        );
    }

    /**
     * Fetches information about one or more categories
     *
     * @param array $categories List of category constants as strings
     *
     * @return array Indexed array of associative arrays
     *
     * @throws \Exception on error
     */
    public function batchRead(array $categories) {
        $requests = [];

        foreach ($categories as $category) {
            $requests[] = [
                'method' => 'cmdb.category_info',
                'params' => array(
                    'category' => $category
                )
            ];
        }

        return $this->api->batchRequest($requests);
    }

    /**
     * Try to fetch information about all available categories
     *
     * Ignored:
     * * Custom categories
     * * Categories which are not assigned to any object types
     *
     * Notice: This method causes 3 API calls.
     *
     * @return array Indexed array of associative arrays
     *
     * @throws \Exception on error
     */
    public function readAll() {
        $cmdbObjectTypes = new CMDBObjectTypes($this->api);
        $cmdbObjectTypeCategories = new CMDBObjectTypeCategories($this->api);
        $categoryConsts = [];
        $objectTypes = $cmdbObjectTypes->read();
        $objectTypeIDs = array_map(function ($objectType) {
            return (int) $objectType['id'];
        }, $objectTypes);
        $objectTypeCategoriesBatch = $cmdbObjectTypeCategories->batchReadByID($objectTypeIDs);
        $catTypes = ['catg', 'cats'];

        foreach ($objectTypeCategoriesBatch as $objectTypeCategories) {
            foreach ($catTypes as $catType) {
                if (!array_key_exists($catType, $objectTypeCategories)) {
                    continue;
                }

                $more = array_map(function ($category) {
                    return $category['const'];
                }, $objectTypeCategories[$catType]);

                $categoryConsts = array_merge($categoryConsts, $more);
            }
        }

        $categoryConsts = array_unique($categoryConsts);

        $blacklistedCategoryConsts = $this->getVirtualCategoryConstants();
        $cleanCategoryConstants = [];

        foreach ($categoryConsts as $categoryConstant) {
            if (!in_array($categoryConstant, $blacklistedCategoryConsts)) {
                $cleanCategoryConstants[] = $categoryConstant;
            }
        }

        sort($cleanCategoryConstants);

        $categories = $this->batchRead($cleanCategoryConstants);

        return array_combine($cleanCategoryConstants, $categories);
    }

    /**
     * Get list of constants for virtual categories
     *
     * "Virtual" means these categories have no attributes at all.
     *
     * @return array Array of strings
     */
    public function getVirtualCategoryConstants() {
        return [
            'C__CATG__CABLING',
            'C__CATG__FLOORPLAN',
            'C__CATG__NET_ZONE',
            'C__CATG__NET_ZONE_SCOPES',
            'C__CATG__OBJECT',
            'C__CATG__OBJECT_VITALITY',
            'C__CATG__RACK_VIEW',
            'C__CATG__SANPOOL',
            'C__CATG__STACK_MEMBERSHIP',
            'C__CATG__STORAGE',
            'C__CATG__VIRTUAL_AUTH',
            'C__CATG__VIRTUAL_RELOCATE_CI',
            'C__CATG__VIRTUAL_SUPERNET',
            'C__CATG__VIRTUAL_TICKETS',
            'C__CATG__VRRP_VIEW',
            'C__CATS__CHASSIS_CABLING'
        ];
    }

}
