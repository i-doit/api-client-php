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
 * Requests for API namespace 'cmdb.reports'
 */
class CMDBReports extends Request {

    /**
     * Lists all reports
     *
     * @return array
     *
     * @throws \Exception on error
     */
    public function listReports() {
        return $this->api->request(
            'cmdb.reports'
        );
    } //function

    /**
     * Fetches the result of a report
     *
     * @param int $reportID Report identifier
     *
     * @return array
     *
     * @throws \Exception on error
     */
    public function read($reportID) {
        return $this->api->request(
            'cmdb.reports',
            [
                'id' => $reportID
            ]
        );
    } //function

    /**
     * Fetches the result of one or more reports
     *
     * @param int[] $reportIDs List of report identifiers
     *
     * @return array Indexed array of arrays
     *
     * @throws \Exception on error
     */
    public function batchRead(array $reportIDs) {
        $requests = [];

        foreach ($reportIDs as $reportID) {
            $requests[] = [
                'method' => 'cmdb.reports',
                'params' => [
                    'id' => $reportID
                ]
            ];
        } //foreach

        return $this->api->batchRequest($requests);
    } //function

} //class
