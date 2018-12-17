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

declare(strict_types=1);

namespace bheisig\idoitapi\tests\Issues;

use bheisig\idoitapi\Idoit;
use bheisig\idoitapi\tests\BaseTest;

/**
 * @group issues
 * @group API-142
 * @see https://i-doit.atlassian.net/browse/API-142
 */
class API142Test extends BaseTest {

    /**
     * @var \bheisig\idoitapi\Idoit
     */
    protected $idoit;

    /**
     * @throws \Exception on error
     */
    public function setUp() {
        parent::setUp();

        $this->idoit = new Idoit($this->api);
    }

    /**
     * @throws \Exception on error
     */
    public function testCreateObjectWithCategories() {
        $objectTitle = 'Title ' . $this->generateRandomString();
        $manufacturer = 'Manufacturer ' . $this->generateRandomString();
        $model = 'Model ' . $this->generateRandomString();
        $serial = 'Serial ' . $this->generateRandomString();
        $cpu1 = 'CPU 1 ' . $this->generateRandomString();
        $cpu2 = 'CPU 2 ' . $this->generateRandomString();

        $result = $this->cmdbObject->createWithCategories(
            'C__OBJTYPE__SERVER',
            $objectTitle,
            [
                'C__CATG__MODEL' => [
                    [
                        'manufacturer' => $manufacturer,
                        'title' => $model,
                        'serial' => $serial
                    ]
                ],
                'C__CATG__CPU' => [
                    [
                        'title' => $cpu1,
                    ],
                    [
                        'title' => $cpu2,
                    ]
                ]
            ]
        );

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('id', $result);
        $this->isID($result['id']);

        $this->searchInCMDBFor('Server > Model > Manufacturer', $manufacturer, $result['id'], $objectTitle);
        $this->searchInCMDBFor('Server > Model > Model', $model, $result['id'], $objectTitle);
        $this->searchInCMDBFor('Server > Model > Serial number', $serial, $result['id'], $objectTitle);
        $this->searchInCMDBFor('Server > CPU > Title', $cpu1, $result['id'], $objectTitle);
        $this->searchInCMDBFor('Server > CPU > Title', $cpu2, $result['id'], $objectTitle);
    }

    /**
     * @throws \Exception on error
     */
    public function testCreateSingleValueCategory() {
        $objectTitle = 'Title ' . $this->generateRandomString();
        $manufacturer = 'Manufacturer ' . $this->generateRandomString();
        $model = 'Model ' . $this->generateRandomString();
        $serial = 'Serial ' . $this->generateRandomString();

        $objectID = $this->cmdbObject->create(
            'C__OBJTYPE__SERVER',
            $objectTitle
        );
        $this->isID($objectID);

        $this->cmdbCategory->create(
            $objectID,
            'C__CATG__MODEL',
            [
                'manufacturer' => $manufacturer,
                'title' => $model,
                'serial' => $serial
            ]
        );

        $this->searchInCMDBFor('Server > Model > Manufacturer', $manufacturer, $objectID, $objectTitle);
        $this->searchInCMDBFor('Server > Model > Model', $model, $objectID, $objectTitle);
        $this->searchInCMDBFor('Server > Model > Serial number', $serial, $objectID, $objectTitle);
    }

    /**
     * @throws \Exception on error
     */
    public function testCreateMultiValueCategory() {
        $objectTitle = 'Title ' . $this->generateRandomString();
        $cpu1 = 'CPU 1 ' . $this->generateRandomString();
        $cpu2 = 'CPU 2 ' . $this->generateRandomString();

        $objectID = $this->cmdbObject->create(
            'C__OBJTYPE__SERVER',
            $objectTitle
        );
        $this->isID($objectID);

        $this->cmdbCategory->create(
            $objectID,
            'C__CATG__CPU',
            [
                'title' => $cpu1,
            ]
        );

        $this->cmdbCategory->create(
            $objectID,
            'C__CATG__CPU',
            [
                'title' => $cpu2,
            ]
        );

        $this->searchInCMDBFor('Server > CPU > Title', $cpu1, $objectID, $objectTitle);
        $this->searchInCMDBFor('Server > CPU > Title', $cpu2, $objectID, $objectTitle);
    }

    /**
     * @throws \Exception on error
     */
    public function testUpdateSingleValueCategory() {
        $objectTitle = 'Title ' . $this->generateRandomString();
        $manufacturer = 'Manufacturer ' . $this->generateRandomString();
        $model = 'Model ' . $this->generateRandomString();
        $serial = 'Serial ' . $this->generateRandomString();

        $objectID = $this->cmdbObject->create(
            'C__OBJTYPE__SERVER',
            $objectTitle
        );
        $this->isID($objectID);

        $this->defineModel($objectID);

        $this->cmdbCategory->create(
            $objectID,
            'C__CATG__MODEL',
            [
                'manufacturer' => $manufacturer,
                'title' => $model,
                'serial' => $serial
            ]
        );

        $this->searchInCMDBFor('Server > Model > Manufacturer', $manufacturer, $objectID, $objectTitle);
        $this->searchInCMDBFor('Server > Model > Model', $model, $objectID, $objectTitle);
        $this->searchInCMDBFor('Server > Model > Serial number', $serial, $objectID, $objectTitle);
    }

