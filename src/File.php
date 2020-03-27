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

namespace bheisig\idoitapi;

use \Exception;
use \BadMethodCallException;
use \RuntimeException;

/**
 * Requests for assigned files
 */
class File extends Request {

    /**
     * Add a new file to a specific object. A new file object will be created and assigned to the specific object.
     *
     * @param int $objectID Object identifier
     * @param string $filePath Path to file
     * @param string $description (Optional) description
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function add(int $objectID, string $filePath, string $description = ''): self {
        $fileAsString = $this->encode($filePath);

        $cmdbObject = new CMDBObject($this->api);

        $fileObjectID = $cmdbObject->create(
            'C__OBJTYPE__FILE',
            $description
        );

        $cmdbCategory = new CMDBCategory($this->api);

        $cmdbCategory->create(
            $fileObjectID,
            'C__CATS__FILE_VERSIONS',
            [
                'file_content' => $fileAsString,
                'file_physical' => basename($filePath),
                'file_title' => $description,
                'version_description' => $description
            ]
        );

        $cmdbCategory->create(
            $objectID,
            'C__CATG__FILE',
            [
                'file' => $fileObjectID
            ]
        );

        return $this;
    }

    /**
     * Add multiple new files to a specific object.
     * New file objects will be created and assigned to the specific object.
     *
     * @param int $objectID Object identifier
     * @param array $files Associative array (key: path to file; value: description)
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function batchAdd(int $objectID, array $files): self {
        $objects = [];

        foreach ($files as $description) {
            $objects[] = [
                'type' => 'C__OBJTYPE__FILE',
                'title' => $description
            ];
        }

        $cmdbObjects = new CMDBObjects($this->api);

        $fileObjectIDs = $cmdbObjects->create($objects);

        if (count($fileObjectIDs) !== count($files)) {
            throw new RuntimeException(sprintf(
                'Wanted to create %s file object(s) but got %s object identifiers',
                count($files),
                count($fileObjectIDs)
            ));
        }

        $requests = [];

        $counter = 0;

        foreach ($files as $filePath => $description) {
            $fileAsString = $this->encode($filePath);

            $requests[] = [
                'method' => 'cmdb.category.create',
                'params' => [
                    'objID' => $fileObjectIDs[$counter],
                    'catsID' => 'C__CATS__FILE_VERSIONS',
                    'data' => [
                        'file_content' => $fileAsString,
                        'file_physical' => basename($filePath),
                        'file_title' => $description,
                        'version_description' => $description
                    ]
                ]
            ];

            $requests[] = [
                'method' => 'cmdb.category.create',
                'params' => [
                    'objID' => $objectID,
                    'catgID' => 'C__CATG__FILE',
                    'data' => [
                        'file' => $fileObjectIDs[$counter]
                    ]
                ]
            ];

            $counter++;
        }

        $this->api->batchRequest($requests);

        return $this;
    }

    /**
     * Encode an file to base64
     *
     * @param string $filePath Path to file
     *
     * @return string Base64 encoded string
     *
     * @throws Exception on error
     */
    public function encode(string $filePath): string {
        if (!file_exists($filePath) ||
            !is_readable($filePath)) {
            throw new BadMethodCallException(sprintf(
                'File "%s" not found or not readable',
                $filePath
            ));
        }

        $fileContent = file_get_contents($filePath);

        if (!is_string($fileContent)) {
            throw new RuntimeException(sprintf(
                'Unable to read from file "%s"',
                $filePath
            ));
        }

        $fileAsString = base64_encode($fileContent);

        return $fileAsString;
    }

}
