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

namespace bheisig\idoitapi\tests;

use bheisig\idoitapi\CMDBDialog;
use bheisig\idoitapi\tests\Constants\Category;
use bheisig\idoitapi\tests\Constants\ObjectType;
use bheisig\idoitapi\tests\Extension\Statistics;
use \Exception;
use PHPUnit\Framework\TestCase;
use bheisig\idoitapi\API;
use bheisig\idoitapi\CMDBObject;
use bheisig\idoitapi\CMDBObjects;
use bheisig\idoitapi\CMDBCategory;
use Symfony\Component\Dotenv\Dotenv;

abstract class BaseTest extends TestCase {

    /**
     * @var API
     */
    protected $api;

    /**
     * @var CMDBObject
     */
    protected $cmdbObject;

    /**
     * @var CMDBObjects
     */
    protected $cmdbObjects;

    /**
     * @var CMDBCategory
     */
    protected $cmdbCategory;

    /**
     * @var CMDBDialog
     */
    protected $cmdbDialog;

    /**
     * Information about this project
     *
     * @var array
     */
    protected static $composer = [];

    /**
     * List of valid object conditions ("status")
     *
     * @var array List of integers
     */
    protected $conditions = [
        1, // Unfinished
        2, // Normal
        3, // Archived
        4, // Deleted
        6, // Template
        7 // Mass change template
    ];

    /**
     * @var Dotenv
     */
    protected static $dotEnv;

    /**
     * Load environment settings
     *
     * @throws Exception on error
     */
    public static function setUpBeforeClass() {
        self::$dotEnv = new Dotenv(true);
        self::$dotEnv->load(__DIR__ . '/../.env');

        $composerFile = __DIR__ . '/../composer.json';

        if (is_readable($composerFile)) {
            $composerFileContent = file_get_contents($composerFile);

            if (is_string($composerFileContent)) {
                self::$composer = json_decode($composerFileContent, true);
            }
        }
    }

    /**
     * Make API available
     *
     * @throws Exception on error
     */
    public function setUp() {
        $config = [
            API::URL => getenv('URL'),
            API::KEY => getenv('KEY'),
            API::BYPASS_SECURE_CONNECTION => getenv('BYPASS_SECURE_CONNECTION')
        ];

        if (getenv('PORT') !== false) {
            $config[API::PORT] = (int) getenv('PORT');
        }

        if (getenv('IDOIT_LANGUAGE') !== false) {
            $config[API::LANGUAGE] = getenv('IDOIT_LANGUAGE');
        }

        if (getenv('USERNAME') !== false && getenv('PASSWORD') !== false) {
            $config[API::USERNAME] = getenv('USERNAME');
            $config[API::PASSWORD] = getenv('PASSWORD');
        }

        if (getenv('BYPASS_SECURE_CONNECTION') !== false) {
            $config[API::BYPASS_SECURE_CONNECTION] = filter_var(
                getenv('BYPASS_SECURE_CONNECTION'),
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            );
        }

        $this->api = new API($config);
    }

    public function tearDown(): void {
        Statistics::getInstance()->update(
            Statistics::API_REQUEST_COUNTER,
            $this->api->countRequests()
        );
    }

    public function useCMDBObject(): CMDBObject {
        if (!isset($this->cmdbObject)) {
            $this->cmdbObject = new CMDBObject($this->api);
        }

        return $this->cmdbObject;
    }

    public function useCMDBObjects(): CMDBObjects {
        if (!isset($this->cmdbObjects)) {
            $this->cmdbObjects = new CMDBObjects($this->api);
        }

        return $this->cmdbObjects;
    }

    public function useCMDBCategory(): CMDBCategory {
        if (!isset($this->cmdbCategory)) {
            $this->cmdbCategory = new CMDBCategory($this->api);
        }

        return $this->cmdbCategory;
    }

    public function useCMDBDialog(): CMDBDialog {
        if (!isset($this->cmdbDialog)) {
            $this->cmdbDialog = new CMDBDialog($this->api);
        }

        return $this->cmdbDialog;
    }

