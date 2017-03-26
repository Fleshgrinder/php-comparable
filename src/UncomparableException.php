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
	/**
	 * Construct new uncomparable exception instance.
	 *
	 * @param $message
	 *     pattern to forward to the formatter.
	 * @param $arguments
	 *     arguments to pass to the formatter.
	 * @throws \Exception
	 * @throws \Fleshgrinder\Core\Formatter\InvalidArgumentException
	 * @throws \Fleshgrinder\Core\Formatter\MissingPlaceholderException
	 */
	public static function new(string $message, array $arguments = [], \Throwable $cause = \null): self {
		return new static(Formatter::format($message, $arguments), 0, $cause);
	}

	/* @noinspection PhpDocMissingThrowsInspection */
	/**
	 * Construct new uncomparable exception where a comparison of two values
	 * cannot be compared because they are of incompatible types.
	 */
	public static function fromIncompatibleTypes($expected, $actual, \Throwable $cause = \null): self {
		/* @noinspection ExceptionsAnnotatingAndHandlingInspection */
		return static::new('Cannot compare {:?} with {:?}', [$expected, $actual], $cause);
	}
}
