<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\Comparators;

use Fleshgrinder\Core\Uncloneable;

/**
 * The **comparator delegate** can be used to quickly create a comparator from
 * a closure or callable without the need to implement a dedicated class or
 * by using an anonymous class (which cannot easily capture surrounding
 * variables).
 */
final class ComparatorDelegate implements Comparator {
	use Uncloneable;

	/** @var callable */
	private $delegate;

	/**
	 * Construct new comparator delegate instance.
	 *
	 * Throws an {@see \AssertionError} if assertions are active and the given
	 * delegate does not comply with the required method signature:
	 *
	 * ```
	 * $delegate = function ($lhs, $rhs): int { }
	 * ```
	 */
	public function __construct(callable $delegate) {
		\assert(
			(static function () use ($delegate) {
				$reflector = new \ReflectionFunction($delegate);

				return \count($reflector->getParameters()) === 2 && $reflector->getReturnType() == 'int';
			})(),
			'Delegate signature is invalid, see ' . __CLASS__ . '::__invoke() for required signature'
		);

		$this->delegate = $delegate;
	}

	/** @inheritDoc */
	public function __invoke($lhs, $rhs): int {
		return ($this->delegate)($lhs, $rhs);
	}
}