    /**
     * Create a new server object with random title
     *
     * @return int Object identifier
     *
     * @throws Exception
     */
    protected function createServer(): int {
        return $this->useCMDBObject()->create(
            ObjectType::SERVER,
            $this->generateRandomString()
        );
    }

    /**
     * Create a new person object with random name and an email address
     *
     * @return array Associative array with keys 'id', 'firstName', 'lastName' and 'email'
     *
     * @throws Exception
     */
    protected function createPerson(): array {
        $firstName = substr($this->generateRandomString(), 0, 10);
        $lastName = substr($this->generateRandomString(), 0, 10);
        $email = sprintf(
            '%s.%s@example.org',
            $firstName,
            $lastName
        );

        $personID = $this->useCMDBObject()->create(
            ObjectType::PERSON,
            $firstName . ' ' . $lastName
        );

        $this->useCMDBCategory()->save(
            $personID,
            Category::CATG__MAIL_ADDRESSES,
            [
                'title' => $email,
                'primary' => 1,
                'description' => $this->generateDescription()
            ]
        );

        return [
            'id' => $personID,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email
        ];
    }

    /**
     * Create new workstation object
     *
     * @return int Object identifier
     *
     * @throws Exception
     */
    protected function createWorkstation(): int {
        $workstationID = $this->useCMDBObject()->create(
            ObjectType::WORKSTATION,
            $this->generateRandomString()
        );

        return $workstationID;
    }

    /**
     * Add person object to workstation object
     *
     * @param int $personID Object identifier
     * @param int $workstationID Object identifier
     *
     * @return int Category entry identifier
     *
     * @throws Exception
     */
    protected function addPersonToWorkstation(int $personID, int $workstationID): int {
        return $this->useCMDBCategory()->create(
            $workstationID,
            Category::CATG__LOGICAL_UNIT,
            [
                'parent' => $personID,
                'description' => $this->generateDescription()
            ]
        );
    }

    /**
     * Add component object to workstation object
     *
     * @param int $workstationID Object identifier
     * @param string $objectTypeConst Object type constant
     *
     * @return int Category entry identifier
     *
     * @throws Exception
     */
    protected function addWorkstationComponent(int $workstationID, string $objectTypeConst): int {
        $componentID = $this->useCMDBObject()->create(
            $objectTypeConst,
            $this->generateRandomString()
        );

        return $this->useCMDBCategory()->create(
            $componentID,
            Category::CATG__ASSIGNED_WORKSTATION,
            [
                'parent' => $workstationID,
                'description' => $this->generateDescription()
            ]
        );
    }

    /**
     * Find object "Global v4"
     *
     * @return int Object identifier
     *
     * @throws Exception
     */
    protected function getIPv4Net(): int {
        return $this->useCMDBObjects()->getID('Global v4', ObjectType::LAYER3_NET);
    }

    /**
     * Find object "Root location"
     *
     * @return int Object identifier
     *
     * @throws Exception
     */
    protected function getRootLocation(): int {
        return $this->useCMDBObjects()->getID('Root location', ObjectType::LOCATION_GENERIC);
    }

    /**
     * Create IPv4 subnet 10.0.0.0/8
     *
     * @return int Object identifier
     *
     * @throws Exception on error
     */
    protected function createSubnet(): int {
        $netID = $this->useCMDBObject()->create(ObjectType::LAYER3_NET, $this->generateRandomString());

        $this->useCMDBCategory()->create($netID, Category::CATS__NET, [
            'type' => 1, // IPv4
            'address' => '10.0.0.0',
            'netmask' => '255.0.0.0'
        ]);

        return $netID;
    }

