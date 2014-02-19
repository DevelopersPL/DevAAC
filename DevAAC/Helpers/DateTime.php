<?php
/**
 * Developer: Daniel
 * Date: 2/19/14
 * Time: 2:12 PM
 */

namespace DevAAC\Helpers;


class DateTime extends \DateTime implements \JsonSerializable {

    public function __toString()
    {
        return $this->format(DateTime::ISO8601);
    }

    public function jsonSerialize()
    {
        return $this->format(DateTime::ISO8601);
    }

    // TODO: Eloquent's toJson does not respect that
} 