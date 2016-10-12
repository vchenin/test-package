<?php

namespace TestPackage;

/**
 * Class for loading image from given path to destination directory.
 * By default supported image formats are: gif, jpg, png.
 * 
 * @author Vadym Chenin <vchenin@meta.ua>
 */
class ImageLoader {

    private $_destination;
    private $_supportedFormats = ['gif', 'jpg', 'png'];


    /**
     * Constructor.
     * 
     * @param string $dirPath Destination directory path for store images.
     */
    public function __construct($dirPath) {
        $this->_destination = $dirPath;
    }

    /**
     * This method add new format to the list of supported formats.
     * 
     * @param string $format Image file extension.
     * @return ImageLoader Current instance.
     */
    public function addFormat($format) {
        if (!$this->isSupportedFormat($format)) {
            array_push($this->_supportedFormats, $format);
        }
        return $this;
    }

    /**
     * 
     * @return string Current destination directory path.
     */
    public function getDestination() {
        return $this->_destination;
    }
    
    /**
     * Iterator which provide supported formats.
     */
    public function getFormats() {
        for ($i = count($this->_supportedFormats) - 1; $i >= 0; $i--) {
            yield $i => $this->_supportedFormats[$i];
        }
    }
    
    /**
     * This method check image format supporting.
     * 
     * @param type $format Image file extension.
     * @return boolean Whether the image format is supported.
     */
    public function isSupportedFormat($format) {
        return in_array($format, $this->_supportedFormats);
    }

    /**
     * This method try to load image from given path and save it into destination directory.
     * 
     * @param type $imagePath Path to image source.
     * @return ImageLoader Current instance.
     * @throws \Exception
     */
    public function load($imagePath) {
        $imageExt = pathinfo($imagePath, PATHINFO_EXTENSION);
        if (!$this->isSupportedFormat($imageExt)) {
            throw new \Exception("File with extension '$imageExt' is not supported. Supported formats are: " . implode(', ', $this->_supportedFormats) . ".");
        }

        $remoteImageContent = file_get_contents($imagePath);
        if (!$remoteImageContent) {
            throw new \Exception("Failed load image from path ($imagePath).");
        }

        $dirPath = $this->_destination;
        if (!is_dir($dirPath) && !(mkdir($dirPath))) {
            throw new \Exception("Destination directory '$dirPath' is not accessible.");
        }

        $imageName = pathinfo($imagePath, PATHINFO_BASENAME);
        $localImagePath = $dirPath . DIRECTORY_SEPARATOR . $imageName;
        if (file_exists($localImagePath)) {
            throw new \Exception("File with the same name '$imageName' already exists in destination directory '$dirPath'.");
        }
        file_put_contents($localImagePath, $remoteImageContent);

        return $this;
    }

    /**
     * This method remove specified format from the list of supported formats.
     * 
     * @param type $format Image file extension.
     * @return ImageLoader Current instance.
     */
    public function removeFormat($format) {
        foreach ($this->getFormats() as $key => $value) {
            if ($value == $format) {
                unset($this->_supportedFormats[$key]);
            }
        }
        return $this;
    }

    /**
     * 
     * @param type $destination Destination directory path for store images.
     * @return ImageLoader Current instance.
     */
    public function setDestination($destination) {
        $this->_destination = $destination;

        return $this;
    }

}