    /**
     * Add random IPv4 address to object
     *
     * @param int $objectID Object identifier
     * @param int $subnetID Use this subnet, otherwise fall back to "Global v4"
     *
     * @return int Category entry identifier
     *
     * @throws Exception
     */
    protected function addIPv4(int $objectID, int $subnetID = null): int {
        return $this->useCMDBCategory()->create(
            $objectID,
            Category::CATG__IP,
            [
                'net' => (isset($subnetID)) ? $subnetID : $this->getIPv4Net(),
                'active' => mt_rand(0, 1),
                'primary' => mt_rand(0, 1),
                'net_type' => 1, // IPv4
                'ipv4_assignment' => 2, // Static
                'ipv4_address' => $this->generateIPv4Address(),
                'description' => $this->generateDescription()
            ]
        );
    }

    /**
     * Add information about manufacturer, model and serial number to object
     *
     * @param int $objectID Object identifier
     *
     * @return int Category entry identifier
     *
     * @throws Exception
     */
    protected function defineModel(int $objectID): int {
        return $this->useCMDBCategory()->create(
            $objectID,
            Category::CATG__MODEL,
            [
                'manufacturer' => $this->generateRandomString(),
                'title' => $this->generateRandomString(),
                'serial' => $this->generateRandomString(),
                'description' => $this->generateDescription()
            ]
        );
    }

    /**
     * Add object to location
     *
     * @param int $objectID Object idenifier
     * @param int $locationID Object identifier
     *
     * @return int Category entry identifier
     *
     * @throws Exception on error
     */
    protected function addObjectToLocation(int $objectID, int $locationID): int {
        return $this->useCMDBCategory()->create(
            $objectID,
            Category::CATG__LOCATION,
            [
                'parent' => $locationID,
                'description' => $this->generateDescription()
            ]
        );
    }

    /**
     * Add contact to object
     *
     * @param int $objectID Object identifier
     * @param int $contactID Contact object identifier
     * @param int $roleID Role identifier; defaults to 1 ('administrator')
     *
     * @return int Category entry identifier
     *
     * @throws Exception on error
     */
    protected function addContact(int $objectID, int $contactID, int $roleID = 1): int {
        return $this->useCMDBCategory()->create(
            $objectID,
            Category::CATG__CONTACT,
            [
                'contact' => $contactID,
                'role' => $roleID,
                'description' => $this->generateDescription()
            ]
        );
    }

    /**
     * Generate random string
     *
     * @return string
     */
    protected function generateRandomString(): string {
        return hash('sha256', (string) microtime(true));
    }

    protected function generateRandomID(): int {
        return mt_rand(1, PHP_INT_MAX);
    }

    /**
     * Generate random IPv4 address
     *
     * @return string
     */
    protected function generateIPv4Address(): string {
        return sprintf(
            '10.%s.%s.%s',
            mt_rand(1, 254),
            mt_rand(1, 254),
            mt_rand(1, 254)
        );
    }

    /**
     * Generate longer description text
     *
     * @return string
     */
    protected function generateDescription(): string {
        return sprintf(
            'This data is auto-generated at %s by a unit test for %s, version %s',
            date('c'),
            self::$composer['name'],
            self::$composer['extra']['version']
        );
    }

    /**
     * Generate date
     *
     * @return string Y-m-d
     */
    protected function generateDate(): string {
        return date('Y-m-d');
    }

    protected function generateRGB(): string {
        return substr(md5($this->generateRandomString()), 0, 6);
    }

