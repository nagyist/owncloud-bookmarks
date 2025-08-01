<?php

/*
 * Copyright (c) 2020-2024. The Nextcloud Bookmarks contributors.
 *
 * This file is licensed under the Affero General Public License version 3 or later. See the COPYING file.
 */

namespace OCA\Bookmarks\Db;

use OCP\AppFramework\Db\Entity;

/**
 * Class PublicFolder
 *
 * @package OCA\Bookmarks\Db
 *
 * @method getId():string
 * @method getFolderId():int
 * @method setFolderId(int $folderId)
 * @method getDescription():string
 * @method setDescription(string $description)
 * @method getCreatedAt():int
 * @method setCreatedAt(int $createdAt)
 */
class PublicFolder extends Entity {
	/**
	 * @var string
	 */
	public $id;
	protected $folderId;
	protected $description;
	protected $createdAt;

	public static $columns = ['id', 'folder_id', 'description', 'created_at'];

	public function __construct() {
		// add types in constructor
		$this->addType('id', 'string');
		$this->addType('folderId', 'integer');
		$this->addType('description', 'integer');
		$this->addType('created_at', 'integer');
	}

	/*
	 * Overridden because of param type
	 */
	public function setId(string $id): void {
		$this->id = $id;
		$this->markFieldUpdated('id');
	}
}
