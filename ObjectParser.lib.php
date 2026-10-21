<?php
class ObjectParser extends Library {

    /**
     * Implement GetInstance() method.
     *
     * This is required for PHP < 5.3 compatibility ( no late static binding )
     *
     * @param string $className class name to instantiate
     *
     * @return object
     */
    public static function GetInstance($className = __CLASS__) {
        /**
         * Call parent method with current class name as parameter.
         */
        return parent::_GetInstance($className);
    }

    public function ParseObject($object) {

        if (is_object($object)) {

            unset($object -> comment);

            $object = get_object_vars($object);
        }

        if (is_array($object)) {
            // more processing if required

            return array_map(array($this, __FUNCTION__), $object);
        }

        $object = $this -> _TrimData($object);

        return $object;

    }

    private function _TrimData($data) {

        return is_array($data) ? array_map(array($this, __FUNCTION__), $data) : trim($data);
    }

}
?>