    /**
     * Validate common information about an object
     *
     * @param array $object Common information about an object
     */
    protected function isObject(array $object) {
        $this->assertArrayHasKey('id', $object);
        $this->isID($object['id']);

        $this->assertArrayHasKey('title', $object, sprintf('Object #%s has no title', $object['id']));
        $this->assertIsString($object['title'], sprintf('Object #%s\'s title is not a string', $object['id']));
        $this->assertNotEmpty($object['title'], sprintf('Object #%s has empty title', $object['id']));

        $this->assertArrayHasKey('sysid', $object, sprintf('Object #%s has no SYSID', $object['id']));
        $this->assertIsString($object['sysid'], sprintf('Object #%s\'s SYSID is not a string', $object['id']));
        $this->assertNotEmpty($object['sysid'], sprintf('Object #%s has empty SYSID', $object['id']));

        $this->assertArrayHasKey('type', $object);
        $this->isID($object['type']);

        $this->assertArrayHasKey('created', $object);
        $this->assertIsString($object['created']);
        $this->isTime($object['created']);

        if (array_key_exists('updated', $object)) {
            $this->assertIsString($object['updated']);
            $this->isTime($object['updated']);
        }

        $this->assertArrayHasKey('type_title', $object);
        $this->assertIsString($object['type_title']);
        $this->assertNotEmpty($object['type_title']);

        $this->assertArrayHasKey('type_group_title', $object);
        $this->assertIsString($object['type_group_title']);
        $this->assertNotEmpty($object['type_group_title']);

        $this->assertArrayHasKey('status', $object);
        $this->isID($object['status']);
        $this->assertContains($object['status'], $this->conditions);

        $this->assertArrayHasKey('cmdb_status', $object);
        $this->isID($object['cmdb_status']);

        $this->assertArrayHasKey('cmdb_status_title', $object);
        $this->assertIsString($object['cmdb_status_title']);
        $this->assertNotEmpty($object['cmdb_status_title']);

        $this->assertArrayHasKey('image', $object);
        $this->assertIsString($object['image']);
        $this->assertNotEmpty($object['image']);
    }

    protected function isAssignedObject(array $object) {
        $this->assertArrayHasKey('id', $object);
        $this->isIDAsString($object['id']);

        $this->assertArrayHasKey('title', $object);
        $this->assertIsString($object['title']);
        $this->isOneLiner($object['title']);

        $this->assertArrayHasKey('sysid', $object);
        $this->assertIsString($object['sysid']);
        $this->isOneLiner($object['sysid']);

        $this->assertArrayHasKey('type', $object);
        $this->isConstant($object['type']);

        $this->assertArrayHasKey('type_title', $object);
        $this->assertIsString($object['type_title']);
        $this->isOneLiner($object['type_title']);
    }

    protected function isLocationTree(array $locationTree) {
        foreach ($locationTree as $index => $object) {
            $this->assertIsInt($index);
            $this->assertGreaterThanOrEqual(0, $index);

            $this->assertIsArray($object);
            $this->isLocationObject($object);

            if (array_key_exists('children', $object)) {
                $this->assertIsArray($object['children']);
                $this->assertNotCount(0, $object['children']);
                $this->isLocationTree($object['children']);
            }
        }
    }

    protected function isLocationObject(array $object) {
        $this->assertArrayHasKey('id', $object);
        $this->assertIsInt($object['id']);
        $this->isID($object['id']);

        $this->assertArrayHasKey('title', $object);
        $this->assertIsString($object['title']);
        $this->isOneLiner($object['title']);

        $this->assertArrayHasKey('sysid', $object);
        $this->assertIsString($object['sysid']);
        $this->isOneLiner($object['sysid']);

        $this->assertArrayHasKey('type', $object);
        $this->assertIsInt($object['type']);
        $this->isID($object['type']);

        $this->assertArrayHasKey('type_title', $object);
        $this->assertIsString($object['type_title']);
        $this->isOneLiner($object['type_title']);

        $this->assertArrayHasKey('cmdb_status', $object);
        $this->assertIsInt($object['cmdb_status']);
        $this->isID($object['cmdb_status']);

        $this->assertArrayHasKey('cmdb_status_title', $object);
        $this->assertIsString($object['cmdb_status_title']);
        $this->isOneLiner($object['cmdb_status_title']);
    }

