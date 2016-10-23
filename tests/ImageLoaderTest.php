<?php

/**
 * Test suite for ImageLoader class
 *
 * @author Vadym Chenin <vchenin@meta.ua>
 */
class ImageLoaderTest extends PHPUnit_Framework_TestCase {

    private $_imgLoader;
    private $_imgName;
    private $_destImgPath;
    private $_srcImgPath;

    public function setUp()
    {
        $destDirPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'images';
        $srcDirPath = sys_get_temp_dir();

        $this->_imgLoader = new \TestPackage\ImageLoader($destDirPath);
        $this->_imgName = 'img.png';
        $this->_destImgPath = $destDirPath . DIRECTORY_SEPARATOR . $this->_imgName;
        $this->_srcImgPath = $srcDirPath . DIRECTORY_SEPARATOR . $this->_imgName;

        if (is_file($this->_destImgPath)) {
            unlink($this->_destImgPath);
        }
    }

    public function tearDown()
    {
        
    }

    public function testAddFormat()
    {
        $exist = false;
        $this->_imgLoader->addFormat('test');
        foreach ($this->_imgLoader->getSupportedFormats() as $format) {
            if ($format == 'test') {
                $exist = true;
                break;
            }
        }
        $this->assertTrue($exist);
    }

    public function testIsSupportedFormat()
    {
        $this->_imgLoader->addFormat('test');
        $this->assertTrue($this->_imgLoader->isSupportedFormat('test'));
    }

    public function testRemoveFormat()
    {
        $this->_imgLoader->addFormat('test');
        $this->_imgLoader->removeFormat('test');
        $this->assertFalse($this->_imgLoader->isSupportedFormat('test'));
    }

    public function testGetDestination()
    {
        $this->assertTrue(is_dir(dirname($this->_imgLoader->getDestination())));
    }

    public function testSetDestination()
    {
        $this->_imgLoader->setDestination($this->_imgLoader->getDestination());
        $this->assertTrue(is_dir(dirname($this->_imgLoader->getDestination())));
    }

    public function testLoad_UnsupportedFormat()
    {
        file_put_contents($this->_srcImgPath, 'PNG');

        $this->setExpectedException('Exception');
        $this->_imgLoader->removeFormat('png');
        $this->_imgLoader->load($this->_srcImgPath);
    }

    public function testLoad_UnavailableImage()
    {
        file_put_contents($this->_srcImgPath, 'PNG');

        $this->setExpectedException('Exception');
        $this->_imgLoader->addFormat('png');
        $this->_imgLoader->load('http://example.com/' . $this->_imgName);
    }

    public function testLoad_PossibleImageName()
    {
        file_put_contents($this->_srcImgPath, 'PNG');

        $this->setExpectedException('Exception');
        $this->_imgLoader->load($this->_srcImgPath);
        $this->_imgLoader->load($this->_srcImgPath);
    }

    public function testLoad_DestinationAccessibility()
    {
        file_put_contents($this->_srcImgPath, 'PNG');

        $this->setExpectedException('Exception');
        $this->_imgLoader->setDestination('');
        $this->_imgLoader->load($this->_srcImgPath);
    }

}
