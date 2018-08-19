<?php

namespace Symbiote\ContentReplace\Extension;

use SilverStripe\Core\Extension;
use Symbiote\ContentReplace\Model\WYSIWYGElement;
use SilverStripe\Control\Controller;
use SilverStripe\Admin\LeftAndMain;

class FileLinkReplaceExtension extends Extension {

	protected $fileIdTmp = null;

	public function onBeforeParse(&$content) {
		$isAdminPage = Controller::curr() instanceof LeftAndMain;

		if(!$isAdminPage) {
			$this->setFileIdTmpIfFileLinkExists($content);
		}
	}

	public function onAfterParse(&$content) {
		$isAdminPage = Controller::curr() instanceof LeftAndMain;

		if(!$isAdminPage) {
			$content = $this->replaceFileLinkWithTemplate($content);
		}
	}

	private function setFileIdTmpIfFileLinkExists($value) {
		preg_replace_callback(
			// Match file_link shorcode
			'#\[file_link.id=+([1-9]\d*)+]#i', 
			function($val) {
				// $val[0] - the shorcode, eg: [file_link,id=12]
				// $val[1] - the file_link id, eg: 12
				$this->fileIdTmp = $val[1];
			}, 
			$value
		);
	}

	/**
	 * Replace file links with WYSIWYG_FileLink.ss template.
	 * eg: <a href="[file_link,id=12]">
	 *
	 * @return string
	 */
	private function replaceFileLinkWithTemplate($value) {
		return preg_replace_callback(
			// Match all a tags, even with nested child html tags
			'#<a[\s]+[^>]+>(?:.(?!\<\/a\>))*.<\/a>#i', 
			function($val) {
				// $val[0] - the link HTML tag, eg: <a href="link">text</a>
				$linkHtml = $val[0];
				$element = WYSIWYGElement::create();
				$id = $this->fileIdTmp;
				if(!$id) {
					return $linkHtml;
				}
				$element->setFileId($id);
				$this->fileIdTmp = null;

				// check if the element has href attribute and
				// it's linking to a File rather than an external url
				$file = $element->getFile();
				if(!$file) {
					return $linkHtml;
				}

				// set default link HTML
				$element->setLinkHTML($linkHtml);

				return $element
					->renderWith([
						["type" => "Symbiote/ContentReplace", 'WYSIWYGFileLink'],
						["type" => "Includes", 'WYSIWYGFileLink']
					  ])
					->RAW();
			}, 
			$value
		);
	}

}
