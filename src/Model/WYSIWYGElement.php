<?php

namespace Symbiote\ContentReplace\Model;

use SilverStripe\View\ViewableData;
use SilverStripe\Assets\File;

class WYSIWYGElement extends ViewableData
{
    /**
     * the HTML content inside link
     *
     * @var string
     */
    protected $linkHTML = "";

    /**
     * @var File|null
     */
    protected $file = null;

    /**
     * @return File|null
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set an HTML attributes on element
     */
    public function setLinkHTML(string $linkHTML)
    {
        $this->linkHTML = $linkHTML;
        return $this;
    }

    /**
     * @return string
     */
    public function getLinkHTML()
    {
        return $this->linkHTML;
    }

    public function setFile(File $file)
    {
        $this->file = $file;
        return $this;
    }

    public function getFileId()
    {
        return $this->file->ID;
    }
}
