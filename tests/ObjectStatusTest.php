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

use bheisig\idoitapi\CMDBObject;

/**
 * @coversDefaultClass \bheisig\idoitapi\CMDBObject
 */
class ObjectStatusTest extends BaseTest {

    /**
     * @group API-88
     * @throws \Exception on error
     */
    public function testArchive() {
        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->archive($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isArchived($objectID);
    }

    /**
     * @group API-88
     * @throws \Exception on error
     */
    public function testArchiveDeletedObject() {
        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->delete($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isDeleted($objectID);

        $result = $this->cmdbObject->archive($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isArchived($objectID);
    }

    /**
     * @group API-88
     * @throws \Exception on error
     */
    public function testArchiveArchivedObject() {
        $this->expectException(\Exception::class);

        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->archive($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isArchived($objectID);

        $result = $this->cmdbObject->archive($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isArchived($objectID);
    }

    /**
     * @group API-88
     * @throws \Exception on error
     */
    public function testArchiveTemplate() {
        $this->expectException(\Exception::class);

        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->markAsTemplate($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isTemplate($objectID);

        $result = $this->cmdbObject->archive($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isArchived($objectID);
    }

    /**
     * @group API-88
     * @throws \Exception on error
     */
    public function testArchiveMassChangeTemplate() {
        $this->expectException(\Exception::class);

        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->markAsMassChangeTemplate($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isMassChangeTemplate($objectID);

        $result = $this->cmdbObject->archive($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isArchived($objectID);
    }

    /**
     * @group API-88
     * @throws \Exception on error
     */
    public function testArchiveNonExistingObject() {
        $this->expectException(\Exception::class);

        $objectID = $this->generateRandomID();
        $result = $this->cmdbObject->archive($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isArchived($objectID);
    }

    /**
     * @group API-89
     * @throws \Exception on error
     */
    public function testDelete() {
        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->delete($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isDeleted($objectID);
    }

    /**
     * @group API-89
     * @throws \Exception on error
     */
    public function testDeleteArchivedObject() {
        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->archive($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isArchived($objectID);

        $result = $this->cmdbObject->delete($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isDeleted($objectID);
    }

    /**
     * @group API-89
     * @throws \Exception on error
     */
    public function testDeleteDeletedObject() {
        $this->expectException(\Exception::class);

        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->delete($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isDeleted($objectID);

        $result = $this->cmdbObject->delete($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isDeleted($objectID);
    }

    /**
     * @group API-89
     * @throws \Exception on error
     */
    public function testDeleteTemplate() {
        $this->expectException(\Exception::class);

        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->markAsTemplate($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isTemplate($objectID);

        $result = $this->cmdbObject->delete($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isDeleted($objectID);
    }

    /**
     * @group API-89
     * @throws \Exception on error
     */
    public function testDeleteMassChangeTemplate() {
        $this->expectException(\Exception::class);

        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->markAsMassChangeTemplate($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isMassChangeTemplate($objectID);

        $result = $this->cmdbObject->delete($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isDeleted($objectID);
    }

    /**
     * @group API-89
     * @throws \Exception on error
     */
    public function testDeleteNonExistingObject() {
        $this->expectException(\Exception::class);

        $objectID = $this->generateRandomID();
        $result = $this->cmdbObject->delete($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isDeleted($objectID);
    }

    /**
     * @group API-89
     * @throws \Exception on error
     */
    public function testLegacyArchive() {
        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $this->api->request(
            'cmdb.object.delete',
            [
                'id' => $objectID,
                'status' => 'C__RECORD_STATUS__ARCHIVE'
            ]
        );
        $this->isArchived($objectID);
    }

    /**
     * @group API-89
     * @throws \Exception on error
     */
    public function testLegacyDelete() {
        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $this->api->request(
            'cmdb.object.delete',
            [
                'id' => $objectID,
                'status' => 'C__RECORD_STATUS__DELETED'
            ]
        );
        $this->isDeleted($objectID);
    }

    /**
     * @group API-89
     * @throws \Exception on error
     */
    public function testLegacyPurge() {
        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $this->api->request(
            'cmdb.object.delete',
            [
                'id' => $objectID,
                'status' => 'C__RECORD_STATUS__PURGE'
            ]
        );
        $this->isPurged($objectID);
    }

    /**
     * @group API-90
     * @throws \Exception on error
     */
    public function testPurge() {
        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->purge($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isPurged($objectID);
    }

    /**
     * @group API-90
     * @throws \Exception on error
     */
    public function testPurgeArchivedObject() {
        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->archive($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isArchived($objectID);

        $result = $this->cmdbObject->purge($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isPurged($objectID);
    }

    /**
     * @group API-90
     * @throws \Exception on error
     */
    public function testPurgeDeletedObject() {
        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->delete($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isDeleted($objectID);

        $result = $this->cmdbObject->purge($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isPurged($objectID);
    }

    /**
     * @group API-90
     * @throws \Exception on error
     */
    public function testPurgeTemplate() {
        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->markAsTemplate($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isTemplate($objectID);

        $result = $this->cmdbObject->purge($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isPurged($objectID);
    }

    /**
     * @group API-90
     * @throws \Exception on error
     */
    public function testPurgeMassChangeTemplate() {
        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->markAsMassChangeTemplate($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isMassChangeTemplate($objectID);

        $result = $this->cmdbObject->purge($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isPurged($objectID);
    }

    /**
     * @group API-90
     * @throws \Exception on error
     */
    public function testPurgeNonExistingObject() {
        $this->expectException(\Exception::class);

        $objectID = $this->generateRandomID();
        $result = $this->cmdbObject->purge($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isPurged($objectID);
    }

    /**
     * @group API-91
     * @throws \Exception on error
     */
    public function testRecycleNormalObject() {
        $this->expectException(\Exception::class);

        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->recycle($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isNormal($objectID);
    }

    /**
     * @group API-91
     * @throws \Exception on error
     */
    public function testRecycleArchivedObject() {
        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->archive($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isArchived($objectID);

        $result = $this->cmdbObject->recycle($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isNormal($objectID);
    }

    /**
     * @group API-91
     * @throws \Exception on error
     */
    public function testRecycleDeletedObject() {
        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->delete($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isDeleted($objectID);

        $result = $this->cmdbObject->recycle($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isNormal($objectID);
    }

    /**
     * @group API-91
     * @throws \Exception on error
     */
    public function testRecyclePurgedObject() {
        $this->expectException(\Exception::class);

        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->purge($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isPurged($objectID);

        $result = $this->cmdbObject->recycle($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isNormal($objectID);
    }

    /**
     * @group API-91
     * @throws \Exception on error
     */
    public function testRecycleTemplate() {
        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->markAsTemplate($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isTemplate($objectID);

        $result = $this->cmdbObject->recycle($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isNormal($objectID);
    }

    /**
     * @group API-91
     * @throws \Exception on error
     */
    public function testRecycleMassChangeTemplate() {
        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->markAsMassChangeTemplate($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isMassChangeTemplate($objectID);

        $result = $this->cmdbObject->recycle($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isNormal($objectID);
    }

    /**
     * @group API-91
     * @throws \Exception on error
     */
    public function testRecycleNonExistingObject() {
        $this->expectException(\Exception::class);

        $objectID = $this->generateRandomID();
        $result = $this->cmdbObject->recycle($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isNormal($objectID);
    }

    /**
     * @group API-92
     * @throws \Exception on error
     */
    public function testMarkAsTemplate() {
        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->markAsTemplate($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isTemplate($objectID);
    }

    /**
     * @group API-92
     * @throws \Exception on error
     */
    public function testConvertFromMassChangeToTemplate() {
        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->markAsMassChangeTemplate($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isMassChangeTemplate($objectID);

        $result = $this->cmdbObject->markAsTemplate($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isTemplate($objectID);
    }

    /**
     * @group API-92
     * @throws \Exception on error
     */
    public function testConvertFromArchivedToTemplate() {
        $this->expectException(\Exception::class);

        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->archive($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isArchived($objectID);

        $result = $this->cmdbObject->markAsTemplate($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isTemplate($objectID);
    }

    /**
     * @group API-92
     * @throws \Exception on error
     */
    public function testConvertFromDeletedToTemplate() {
        $this->expectException(\Exception::class);

        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->delete($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isDeleted($objectID);

        $result = $this->cmdbObject->markAsTemplate($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isTemplate($objectID);
    }

    /**
     * @group API-92
     * @throws \Exception on error
     */
    public function testConvertFromTemplateToTemplate() {
        $this->expectException(\Exception::class);

        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->markAsTemplate($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isTemplate($objectID);

        $result = $this->cmdbObject->markAsTemplate($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isTemplate($objectID);
    }

    /**
     * @group API-92
     * @throws \Exception on error
     */
    public function testMarkNonExistingObjectAsTemplate() {
        $this->expectException(\Exception::class);

        $objectID = $this->generateRandomID();
        $result = $this->cmdbObject->markAsTemplate($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isTemplate($objectID);
    }

    /**
     * @group API-93
     * @throws \Exception on error
     */
    public function testMarkAsMassChangeTemplate() {
        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->markAsMassChangeTemplate($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isMassChangeTemplate($objectID);
    }

    /**
     * @group API-93
     * @throws \Exception on error
     */
    public function testConvertFromTemplateToMassChangeTemplate() {
        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->markAsTemplate($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isTemplate($objectID);

        $result = $this->cmdbObject->markAsMassChangeTemplate($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isMassChangeTemplate($objectID);
    }

    /**
     * @group API-93
     * @throws \Exception on error
     */
    public function testConvertFromArchivedToMassChangeTemplate() {
        $this->expectException(\Exception::class);

        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->archive($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isArchived($objectID);

        $result = $this->cmdbObject->markAsMassChangeTemplate($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isMassChangeTemplate($objectID);
    }

    /**
     * @group API-93
     * @throws \Exception on error
     */
    public function testConvertFromDeletedToMassChangeTemplate() {
        $this->expectException(\Exception::class);

        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->delete($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isDeleted($objectID);

        $result = $this->cmdbObject->markAsMassChangeTemplate($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isMassChangeTemplate($objectID);
    }

    /**
     * @group API-93
     * @throws \Exception on error
     */
    public function testConvertFromMassChangeTemplateToMassChangeTemplate() {
        $this->expectException(\Exception::class);

        $objectID = $this->createServer();
        $this->isNormal($objectID);

        $result = $this->cmdbObject->markAsMassChangeTemplate($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isMassChangeTemplate($objectID);

        $result = $this->cmdbObject->markAsMassChangeTemplate($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
    }

    /**
     * @group API-93
     * @throws \Exception on error
     */
    public function testMarkNonExistingObjectAsMassChangeTemplate() {
        $this->expectException(\Exception::class);

        $objectID = $this->generateRandomID();
        $result = $this->cmdbObject->markAsMassChangeTemplate($objectID);
        $this->assertInstanceOf(CMDBObject::class, $result);
        $this->isMassChangeTemplate($objectID);
    }

}
