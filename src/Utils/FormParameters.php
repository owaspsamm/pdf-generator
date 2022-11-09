<?php
/**
 * This is automatically generated file using the Codific Prototizer
 *
 * PHP version 8
 *
 * @category PHP
 * @package  Admin
 * @author   CODIFIC <info@codific.com>
 * @link     http://codific.com
 */

declare(strict_types=1);

namespace App\Utils;


final class FormParameters
{

    /** @var \StdClass|null $additionalFormVars Any additional properties which have to be sent to the form */
    public ?\StdClass $additionalFormVars = null;

    /**
     * FormParameters constructor.
     */
    public function __construct()
    {
        $this->additionalFormVars = new \StdClass();
    }

    /**
     * Get array with only the modified properties
     * @return array
     */
    public function getModifiedPropertiesArray()
    {

        try {
            foreach (get_object_vars($this->additionalFormVars) as $key => $value) {
                if (!property_exists($this, $key)) {
                    $this->{$key} = $value;
                }
            }
            $diff = $this->arrayRecursiveDiff(get_object_vars($this), (new \ReflectionClass($this))->getDefaultProperties());
            if (isset($diff['additionalFormVars'])) {
                unset($diff['additionalFormVars']);
            }

            return $diff;
        } catch (\ReflectionException $e) {
            return [];
        }
    }

    /**
     * Recursive diff between two arrays
     * @param array $firstArray
     * @param array $secondArray
     * @return array
     */
    private function arrayRecursiveDiff(array $firstArray, array $secondArray): array
    {
        $result = [];
        foreach ($firstArray as $key => $value) {
            if (array_key_exists($key, $secondArray)) {
                if (is_array($value)) {
                    $recursiveDiff = $this->arrayRecursiveDiff($value, $secondArray[$key]);
                    if (count($recursiveDiff) > 0) {
                        $result[$key] = $recursiveDiff;
                    }
                } else {
                    if ($value != $secondArray[$key]) {
                        $result[$key] = $value;
                    }
                }
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

}