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
	 * Construct new uncomparable exception where a value cannot be compared
	 * against the void bottom type.
	 *
	 * @param $value
	 *     which cannot be compared against the void bottom type.
	 * @param $reason
	 *     which explains how it was possible to to get to this situation, it
	 *     is not common to interact with a bottom type, and the reason is most
	 *     probably necessary for a user to understand the circumstances.
	 */
	public static function againstVoid($value, string $reason = ''): self {
		/* @noinspection ExceptionsAnnotatingAndHandlingInspection */
		return new static(Formatter::format('Cannot compare {:?} against void[{1}?]', [$value, $reason]));
	}

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

	/* @noinspection PhpDocMissingThrowsInspection */
	/**
	 * Construct new uncomparable exception where a comparison can be performed
	 * only if both, left- and right-hand side, are of a fixed type.
	 *
	 * @param $expected
	 *     type for both left- and right-hand side.
	 * @param $lhs
	 *     value which might have been the one that was of an unexpected type.
	 * @param $rhs
	 *     value which might have been the one that was of an unexpected type.
	 */
	public static function fromUnexpectedTypes(string $expected, $lhs, $rhs): self {
		/* @noinspection ExceptionsAnnotatingAndHandlingInspection */
		return new static(Formatter::format(
			'Can compare {}s only, got {:?} for left- and {:?} for right-hand side',
			[$expected, $lhs, $rhs]
		));
	}
}
