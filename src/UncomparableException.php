<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core;

/**
 * The **uncomparable exception** is thrown if an implementor of
 * {@see Comparable} is unable to compare a given value with itself.
 *
 * ## Wording
 * > Two or more things that can’t be compared with each other are
 * > uncomparable. Something that is so good that it is beyond comparison is
 * > incomparable. Some dictionaries don’t list uncomparable, and your spell
 * > check might say it’s wrong, but it’s a perfectly good, useful word. It
 * > fills a role not conventionally filled by incomparable.
 * >
 * > --- [Grammarist](http://grammarist.com/usage/incomparable-uncomparable/)
 */
class UncomparableException extends \InvalidArgumentException {
	/* @noinspection PhpDocMissingThrowsInspection */
	/**
	 * Construct new uncomparable exception where a comparison of two values
	 * cannot be compared because they are of incompatible types.
	 *
	 * This is very similar to {@see fromUnexpectedType} and
	 * {@see fromUnexpectedTypes}, the difference is that the expected type is
	 * defined by a reference value, and not by a fixed specific type.
	 *
	 * @param $expected
	 *     type that the actual value did not have.
	 * @param $actual
	 *     value that was of a different type.
	 */
	public static function fromIncompatibleTypes($expected, $actual): self {
		/* @noinspection ExceptionsAnnotatingAndHandlingInspection */
		return new self(Formatter::format('Cannot compare {:?} with {:?}', [$expected, $actual]));
	}

	/* @noinspection PhpDocMissingThrowsInspection */
	/**
	 * Construct new uncomparable exception where a comparison can be performed
	 * only if the actual value is of a fixed type.
	 *
	 * @param $expected
	 *     type that the value did not have.
	 * @param $actual
	 *     value that was of a different type.
	 */
	public static function fromUnexpectedType(string $expected, $actual): self {
		/* @noinspection ExceptionsAnnotatingAndHandlingInspection */
		return new static(Formatter::format('Cannot compare {} with {:?}', [$expected, $actual]));
	}
}
