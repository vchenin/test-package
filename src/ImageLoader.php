<?php

namespace TestPackage;

/**
 * Class for loading image from given path to destination directory.
 * By default supported image formats are: gif, jpg, png.
 * 
 * @package TestPackage
 * @author  Vadym Chenin <vchenin@meta.ua>
 */
class ImageLoader
{

    private $_destination;
    private $_supportedFormats = ['gif', 'jpg', 'png'];

    public $l10n;

    /**
     * Constructor.
     * 
     * @param string $dirPath Destination directory path for store images.
     */
    public function __construct($dirPath)
    {
        $this->_destination = $dirPath;
        $this->l10n = new Localization(__DIR__ . '/../languages');
    }

    /**
     * This method check accessibility of destination directory.
     * 
     * @return string Current path to destination directory.
     * @throws \Exception
     */
    private function _checkDestinationDirectory()
    {
        $dirPath = $this->getDestination();

        if ((!is_dir($dirPath) && !(@mkdir($dirPath))) || !is_writable($dirPath)) {
            throw new \Exception(
                $this->l10n->msg('DestinationDirectoryIsNotAccessible', $dirPath)
            );
        }

        return $dirPath;
    }

    /**
     * This method tries to get image content from given path.
     * 
     * @param string $imagePath Remote image path.
     * 
     * @return data Image raw data.
     */
    private function _getImageContent($imagePath)
    {
        $imageExt = pathinfo($imagePath, PATHINFO_EXTENSION);

        if (!$this->isSupportedFormat($imageExt)) {
            throw new \Exception(
                $this->l10n->msg('FormatIsNotSupported', [$imageExt, implode(', ', $this->_supportedFormats)])
            );
        }

        if (!$remoteImageContent = @file_get_contents($imagePath)) {
            throw new \Exception(
                $this->l10n->msg('FailedToLoadImageFromPath', $imagePath)
            );
        }

        return $remoteImageContent;
    }

    /**
     * This method check possibility of using remote file name in that directory.
     * 
     * @param string $imagePath Remote image path.
     * 
     * @return string Local path for new image file.
     * @throws \Exception
     */
    private function _getNewImagePath($imagePath)
    {
        $dirPath = $this->_checkDestinationDirectory();
        $imageName = pathinfo($imagePath, PATHINFO_BASENAME);
        $localImagePath = $dirPath . DIRECTORY_SEPARATOR . $imageName;

        if (file_exists($localImagePath)) {
            throw new \Exception(
                $this->l10n->msg('FileWithTheSameNameAlreadyExist', [$imageName, $dirPath])
            );
        }

        return $localImagePath;
    }

    /**
     * This method add new format to the list of supported formats.
     * 
     * @param string $format Image file extension.
     * 
     * @return ImageLoader Current instance.
     */
    public function addFormat($format)
    {
        if (!$this->isSupportedFormat($format)) {
            array_push($this->_supportedFormats, $format);
        }

        return $this;
    }

    /**
     * This method return current destination path.
     * 
     * @return string Current destination directory path.
     */
    public function getDestination()
    {
        return $this->_destination;
    }

    /**
     * Generator which provide supported formats in reverse order from list.
     * 
     * @return Iterator [$key (position in list) => $value (image extension)].
     */
    public function getSupportedFormats()
    {
        for ($i = count($this->_supportedFormats) - 1; $i >= 0; $i--) {
            yield $i => $this->_supportedFormats[$i];
        }
    }

    /**
     * This method check image format supporting.
     * 
     * @param string $format Image file extension.
     * 
     * @return boolean Whether the image format is supported.
     */
    public function isSupportedFormat($format)
    {
        return in_array($format, $this->_supportedFormats);
    }

    /**
     * This method try to load image from given path 
     * and save it into destination directory.
     * 
     * @param string $imagePath Path to image source.
     * 
     * @return string Path to saved image.
     * @throws \Exception
     */
    public function load($imagePath)
    {
        $imageContent = $this->_getImageContent($imagePath);
        $localImagePath = $this->_getNewImagePath($imagePath);

        @file_put_contents($localImagePath, $imageContent);

        return $localImagePath;
    }

    /**
     * This method remove specified format from the list of supported formats.
     * 
     * @param string $format Image file extension.
     * 
     * @return ImageLoader Current instance.
     */
    public function removeFormat($format)
    {
        foreach ($this->getSupportedFormats() as $key => $value) {
            if ($value == $format) {
                unset($this->_supportedFormats[$key]);
            }
        }

        return $this;
    }

    /**
     * This method set current destination path to specified path.
     * 
     * @param string $destination Destination directory path for store images.
     * 
     * @return ImageLoader Current instance.
     */
    public function setDestination($destination)
    {
        $this->_destination = $destination;

        return $this;
    }

}
