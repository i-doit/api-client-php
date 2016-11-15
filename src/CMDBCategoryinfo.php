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
 * Requests for API namespace 'cmdb.category_info'
 */
class CMDBCategoryInfo extends Request {

    /**
     * Fetches information about a category
     *
     * @param string $categoryConst Category constant
     * @param bool $isGlobal (optional) Is category global, otherwise specific?
     *
     * @return array Result set
     *
     * @throws \Exception on error
     */
    public function read($categoryConst, $isGlobal = true) {
        $params = [];

        if ($isGlobal === true) {
            $params['catgID'] = $categoryConst;
        } else {
            $params['catsID'] = $categoryConst;
        } //if

        return $this->api->request(
            'cmdb.category_info',
            $params
        );
    } //function

    /**
     * Fetches information about one or more categories
     *
     * This only works with "global" categories.
     *
     * @param string[] $categories List of category constants
     *
     * @return array Indexed array of associative arrays
     *
     * @throws \Exception on error
     */
    public function batchRead($categories) {
        $requests = [];

        foreach ($categories as $category) {
            $requests[] = [
                'method' => 'cmdb.category_info',
                'params' => [
                    'catgID' => $category
                ]
            ];
        } //foreach

        return $this->api->batchRequest($requests);
    } //function

} //class
