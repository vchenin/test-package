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
     * @return Iterator Description
     */
    public function getFormats()
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
     * @return ImageLoader Current instance.
     * @throws \Exception
     */
    public function load($imagePath)
    {
        $imageExt = pathinfo($imagePath, PATHINFO_EXTENSION);
        if (!$this->isSupportedFormat($imageExt)) {
            $msg = "File with extension '$imageExt' is not supported.";
            $msg .= " Supported formats are: ";
            $msg .= implode(', ', $this->_supportedFormats) . ".";
            throw new \Exception($msg);
        }

        $remoteImageContent = file_get_contents($imagePath);
        if (!$remoteImageContent) {
            $msg = "Failed load image from path ($imagePath).";
            throw new \Exception($msg);
        }

        $dirPath = $this->_destination;
        if ((!is_dir($dirPath) && !(mkdir($dirPath))) || !is_writable($dirPath)) {
            $msg = "Destination directory '$dirPath' is not accessible.";
            throw new \Exception($msg);
        }

        $imageName = pathinfo($imagePath, PATHINFO_BASENAME);
        $localImagePath = $dirPath . DIRECTORY_SEPARATOR . $imageName;
        if (file_exists($localImagePath)) {
            $msg = "File with the same name '$imageName' already exists";
            $msg .= " in destination directory '$dirPath'.";
            throw new \Exception($msg);
        }
        file_put_contents($localImagePath, $remoteImageContent);

        return $this;
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
        foreach ($this->getFormats() as $key => $value) {
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
