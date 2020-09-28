<?php

/**
 * Copyright (C) 2016-2020 Benjamin Heisig
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
 * @copyright Copyright (C) 2016-2020 Benjamin Heisig
 * @license http://www.gnu.org/licenses/agpl-3.0 GNU Affero General Public License (AGPL)
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

declare(strict_types=1);

namespace bheisig\idoitapi\Issues;

use \Exception;
use bheisig\idoitapi\BaseTest;
use bheisig\idoitapi\Constants\Category;

/**
 * @group issues
 * @group API-205
 * @see https://i-doit.atlassian.net/browse/API-205
 */
class API205Test extends BaseTest {

    /**
     * This list is based on this SQL query:
     *
     * SELECT
     * isysgui_catg__const AS 'Global constant',
     * isysgui_cats__const AS 'Specific constant',
     * isys_property_2_cat__prop_key AS 'Property identifier'
     * FROM isys_property_2_cat
     * LEFT JOIN isysgui_catg ON isysgui_catg__id = isys_property_2_cat__isysgui_catg__id
     * LEFT JOIN isysgui_cats ON isysgui_cats__id = isys_property_2_cat__isysgui_cats__id
     * WHERE isys_property_2_cat__prop_provides & 128
     *
     * @var array
     */
    protected static $virtualProperties = [
        [Category::CATG__GLOBAL, 'id'],
        [Category::CATG__MEMORY, 'quantity'],
        [Category::CATG__MEMORY, 'total_capacity'],
        [Category::CATG__POWER_CONSUMER, 'connector'],
        [Category::CATG__POWER_CONSUMER, 'connector_sibling'],
        [Category::CATG__UNIVERSAL_INTERFACE, 'connector'],
        [Category::CATG__APPLICATION, 'application_type'],
        [Category::CATG__APPLICATION, 'assigned_license_key'],
        [Category::CATG__ACCESS, 'primary_url'],
        [Category::CATG__ACCESS, 'formatted_url'],
        [Category::CATG__EMERGENCY_PLAN, 'time_needed'],
        [Category::CATG__EMERGENCY_PLAN, 'time_needed_unit'],
        [Category::CATG__EMERGENCY_PLAN, 'practice_date'],
        [Category::CATG__FILE, 'revision'],
        [Category::CATG__CONTACT, 'primary_contact'],
        [Category::CATG__CONTACT, 'contact_list'],
        [Category::CATG__CONTACT, 'contact_list_with_roles'],
        [Category::CATG__LOCATION, 'location_path'],
        [Category::CATG__LOCATION, 'pos'],
        [Category::CATG__LOCATION, 'latitude'],
        [Category::CATG__LOCATION, 'longitude'],
        [Category::CATG__IMAGE, 'image'],
        [Category::CATG__OBJECT, 'assigned_object'],
        [Category::CATG__ACCOUNTING, 'guarantee_date'],
        [Category::CATG__ACCOUNTING, 'guarantee_status'],
        [Category::CATG__NETWORK_PORT, 'connector'],
        [Category::CATG__NETWORK_PORT, 'addresses'],
        [Category::CATG__NETWORK_PORT, 'layer2_assignment'],
        [Category::CATG__NETWORK_PORT, 'default_vlan'],
        [Category::CATG__NETWORK_PORT, 'relation_direction'],
        [Category::CATG__NETWORK_LOG_PORT, 'net'],
        [Category::CATG__NETWORK_LOG_PORT, 'addresses'],
        [Category::CATG__DRIVE, 'device'],
        [Category::CATG__DRIVE, 'raid'],
        [Category::CATG__DRIVE, 'ldev'],
        [Category::CATG__DRIVE, 'category_const'],
        [Category::CATG__CONTROLLER_FC_PORT, 'connector_sibling'],
        [Category::CATG__CONTROLLER_FC_PORT, 'connector'],
        [Category::CATG__CONTROLLER_FC_PORT, 'relation_direction'],
        [Category::CATG__IP, 'primary_hostaddress'],
        [Category::CATG__IP, 'primary_hostname'],
        [Category::CATG__IP, 'dns_server_address'],
        [Category::CATG__IP, 'assigned_logical_port'],
        [Category::CATG__IP, 'all_ips'],
        [Category::CATG__IP, 'primary_fqdn'],
        [Category::CATG__IP, 'aliases'],
        [Category::CATG__CONNECTOR, 'relation_direction'],
        [Category::CATG__POWER_SUPPLIER, 'connector'],
        [Category::CATG__POWER_SUPPLIER, 'assigned_category'],
        [Category::CATG__RAID, 'storages'],
        [Category::CATG__RAID, 'full_capacity'],
        [Category::CATG__RAID, 'capacity'],
        [Category::CATG__LDEV_SERVER, 'primary_path'],
        [Category::CATG__LDEV_CLIENT, 'paths'],
        [Category::CATG__LDEV_CLIENT, 'primary_path'],
        [Category::CATG__CLUSTER, 'administration_service'],
        [Category::CATG__CLUSTER, 'cluster_members'],
        [Category::CATG__CLUSTER, 'cluster_member_count'],
        [Category::CATG__CLUSTER, 'cluster_service'],
        [Category::CATG__CLUSTER_SERVICE, 'hostaddresses'],
        [Category::CATG__CLUSTER_SERVICE, 'drives'],
        [Category::CATG__CLUSTER_SERVICE, 'shares'],
        [Category::CATG__CLUSTER_SERVICE, 'runs_on'],
        [Category::CATG__CLUSTER_SERVICE, 'default_server'],
        [Category::CATG__CLUSTER_SERVICE, 'assigned_database_schema'],
        [Category::CATG__IT_SERVICE, 'sysid'],
        [Category::CATG__DATABASE_ASSIGNMENT, 'database_assignment'],
        [Category::CATG__DATABASE_ASSIGNMENT, 'runs_on'],
        [Category::CATG__SOA_STACKS, 'soa_stack_it_services'],
        [Category::CATG__SIM_CARD, 'assigned_mobile'],
        [Category::CATG__MONITORING, 'host_name'],
        [Category::CATG__LIVESTATUS, 'livestatus_state'],
        [Category::CATG__LIVESTATUS, 'livestatus_state_button'],
        [Category::CATG__NET_CONNECTOR, 'connected_to'],
        [Category::CATG__CLUSTER_ADM_SERVICE, 'objtype'],
        [Category::CATG__NDO, 'ndo_state'],
        [Category::CATG__NDO, 'ndo_state_button'],
        [Category::CATG__CABLE, 'connection'],
        [Category::CATG__SERVICE, 'service_alias'],
        [Category::CATG__OPERATING_SYSTEM, 'application_type'],
        [Category::CATG__OPERATING_SYSTEM, 'application_priority'],
        [Category::CATG__OPERATING_SYSTEM, 'assigned_license_key'],
        [Category::CATG__IMAGES, 'name'],
        [Category::CATG__IMAGES, 'content'],
        [Category::CATG__RM_CONTROLLER, 'remote_url'],
        [Category::CATG__MANAGED_OBJECTS, 'connected_object'],
        [Category::CATG__NET_ZONE_SCOPES, 'from_to'],
        [Category::CATG__DATABASE, 'manufacturer'],
        [Category::CATG__DATABASE, 'version'],
        [Category::CATG__DATABASE, 'import_key'],
        [Category::CATG__DATABASE_TABLE, 'instance'],
        [Category::CATG__DATABASE_TABLE, 'import_key'],
        [Category::CATG__DATABASE_SA, 'import_key'],
        [Category::CATS__SERVICE, 'installation_count'],
        [Category::CATS__FILE, 'md5_hash'],
        [Category::CATS__APPLICATION, 'installation_count'],
        [Category::CATS__NET, 'dns_server'],
        [Category::CATS__NET, 'dns_domain'],
        [Category::CATS__NET, 'layer2_assignments'],
        [Category::CATS__NET, 'address_v6'],
        [Category::CATS__NET, 'address_range'],
        [Category::CATS__NET, 'address_with_suffix'],
        [Category::CATS__NET, 'free_addresses'],
        [Category::CATS__LICENCE_LIST, 'used_licences'],
        [Category::CATS__LICENCE_LIST, 'lic_not_in_use'],
        [Category::CATS__LICENCE_LIST, 'overall_costs'],
        [Category::CATS__FILE_VERSIONS, 'file_content'],
        [Category::CATS__FILE_VERSIONS, 'md5_hash'],
        [Category::CATS__FILE_VERSIONS, 'uploaded_by'],
        [Category::CATS__FILE_OBJECTS, 'assigned_objects'],
        [Category::CATS__PERSON_LOGIN, 'user_pass2'],
        [Category::CATS__APPLICATION_ASSIGNED_OBJ, 'application_type'],
        [Category::CATS__APPLICATION_ASSIGNED_OBJ, 'application_priority'],
        [Category::CATS__APPLICATION_ASSIGNED_OBJ, 'assigned_license_key'],
        [Category::CATS__MIDDLEWARE, 'installation_count'],
        [Category::CATS__NET_IP_ADDRESSES, 'net_type'],
        [Category::CATS__NET_IP_ADDRESSES, 'assigned_object'],
        [Category::CATS__NET_IP_ADDRESSES, 'ip_address_link'],
        [Category::CATS__LAYER2_NET, 'ip_helper_addresses'],
        [Category::CATS__LAYER2_NET_ASSIGNED_PORTS, 'isys_catg_port_list__mac'],
        [Category::CATS__CONTRACT, 'notice_period'],
        [Category::CATS__CHASSIS_SLOT, 'from_x'],
        [Category::CATS__CHASSIS_SLOT, 'to_x'],
        [Category::CATS__CHASSIS_SLOT, 'from_y'],
        [Category::CATS__CHASSIS_SLOT, 'to_y'],
        [Category::CATS__CHASSIS_SLOT, 'assigned_devices'],
        [Category::CATS__NET_ZONE, 'range_from_long'],
        [Category::CATS__NET_ZONE, 'range_to_long'],
        [Category::CATS__OPERATING_SYSTEM, 'installation_count'],
        [Category::CATS__DATABASE_INSTALLATION, 'application_type'],
        [Category::CATS__DATABASE_INSTALLATION, 'application_priority'],
        [Category::CATS__DATABASE_INSTALLATION, 'assigned_license_key'],
        [Category::CATS__DATABASE_INSTALLATION, 'assigned_databases']
    ];