    /**
     * @throws \Exception on error
     */
    public function testUpdateMultiValueCategory() {
        $objectTitle = 'Title ' . $this->generateRandomString();
        $cpu1 = 'CPU 1 ' . $this->generateRandomString();
        $cpu2 = 'CPU 2 ' . $this->generateRandomString();

        $objectID = $this->cmdbObject->create(
            'C__OBJTYPE__SERVER',
            $objectTitle
        );
        $this->isID($objectID);

        $entry1ID = $this->cmdbCategory->create(
            $objectID,
            'C__CATG__CPU',
            [
                'title' => $this->generateRandomString(),
            ]
        );

        $entry2ID = $this->cmdbCategory->create(
            $objectID,
            'C__CATG__CPU',
            [
                'title' => $this->generateRandomString(),
            ]
        );

        $this->cmdbCategory->update(
            $objectID,
            'C__CATG__CPU',
            [
                'title' => $cpu1,
            ],
            $entry1ID
        );

        $this->cmdbCategory->update(
            $objectID,
            'C__CATG__CPU',
            [
                'title' => $cpu2,
            ],
            $entry2ID
        );

        $this->searchInCMDBFor('Server > CPU > Title', $cpu1, $objectID, $objectTitle);
        $this->searchInCMDBFor('Server > CPU > Title', $cpu2, $objectID, $objectTitle);
    }

    /**
     * @throws \Exception on error
     */
    public function testSaveSingleValueCategory() {
        $objectTitle = 'Title ' . $this->generateRandomString();
        $manufacturer = 'Manufacturer ' . $this->generateRandomString();
        $model = 'Model ' . $this->generateRandomString();
        $serial = 'Serial ' . $this->generateRandomString();

        $objectID = $this->cmdbObject->create(
            'C__OBJTYPE__SERVER',
            $objectTitle
        );
        $this->isID($objectID);

        $this->cmdbCategory->save(
            $objectID,
            'C__CATG__MODEL',
            [
                'manufacturer' => $manufacturer,
                'title' => $model,
                'serial' => $serial
            ]
        );

        $this->searchInCMDBFor('Server > Model > Manufacturer', $manufacturer, $objectID, $objectTitle);
        $this->searchInCMDBFor('Server > Model > Model', $model, $objectID, $objectTitle);
        $this->searchInCMDBFor('Server > Model > Serial number', $serial, $objectID, $objectTitle);
    }

    /**
     * @throws \Exception on error
     */
    public function testSaveMultiValueCategory() {
        $objectTitle = 'Title ' . $this->generateRandomString();
        $cpu1 = 'CPU 1 ' . $this->generateRandomString();
        $cpu2 = 'CPU 2 ' . $this->generateRandomString();

        $objectID = $this->cmdbObject->create(
            'C__OBJTYPE__SERVER',
            $objectTitle
        );
        $this->isID($objectID);

        $this->cmdbCategory->save(
            $objectID,
            'C__CATG__CPU',
            [
                'title' => $cpu1,
            ]
        );

        $this->cmdbCategory->save(
            $objectID,
            'C__CATG__CPU',
            [
                'title' => $cpu2,
            ]
        );

        $this->searchInCMDBFor('Server > CPU > Title', $cpu1, $objectID, $objectTitle);
        $this->searchInCMDBFor('Server > CPU > Title', $cpu2, $objectID, $objectTitle);
    }

    /**
     * @param string $attribute Attribute name
     * @param string $value Value
     * @param int $objectID Object identifier
     * @param string $objectTitle Object title
     *
     * @throws \Exception on error
     */
    protected function searchInCMDBFor(string $attribute, string $value, int $objectID, string $objectTitle) {
        $results = $this->idoit->search($value);

        $this->assertInternalType('array', $results);

        $this->assertCount(1, $results);
        $this->assertArrayHasKey(0, $results);
        $this->assertInternalType('array', $results[0]);

        $this->isSearchResult($results[0]);

        $expectedValue = sprintf(
            '%s: %s',
            $objectTitle,
            $value
        );

        $this->assertSame($objectID, (int) $results[0]['documentId']);
        $this->assertSame($attribute, $results[0]['key']);
        $this->assertSame($expectedValue, $results[0]['value']);
        $this->assertSame('cmdb', $results[0]['type']);
        $this->assertSame('Normal', $results[0]['status']);
    }

}
