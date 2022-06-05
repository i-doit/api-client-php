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
 * @link https://github.com/i-doit/api-client-php
 */

declare(strict_types=1);

namespace Idoit\APIClient;

use \Exception;
use \BadMethodCallException;
use \RuntimeException;

/**
 * Special methods for subnets
 */
class Subnet extends Request {

    /**
     * List of used IP addresses
     *
     * @var array List of strings
     */
    protected $taken = [];

    /**
     * Current IP address as long integer
     *
     * @var int|null
     */
    protected $current;

    /**
     * First IP address in subnet as long integer
     *
     * @var int
     */
    protected $first;

    /**
     * Last IP address in subnet as long integer
     *
     * @var int
     */
    protected $last;

    /**
     * Fetches some information about subnet object
     *
     * @param int $objectID Object identifier
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function load(int $objectID): self {
        $category = new CMDBCategory($this->api);
        $netInfo = $category->read($objectID, 'C__CATS__NET');

        if (count($netInfo) !== 1 ||
            !array_key_exists(0, $netInfo) ||
            !is_array($netInfo[0])) {
            throw new RuntimeException(sprintf(
                'Nothing found for object identifier %s',
                $objectID
            ));
        }

        if (!array_key_exists('type', $netInfo[0]) ||
            !is_array($netInfo[0]['type']) ||
            !array_key_exists('const', $netInfo[0]['type']) ||
            !is_string($netInfo[0]['type']['const']) ||
            $netInfo[0]['type']['const'] !== 'C__CATS_NET_TYPE__IPV4') {
            throw new RuntimeException('Works only for IPv4');
        }

        if (!array_key_exists('range_from', $netInfo[0]) ||
            !is_string($netInfo[0]['range_from']) ||
            !array_key_exists('range_to', $netInfo[0]) ||
            !is_string($netInfo[0]['range_to'])) {
            throw new RuntimeException(sprintf(
                'Subnet #%s has no IP address range',
                $objectID
            ));
        }

        $this->first = $this->convertIPv4Address($netInfo[0]['range_from']);
        $this->last = $this->convertIPv4Address($netInfo[0]['range_to']);

        $takenIPAddresses = $category->read($objectID, 'C__CATS__NET_IP_ADDRESSES');

        foreach ($takenIPAddresses as $takenIPAddress) {
            $this->taken[] = $takenIPAddress['title'];
        }

        $this->current = $this->first;

        return $this;
    }

    /**
     * Is there a free IP address?
     *
     * @return bool
     *
     * @throws Exception on error
     */
    public function hasNext(): bool {
        if (!isset($this->current)) {
            throw new BadMethodCallException('You need to call method "load()" first.');
        }

        for ($ipLong = $this->current; $ipLong <= $this->last; $ipLong++) {
            if ($this->isUsed($ipLong) === false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Fetches next free IP address
     *
     * @return string IPv4 address
     *
     * @throws Exception on error
     */
    public function next(): string {
        if (!isset($this->current)) {
            throw new BadMethodCallException('You need to call method "load()" first.');
        }

        for ($ipLong = $this->current; $ipLong <= $this->last; $ipLong++) {
            $this->current = $ipLong;

            if ($this->isUsed($ipLong) === false) {
                return long2ip($ipLong);
            }
        }

        throw new RuntimeException('No free IP addresses left');
    }

    /**
     * Is IP address currently unused in subnet?
     *
     * @param string $ipAddress IPv4 address
     *
     * @return bool
     *
     * @throws Exception on error
     */
    public function isFree(string $ipAddress): bool {
        if (!isset($this->current)) {
            throw new BadMethodCallException('You need to call method "load()" first.');
        }

        $longIP = $this->convertIPv4Address($ipAddress);

        return ! $this->isUsed($longIP);
    }

    /**
     * Is IP address already taken in subnet?
     *
     * @param int $longIP IPv4 address converted to long integer
     *
     * @return bool
     *
     * @throws Exception on error
     */
    protected function isUsed(int $longIP): bool {
        foreach ($this->taken as $taken) {
            $takenIPLong = $this->convertIPv4Address($taken);

            if ($takenIPLong === $longIP) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convert IPv4 address to integer
     *
     * @param string $ipv4Address IPv4 address
     *
     * @return int
     *
     * @throws Exception on error
     */
    protected function convertIPv4Address(string $ipv4Address): int {
        $longIP = ip2long($ipv4Address);

        if (!is_int($longIP)) {
            throw new RuntimeException(sprintf(
                'Unable to convert IP address "%s"',
                $ipv4Address
            ));
        }

        return $longIP;
    }

}
