<?php

namespace Symbiote\ContentReplace\Extension;

use SilverStripe\Core\Extension;
use Symbiote\ContentReplace\Model\WYSIWYGElement;
use SilverStripe\Control\Controller;
use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Assets\File;

class FileLinkReplaceExtension extends Extension
{

    protected $fileIdsTmp = null;

    public function onBeforeParse(&$content)
    {
        $isAdminPage = Controller::curr() instanceof LeftAndMain;

        if (!$isAdminPage) {
            $this->setfileIdsTmpIfFileLinkExists($content);
        }
    }

    public function onAfterParse(&$content)
    {
        $isAdminPage = Controller::curr() instanceof LeftAndMain;

        if (!$isAdminPage) {
            $content = $this->replaceFileLinkWithTemplate($content);
        }
    }

    private function setfileIdsTmpIfFileLinkExists($value)
    {
        if ($this->fileIdsTmp) {
            $this->fileIdsTmp = null;
        }

        preg_replace_callback(
            // Match file_link shorcode
            '#\[file_link.id=+([1-9]\d*)+]#i',
            function ($val) {
                // $val[0] - the shorcode, eg: [file_link,id=12]
                // $val[1] - the file_link id, eg: 12
                $this->fileIdsTmp[] = $val[1];
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
    private function replaceFileLinkWithTemplate($value)
    {
        $fileIds = $this->fileIdsTmp;
        if (!$fileIds) {
            return $value;
        }

        $files = File::get()->filter('ID', $fileIds);

        $fileMap = [];
        // create a map of URL to file object for later use
        foreach ($files as $file) {
            $fileMap[$file->getURL()] = $file;
        }

        $res = preg_replace_callback(
            // Match all a tags, even with nested child html tags
            '#<a.*?href=\"(.*?)\".*?>(?:.(?!\<\/a\>))*.<\/a>#i',
            function ($val) use ($fileMap) {
                // $val[0] - the link HTML tag, eg: <a href="link">text</a>
                $linkHtml = $val[0];
                $href = $val[1];

                $element = WYSIWYGElement::create();

                if (!isset($fileMap[$href])) {
                    return $linkHtml;
                }

                $element->setFile($fileMap[$href]);

                // set default link HTML
                $element->setLinkHTML($linkHtml);
                return $element
                    ->renderWith(
                        [
                        ["type" => "Symbiote/ContentReplace", 'WYSIWYGFileLink'],
                        ["type" => "Includes", 'WYSIWYGFileLink']
                        ]
                    )
                    ->RAW();
            },
            $value
        );

        return $res;
    }
}
