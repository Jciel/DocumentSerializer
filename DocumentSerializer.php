<?php

namespace namespace\your\traits;

use stdClass;

trait DocumentSerializer
{
    /**
     * Convert Doctrine\ODM Document to Array
     *
     * @return array
     */
    public function toArray()
    {
        $document = $this->toStdClass();
        return get_object_vars($document);
    }

    /**
     * Convert Doctrine\ODM Document to JSON
     *
     * @return string
     */
    public function toJSON()
    {
        $document = $this->toStdClass();
        return json_encode($document, JSON_FORCE_OBJECT);
    }

    /**
     * Convert Doctrine\ODM Document to plain simple stdClass
     *
     * @return stdClass
     */
    private function toStdClass()
    {
        $document = new stdClass();
        $gettersMethods = $this->findGetters();
        array_walk($gettersMethods, function ($getterMethod) use ($document) {
            $property = lcfirst(substr($getterMethod, 3));
            $value = $this->$getterMethod();
            $document->$property = $this->formatValue($value);
        });
        return $document;
    }

    private function findGetters(): array
    {
        $methods = get_class_methods(get_class($this));
        $setters = array_filter($methods, function (string $method) {
            if (strpos($method, 'get') === 0) {
                return true;
            }
            return false;
        });
        return $setters ;
    }

    private function formatValue($value)
    {
        if (is_scalar($value) || gettype($value) === "array") {
            return $value;
        } elseif (in_array(__TRAIT__, class_uses(get_class($value)))) {
//            If the object uses this trait
            return $value->toStdClass();
        } elseif (is_a($value, 'Doctrine\ODM\MongoDB\PersistentCollection')) {
//            If it's a collection, format each value
            return array_map(function ($v) {
                $this->formatValue($v);
            }, $value);
        } elseif (is_a($value, 'DateTime')) {
//            If it's a Date, convert to unix timestamp
            return $value->getTimestamp();
        } else {
//            Otherwise leave a note that this type is not formatted
//            So that I can add formatting for this missed class
            return 'Not formatted in DocumentSerializer: '. get_class($value);
        }
    }
}
