<?php

namespace TestPackage;

/**
 * Class for localization.
 *
 * @package TestPackage
 * @author  Vadym Chenin <vchenin@meta.ua>
 */
class Localization
{

    private $_lang;
    private $_langDirPath;
    private $_messages;

    public function __construct($langDirPath, $lang = 'en') {
        $this->_lang = $lang;
        $this->_langDirPath = $langDirPath;
        $this->_setMessages();
    }

    private function _setMessages() {
        $langFile = $this->_langDirPath . DIRECTORY_SEPARATOR . $this->_lang . '.php';
        $this->_messages = require $langFile;
    }

    public function getLanguage() {
        return $this->_lang;
    }

    public function msg($msgId, $args = []) {
        if (!is_array($args)) {
            $args = [$args];
        }

        $msg = @$this->_messages[$msgId];

        if (empty($msg)) {
            $msg = "Message: $msgId.";
            if (!empty($args)) {
                $msg .= " Arguments: '" . implode("', '", $args) . "'.";
            }
        }

        return vsprintf($msg, $args);
    }

    public function setLanguage($lang) {
        $this->_lang = $lang;
        $this->_setMessages();
    }

}