    protected function isDialog(array $dialog) {
        $this->assertArrayHasKey('id', $dialog);
        $this->isIDAsString($dialog['id']);

        $this->assertArrayHasKey('title', $dialog);
        $this->assertIsString($dialog['title']);
        $this->isOneLiner($dialog['title']);

        // "const" is optional and may be null or empty string.
        // Sometimes it's even a constant:
        if (array_key_exists('const', $dialog)) {
            switch (gettype($dialog['const'])) {
                case 'NULL':
                    // Okay!
                    break;
                case 'string':
                    if (strlen($dialog['const'])) {
                        $this->isConstant($dialog['const']);
                    }
                    break;
            }
        }

        // With "cmdb.category.read" we get the translated title:
        if (array_key_exists('title_lang', $dialog)) {
            $this->assertArrayHasKey('title_lang', $dialog);
            $this->assertIsString($dialog['title_lang']);
            $this->isOneLiner($dialog['title_lang']);
        }

        // With "cmdb.dialog.read" we get some information about its parent.
        // But is is completely optional:
        if (array_key_exists('parent', $dialog)) {
            $this->assertIsArray($dialog['parent']);
            $this->assertArrayHasKey('id', $dialog['parent']);
            $this->assertArrayHasKey('const', $dialog['parent']);
            $this->assertArrayHasKey('title', $dialog['parent']);

            // Only if parent is set it's a valid dialog:
            if (isset($dialog['parent']['id']) && isset($dialog['parent']['title'])) {
                $this->isDialog($dialog['parent']);
            }
        }
    }

    protected function isOneLiner(string $value) {
        $length = strlen($value);
        $this->assertGreaterThan(0, $length, 'One-liner is empty');
        $this->assertLessThan(255, $length, 'One-liner is too long');
    }

    /**
     * Validate string as timestamp
     *
     * @param string $time Format: "Y-m-d H:i:s"
     */
    protected function isTime(string $time) {
        $timestamp = strtotime($time);
        $this->assertIsInt($timestamp);
        if (is_int($timestamp)) {
            $formattedTimestamp = date('Y-m-d H:i:s', $timestamp);
            $this->assertIsString($formattedTimestamp);
            $this->assertSame($formattedTimestamp, $time);
        }
    }

    /**
     * Validate string as identifier
     *
     * @param int $value Positive integer
     */
    protected function isID(int $value) {
        $this->assertGreaterThan(0, $value);
    }

    /**
     * Validate string as identifier
     *
     * @param string $value Positive, numeric string
     */
    protected function isIDAsString(string $value) {
        $this->assertIsString($value);
        $id = (int) $value;
        $this->assertGreaterThan(0, $id);
    }

    /**
     * Validate string as i-doit constant
     *
     * @param string $value i-doit constant
     */
    protected function isConstant(string $value) {
        $this->assertNotEmpty($value);
        $this->assertRegExp('/([A-Z0-9_]+)/', $value);
        $this->assertRegExp('/^([A-Z]+)/', $value);
    }

    protected function isValidResponse(array $response, array $request) {
        $this->hasValidJSONRPCIdentifier($request, $response);

        $this->assertArrayHasKey('jsonrpc', $response, 'Missing JSON-RPC version number');
        $this->assertIsString($response['jsonrpc'], 'Invalid JSON-RPC version number');
        $this->assertSame('2.0', $response['jsonrpc'], 'Unknown JSON-RPC version number');

        $this->assertArrayHasKey('result', $response, 'Result is missing');
        $this->assertNotNull($response['result'], 'Result is null');

        $this->assertArrayNotHasKey('error', $response);
    }

    protected function isError(array $response) {
        // 'id' may be set or not depending on whether 'id' is valid in request:
        $this->assertArrayHasKey('id', $response, 'Identifier is missing');

        $this->assertArrayHasKey('jsonrpc', $response, 'Missing JSON-RPC version number');
        $this->assertIsString($response['jsonrpc'], 'Invalid JSON-RPC version number');
        $this->assertSame('2.0', $response['jsonrpc'], 'Unknown JSON-RPC version number');

        $this->assertArrayNotHasKey('result', $response, '"result" must not exist');

        $this->assertArrayHasKey('error', $response, 'Error is missing');
        $this->assertIsArray($response['error'], 'Error is invalid');

        $this->assertArrayHasKey('code', $response['error'], 'Error code is missing');
        $this->assertIsInt($response['error']['code'], 'Error code is invalid');
        $this->assertLessThan(0, $response['error']['code'], 'Error code is invalid');

        $this->assertArrayHasKey('message', $response['error'], 'Error message is missing');
        $this->assertIsString($response['error']['message'], 'Error message is invalid');
        $this->assertNotEmpty($response['error']['message'], 'Error message is empty');

        $this->assertArrayHasKey('data', $response['error']);
        if (isset($response['error']['data'])) {
            $this->assertIsArray($response['error']['data']);
            $this->assertNotCount(0, $response['error']['data']);

            foreach ($response['error']['data'] as $key => $value) {
                // @todo Check whether key is int or string

                $this->assertIsString($value);
            }
        }
    }

