<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\Comparators;

/**
 * The **comparator delegate** can be used to quickly create a comparator from
 * a closure or callable without the need to implement a dedicated class or
 * by using an anonymous class (which cannot easily capture surrounding
 * variables).
 */
final class ComparatorDelegate extends Comparator {
	/** @var callable */
	private $delegate;

	/**
	 * Construct new comparator delegate instance.
	 *
	 * @throws \InvalidArgumentException
	 *     if assertions are enabled and the arguments or return type of the
	 *     callable argument do not match the ones from {@see __invoke}.
	 */
	public static function new(callable $delegate): self {
		\assert(self::checkCallable($delegate));

		$self = new self;
		$self->delegate = $delegate;

		return $self;
	}

	/** @throws \InvalidArgumentException */
	public static function checkCallable(callable $delegate): bool {
		$reflector = new \ReflectionFunction($delegate);

		if (\count($reflector->getParameters()) !== 2) {
			throw new \InvalidArgumentException(
				'Delegate must take exactly 2 arguments, see ' . __CLASS__ . '::__invoke() for exact signature'
			);
		}

		$return_type = $reflector->getReturnType();
		$return_type = $return_type ? $return_type->__toString() : 'none';
		if ($return_type !== 'int') {
			throw new \InvalidArgumentException(
				"Delegate return type must be of type int, got {$return_type}, see " . __CLASS__ . '::__invoke() for exact signature'
			);
		}

		return \true;
	}

	/** @inheritDoc */
	public function __invoke($lhs, $rhs): int {
		return ($this->delegate)($lhs, $rhs);
	}
}
