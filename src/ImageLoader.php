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

    /**
     * Constructor.
     * 
     * @param string $dirPath Destination directory path for store images.
     */
    public function __construct($dirPath)
    {
        $this->_destination = $dirPath;
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
     * This method check accessibility of destination directory.
     * 
     * @return string Current path to destination directory.
     * @throws \Exception
     */
    private function _checkDestinationDirectory()
    {
        $dirPath = $this->getDestination();

        if ((!is_dir($dirPath) && !(@mkdir($dirPath))) || !is_writable($dirPath)) {
            $msg = "Destination directory '$dirPath' is not accessible.";
            throw new \Exception($msg);
        }

        return $dirPath;
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
            $msg = "File with extension '$imageExt' is not supported.";
            $msg .= " Supported formats are: ";
            $msg .= implode(', ', $this->_supportedFormats) . ".";
            throw new \Exception($msg);
        }

        if (!$remoteImageContent = @file_get_contents($imagePath)) {
            $msg = "Failed load image from path '$imagePath'.";
            throw new \Exception($msg);
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
            $msg = "File with the same name '$imageName' already exists";
            $msg .= " in destination directory '$dirPath'.";
            throw new \Exception($msg);
        }

        return $localImagePath;
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
