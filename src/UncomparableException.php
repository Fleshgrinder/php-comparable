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
	/** @noinspection MoreThanThreeArgumentsInspection */
	/** @noinspection PhpDocMissingThrowsInspection */
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
	 * @param $message
	 *     pattern for the exception message.
	 * @param $arguments
	 *     to pass to the message formatter. The `type` item is set to the
	 *     {@see Value::getType} result of the `$value` argument, and the
	 *     `reason` to the `$reason` argument.
	 * @throws \Exception
	 *     see {@see Formatter::format} for more information.
	 */
	public static function againstVoid(
		$value,
		string $reason = '',
		string $message = 'Cannot compare {value:?} against void[{reason}?]',
		array $arguments = []
	): self {
		$arguments += ['value' => $value, 'reason' => $reason];

		return new static(Formatter::format($message, $arguments));
	}

	/** @noinspection MoreThanThreeArgumentsInspection */
	/** @noinspection PhpDocMissingThrowsInspection */
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
	 * @param $message
	 *     pattern for the exception message.
	 * @param $arguments
	 *     to pass to the message formatter. The `expected_type` item will be
	 *     set to the {@see Value::getType} result for the `$expected_value`
	 *     argument, and the `actual_type` to the {@see Value::getType} result
	 *     of the `$incompatible_value` argument.
	 * @throws \Exception
	 *     see {@see Formatter::format} for more information.
	 */
	public static function fromIncompatibleTypes(
		$expected,
		$actual,
		string $message = 'Cannot compare {expected:?} with {actual:?}',
		array $arguments = []
	): self {
		$arguments += ['expected' => $expected, 'actual' => $actual];

		return new self(Formatter::format($message, $arguments));
	}

	/** @noinspection MoreThanThreeArgumentsInspection */
	/** @noinspection PhpDocMissingThrowsInspection */
	/**
	 * Construct new uncomparable exception where a comparison can be performed
	 * only if the actual value is of a fixed type.
	 *
	 * @param $expected
	 *     type that the value did not have.
	 * @param $actual
	 *     value that was of a different type.
	 * @param $message
	 *     pattern for the exception message.
	 * @param $arguments
	 *     to pass to the message formatter. The `expected_type` item will be
	 *     set to the `$expected_type` argument, and the `actual_type` to the
	 *     {@see Value::getType} result of the `$incompatible_value` argument.
	 * @throws \Exception
	 *     see {@see Formatter::format} for more information.
	 */
	public static function fromUnexpectedType(
		string $expected,
		$actual,
		string $message = 'Cannot compare {expected} with {actual:?}',
		array $arguments = []
	): self {
		$arguments += ['expected' => $expected, 'actual' => $actual];

		return new static(Formatter::format($message, $arguments));
	}

	/** @noinspection MoreThanThreeArgumentsInspection */
	/** @noinspection PhpDocMissingThrowsInspection */
	/**
	 * Construct new uncomparable exception where a comparison can be performed
	 * only if both, left- and right-hand side, are of a fixed type.
	 *
	 * @param $expected
	 *     type for both left- and right-hand side.
	 * @param $lhs_value
	 *     which might have been the one that was of an unexpected type.
	 * @param $rhs_value
	 *     which might have been the one that was of an unexpected type.
	 * @param $message
	 *     pattern for the exception message.
	 * @param $arguments
	 *     to pass to the message formatter. The `expected_type` item will be
	 *     set to the `$expected_type` argument, and `lhs_type` and `rhs_type`
	 *     to the value of their respective {@see Value::getType} results.
	 * @throws \Exception
	 *     see {@see Formatter::format} for more information.
	 */
	public static function fromUnexpectedTypes(
		string $expected,
		$lhs_value,
		$rhs_value,
		string $message = 'Can compare {expected_types}s only, got {lhs:?} for left- and {rhs:?} for right-hand side',
		array $arguments = []
	): self {
		$arguments += [
			'expected_type' => $expected,
			'lhs'           => $lhs_value,
			'rhs'           => $rhs_value,
		];

		return new static(Formatter::format($message, $arguments));
	}
}
