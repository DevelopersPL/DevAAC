<?php
/**
 * Developer: Daniel
 * Date: 2/19/14
 * Time: 2:12 PM
 */

namespace DevAAC\Helpers;

use Illuminate\Support\Contracts\JsonableInterface;
use Illuminate\Support\Contracts\ArrayableInterface;


class DateTime extends \DateTime implements \JsonSerializable, JsonableInterface {

    public function __toString()
    {
        return $this->format(DateTime::ISO8601);
    }

    public function jsonSerialize()
    {
        return $this->format(DateTime::ISO8601);
    }

    public function toJson($options = 0)
    {
        return json_encode($this->format(DateTime::ISO8601, $options));
    }
} 