    public function provideVirtualProperties(): array {
        $virtualProperties = [];

        foreach (self::$virtualProperties as $tupel) {
            $categoryConstant = $tupel[0];
            $attributeIdentifier = $tupel[1];

            $description = sprintf(
                '%s::%s',
                $categoryConstant,
                $attributeIdentifier
            );

            $virtualProperties[$description] = [$categoryConstant, $attributeIdentifier];
        }

        return $virtualProperties;
    }

//    /**
//     * @dataProvider provideVirtualProperties
//     * @param string $categoryConstant
//     * @param string $attributeIdentifier
//     * @throws Exception on error
//     * @todo This hasn't been implemented yet. See discussion for issue API-205.
//     */
//    public function testEditVirtualProperties(string $categoryConstant, string $attributeIdentifier) {
//        /**
//         * Create test data:
//         */
//
//        $objectID = $this->createServer();
//        $this->isID($objectID);
//
//        /**
//         * Run tests:
//         */
//
//        $request = [
//            'jsonrpc' => '2.0',
//            'method' => 'cmdb.category.save',
//            'params' => [
//                'apikey' => getenv('KEY'),
//                'object' => $objectID,
//                'category' => $categoryConstant,
//                'data' => [
//                    $attributeIdentifier => $this->generateRandomString()
//                ]
//            ],
//            'id' => 1
//        ];
//
//        $response = $this->api->rawRequest($request);
//
//        $this->assertIsArray($response);
//        $this->isError($response);
//        $this->assertNull($response['id']);
//        $this->assertSame(-32603, $response['error']['code']);
//    }