    protected function hasValidJSONRPCIdentifier(array $request, array $response) {
        $this->assertArrayHasKey('id', $request, 'Identifier is missing in request');

        $this->assertIsNotBool($request['id']);
        $this->assertIsNotFloat($request['id']);
        $this->assertIsNotArray($request['id']);
        $this->assertIsNotObject($request['id']);
        $this->assertNotNull($request['id']);

        $this->assertArrayHasKey('id', $response, 'Identifier is missing in response');

        $this->assertIsNotBool($response['id']);
        $this->assertIsNotFloat($response['id']);
        $this->assertIsNotArray($response['id']);
        $this->assertIsNotObject($response['id']);
        $this->assertNotNull($response['id']);

        $this->assertSame($request['id'], $response['id'], 'Identifiers in request and response do not match');
    }

    protected function isOutput(array $output) {
        foreach ($output as $lineNumber => $line) {
            $this->assertIsInt($lineNumber);
            $this->assertGreaterThanOrEqual(0, $lineNumber);

            $this->assertIsString($line);
        }
    }

    protected function isSearchResult(array $result) {
        $this->assertArrayHasKey('documentId', $result);
        $this->assertIsString($result['documentId']);
        $this->isIDAsString($result['documentId']);

        $this->assertArrayHasKey('key', $result);
        $this->assertIsString($result['key']);

        $this->assertArrayHasKey('value', $result);
        $this->assertIsString($result['value']);

        $this->assertArrayHasKey('type', $result);
        $this->assertIsString($result['type']);

        $this->assertArrayHasKey('link', $result);
        $this->assertIsString($result['link']);

        $this->assertArrayHasKey('score', $result);
        $this->assertIsString($result['score']);

        $this->assertArrayHasKey('status', $result);
        $this->assertIsString($result['status']);
    }

    protected function isCategoryEntry(array $entry) {
        $this->assertGreaterThanOrEqual(2, count($entry));

        $this->assertArrayHasKey('id', $entry);

        // @todo Old vs. new behavior:
        switch (gettype($entry['id'])) {
            case 'int':
                $this->assertGreaterThan(0, $entry['id']);
                break;
            case 'string':
                $this->assertGreaterThan(0, (int) $entry['id']);
                break;
        }

        $this->assertArrayHasKey('objID', $entry);
        $this->isIDAsString($entry['objID']);
    }

    protected function isEmail(string $email) {
        $filter = filter_var($email, FILTER_VALIDATE_EMAIL);
        $this->assertIsString($filter);
        $this->assertSame($filter, $email);
    }

    /**
     * @param int $objectID Object identifier
     *
     * @throws Exception on error
     */
    protected function isNormal(int $objectID) {
        $object = $this->useCMDBObject()->read($objectID);
        $this->assertIsArray($object);

        $this->assertArrayHasKey('status', $object);
        $this->assertSame(2, (int) $object['status']);
    }

    /**
     * @param int $objectID Object identifier
     *
     * @throws Exception on error
     */
    protected function isArchived(int $objectID) {
        $object = $this->useCMDBObject()->read($objectID);
        $this->assertIsArray($object);

        $this->assertArrayHasKey('status', $object);
        $this->assertSame(3, (int) $object['status']);
    }

    /**
     * @param int $objectID Object identifier
     *
     * @throws Exception on error
     */
    protected function isDeleted(int $objectID) {
        $object = $this->useCMDBObject()->read($objectID);
        $this->assertIsArray($object);

        $this->assertArrayHasKey('status', $object);
        $this->assertSame(4, (int) $object['status']);
    }

