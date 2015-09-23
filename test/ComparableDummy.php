<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2015 Richard Fussenegger
 * @license MIT
 */

namespace Fleshgrinder\Core;

/**
 * Defines the comparable dummy class.
 *
 * This class is provided for tests that want to verify that something accepts or is able to handle instances that
 * implement the {@see Comparable} interface. This class does not contain any logic and the integer that is returned by
 * the compare method is simply a public property that can be altered by anyone.
 *
 * This class is final because nobody should ever extend it, instead implement the {@see Stringable} interface.
 */
final class ComparableDummy implements Comparable {

    /**
     * The value that should be returned by the comparable method.
     *
     * @var mixed
     */
    public $compareTo;

    /**
     * Construct new comparable dummy instance.
     *
     * @param mixed $compareTo [optional]
     *     Value that should be returned by this instance.
     */
    public function __construct($compareTo = 0) {
        $this->compareTo = $compareTo;
    }

    /**
     * @inheritDoc
     */
    public function compareTo($value) {
        return $this->compareTo;
    }

}