    public function provideVirtualCustomAttributes(): array {
        return [
            'horizontal line' => ['hr_c_1566976986164'],
            'HTML' => ['html_c_1566976997893'],
            'JavaScript' => ['script_c_1566977015135']
        ];
    }

    /**
     * @dataProvider provideVirtualCustomAttributes
     * @param string $attributeIdentifier
     * @throws Exception on error
     */
    public function testVirtualCustomAttribute(string $attributeIdentifier) {
        $this->markTestSkipped(
            'Custom category needed!'
        );

        /**
         * Create test data:
         */

        $objectID = $this->createServer();
        $this->isID($objectID);

        /**
         * Run tests:
         */

        $request = [
            'jsonrpc' => '2.0',
            'method' => 'cmdb.category.save',
            'params' => [
                'apikey' => getenv('KEY'),
                'object' => $objectID,
                'category' => 'C__CATG__CUSTOM_FIELDS_API_205_VIRTUAL_PROPERTIES',
                'data' => [
                    $attributeIdentifier => $this->generateRandomString()
                ]
            ],
            'id' => 1
        ];

        $response = $this->api->rawRequest($request);

        $this->assertIsArray($response);
        $this->isError($response);
        $this->assertSame(-32603, $response['error']['code']);
        $this->assertSame(
            'Internal error: You can not populate virtual properties with data.',
            $response['error']['message']
        );
        $this->assertArrayHasKey($attributeIdentifier, $response['error']['data']);
        $this->assertSame(
            'This property is a "virtual" property and can not contain data.',
            $response['error']['data'][$attributeIdentifier]
        );
    }

}