    /**
     * @param int $objectID Object identifier
     *
     * @throws Exception on error
     */
    protected function isPurged(int $objectID) {
        $result = $this->useCMDBObject()->read($objectID);
        $this->assertCount(0, $result);
    }

    /**
     * @param int $objectID Object identifier
     *
     * @throws Exception on error
     */
    protected function isTemplate(int $objectID) {
        $object = $this->useCMDBObject()->read($objectID);
        $this->assertIsArray($object);

        $this->assertArrayHasKey('status', $object);
        $this->assertSame(6, (int) $object['status']);
    }

    /**
     * @param int $objectID Object identifier
     *
     * @throws Exception on error
     */
    protected function isMassChangeTemplate(int $objectID) {
        $object = $this->useCMDBObject()->read($objectID);
        $this->assertIsArray($object);

        $this->assertArrayHasKey('status', $object);
        $this->assertSame(7, (int) $object['status']);
    }

    /**
     * Has category entry status "normal" (2)?
     *
     * If entry has another status this method will throw an \Exception.
     *
     * @param int $objectID Object identifier
     * @param string $categoryConstant Category constant
     * @param int $entryID Entry identifier
     *
     * @throws Exception on error
     */
    protected function isNormalEntry(int $objectID, string $categoryConstant, int $entryID) {
        $result = $this->useCMDBCategory()->readOneByID($objectID, $categoryConstant, $entryID, 2);
        $this->assertIsArray($result);

        $this->assertArrayHasKey('id', $result);
        $this->isIDAsString($result['id']);
        $this->assertSame($entryID, (int) $result['id']);

        $this->assertArrayHasKey('objID', $result);
        $this->isIDAsString($result['objID']);
        $this->assertSame($objectID, (int) $result['objID']);
    }

    /**
     * Has category entry status "archived" (3)?
     *
     * If entry has another status this method will throw an \Exception.
     *
     * @param int $objectID Object identifier
     * @param string $categoryConstant Category constant
     * @param int $entryID Entry identifier
     *
     * @throws Exception on error
     */
    protected function isArchivedEntry(int $objectID, string $categoryConstant, int $entryID) {
        $result = $this->useCMDBCategory()->readOneByID($objectID, $categoryConstant, $entryID, 3);
        $this->assertIsArray($result);

        $this->assertArrayHasKey('id', $result);
        $this->isIDAsString($result['id']);
        $this->assertSame($entryID, (int) $result['id']);

        $this->assertArrayHasKey('objID', $result);
        $this->isIDAsString($result['objID']);
        $this->assertSame($objectID, (int) $result['objID']);
    }

    /**
     * Has category entry status "deleted" (4)?
     *
     * If entry has another status this method will throw an \Exception.
     *
     * @param int $objectID Object identifier
     * @param string $categoryConstant Category constant
     * @param int $entryID Entry identifier
     *
     * @throws Exception on error
     */
    protected function isDeletedEntry(int $objectID, string $categoryConstant, int $entryID) {
        $result = $this->useCMDBCategory()->readOneByID($objectID, $categoryConstant, $entryID, 4);
        $this->assertIsArray($result);

        $this->assertArrayHasKey('id', $result);
        $this->isIDAsString($result['id']);
        $this->assertSame($entryID, (int) $result['id']);

        $this->assertArrayHasKey('objID', $result);
        $this->isIDAsString($result['objID']);
        $this->assertSame($objectID, (int) $result['objID']);
    }

    /**
     * Is category entry not available?
     *
     * @param int $objectID Object identifier
     * @param string $categoryConstant Category constant
     * @param int $entryID Entry identifier
     *
     * @throws Exception on error
     */
    protected function isNotAvailable(int $objectID, string $categoryConstant, int $entryID) {
        $this->expectException(Exception::class);
        $result = $this->useCMDBCategory()->readOneByID($objectID, $categoryConstant, $entryID);
        $this->assertIsArray($result);
    }

}
