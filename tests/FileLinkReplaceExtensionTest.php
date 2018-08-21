<?php

namespace Symbiote\ContentReplace\Extension;

use SilverStripe\Dev\FunctionalTest;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Shortcodes\FileShortcodeProvider;
use Silverstripe\Assets\Dev\TestAssetStore;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Config\Config;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\Parsers\ShortcodeParser;
use Symbiote\ContentReplace\Model\WYSIWYGElement;

class FileLinkReplaceExtensionTest extends FunctionalTest
{

    protected static $fixture_file = 'FileLinkReplaceExtensionTest.yml';

    // Taken from "vendor/silverstripe/assets/tests/php/Shortcodes/FileShortcodeProviderTest.php"
    public function setUp()
    {
        parent::setUp();
        $this->logInWithPermission('ADMIN');
        Versioned::set_stage(Versioned::DRAFT);
        // Set backend root to /FileLinkReplaceExtensionTest
        TestAssetStore::activate('FileLinkReplaceExtensionTest');

        // Create a test files for each of the fixture references
        $fileIDs = array_merge(
            $this->allFixtureIDs(File::class)
        );
        foreach ($fileIDs as $fileID) {
            /**
             * @var File $file
            */
            $file = DataObject::get_by_id(File::class, $fileID);
            $file->setFromString(str_repeat('x', 1000000), $file->getFilename());
        }
    }

    // Taken from "vendor/silverstripe/assets/tests/php/Shortcodes/FileShortcodeProviderTest.php"
    public function tearDown()
    {
        TestAssetStore::reset();
        parent::tearDown();
    }

    public function testFileLinkReplace()
    {

        $testFile = $this->objFromFixture(File::class, 'example_file');
        
        $parser = new ShortcodeParser();
        $parser->register('file_link', [FileShortcodeProvider::class, 'handle_shortcode']);

        $fileSimpleLink = sprintf('[file_link,id=%d]', $testFile->ID);
        $fileEnclosedWithHtml  = sprintf('<a href="[file_link,id=%d]" class="file" data-type="pdf" data-size="977 KB">Example Content</a>', $testFile->ID);

        $element = WYSIWYGElement::create();
        $linkHtml = '<a href="/assets/FileLinkReplaceExtensionTest/55b443b601/example.pdf" class="file" data-type="pdf" data-size="977 KB">Example Content</a>';
        
        $element->setFileId($testFile->ID);
        $element->setLinkHTML($linkHtml);
        $htmlExpected = $element
            ->renderWith(
                [
                ["type" => "Symbiote/ContentReplace", 'WYSIWYGFileLink'],
                ["type" => "Includes", 'WYSIWYGFileLink']
                ]
            )
            ->RAW();

        $this->assertEquals(
            $testFile->Link(),
            $parser->parse($fileSimpleLink),
            'Test that simple linking works.'
        );
        $this->assertEquals(
            $htmlExpected,
            $parser->parse($fileEnclosedWithHtml),
            'Test file extension and size are added after the enclosed shortcode + html link.'
        );

        $testFile->delete();
    
        $this->assertEquals('', $parser->parse('[file_link]'), 'Test that invalid ID attributes are not parsed.');
        $this->assertEquals('', $parser->parse('[file_link,id="text"]'));
        $this->assertEquals('', $parser->parse('[file_link,id="-1"]'), 'Short code is removed if file record is not present.');
    }
}
