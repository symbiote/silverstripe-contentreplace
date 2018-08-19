<?php

namespace Symbiote\ContentReplace\Model;

use SilverStripe\View\ViewableData;
use SilverStripe\Assets\File;

class WYSIWYGElement extends ViewableData {
	/**
	 * the HTML content inside link
	 *
	 * @var string
	 */
	protected $linkHTML = "";

	/**
	 * @var int|null
	 */
	protected $fileId = null;

	/**
	 * @return File|null
	 */
	public function getFile() {
		$id = $this->getFileId();
		return $id ? 
			File::get()->filter('ID', $id)->first() : 
			null;
	}

	/**
	 * Set an HTML attributes on element 
	 */
	public function setLinkHTML(string $linkHTML) {
		$this->linkHTML = $linkHTML;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLinkHTML() {
		return $this->linkHTML;
	}

	public function setFileId(int $id) {
		$this->fileId = $id;
		return $this;
	}

	public function getFileId() {
		return $this->fileId;
	}
}
