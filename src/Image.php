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
 * @link https://github.com/bheisig/i-doit-api-client-php
 */

declare(strict_types=1);

namespace bheisig\idoitapi;

use \Exception;
use \BadMethodCallException;
use \RuntimeException;

/**
 * Requests for image galleries
 */
class Image extends Request {

    /**
     * Add a new file to the image gallery.
     *
     * @param int $objectID Object identifier
     * @param string $filePath Path to image file
     * @param string $caption (Optional) caption
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function add(int $objectID, string $filePath, string $caption = ''): self {
        $imageAsString = $this->encode($filePath);

        $cmdbCategory = new CMDBCategory($this->api);

        $cmdbCategory->create(
            $objectID,
            'C__CATG__IMAGES',
            [
                'name' => $caption,
                'content' => $imageAsString
            ]
        );

        return $this;
    }

    /**
     * Add new files to the image gallery.
     *
     * @param int $objectID Object identifier
     * @param array $images Associative array (key: path to image file; value: caption)
     *
     * @return self Returns itself
     *
     * @throws Exception on error
     */
    public function batchAdd(int $objectID, array $images): self {
        $objectIDs = [$objectID];
        $categoryConst = 'C__CATG__IMAGES';
        $attributes = [];

        foreach ($images as $filePath => $caption) {
            $imageAsString = $this->encode($filePath);

            $attributes[] = [
                'name' => $caption,
                'content' => $imageAsString
            ];
        }

        $cmdbCategory = new CMDBCategory($this->api);

        $cmdbCategory->batchCreate($objectIDs, $categoryConst, $attributes);

        return $this;
    }

    /**
     * Encode an image file to base64
     *
     * @param string $filePath Path to image file
     *
     * @return string Base64 encoded string
     *
     * @throws Exception on error
     */
    public function encode(string $filePath): string {
        if (!file_exists($filePath) ||
            !is_readable($filePath)) {
            throw new BadMethodCallException(sprintf(
                'Image "%s" not found or not readable',
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

        $imageAsString = base64_encode($fileContent);

        return $imageAsString;
    }

}
