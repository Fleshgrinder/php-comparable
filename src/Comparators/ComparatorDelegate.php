<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\Comparators;

use Fleshgrinder\Core\{
	Immutable, UncomparableException
};

/**
 * The **comparator delegate** can be used to quickly create a comparator from
 * a closure or callable without the need to implement a dedicated class or
 * by using an anonymous class (which cannot easily capture surrounding
 * variables).
 */
final class ComparatorDelegate implements Comparator {
	use Immutable;

	/** @var callable */
	private $delegate;

	/**
	 * Construct new comparator delegate instance.
	 *
	 * Throws an {@see \AssertionError} if assertions are active and the given
	 * delegate does not comply with the required method signature.
	 */
	public static function new(callable $delegate): self {
		\assert(
			(static function ($delegate) {
				$reflector = new \ReflectionFunction($delegate);

				return \count($reflector->getParameters()) === 2 && $reflector->getReturnType() == 'int';
			})($delegate),
			'Delegate signature is invalid, see ' . __CLASS__ . '::__invoke() for required signature'
		);

		$self = new static;

		$self->delegate = $delegate;

		return $self;
	}

	/** @inheritDoc */
	public function __invoke($lhs, $rhs): int {
		try {
			return ($this->delegate)($lhs, $rhs);
		}
		catch (\TypeError $cause) {
			if ($cause->getTrace()[0]['file'] !== __FILE__) {
				throw $cause;
			}

			$reflector = new \ReflectionFunction($this->delegate);
			$parameter = $reflector->getParameters()[0] ?? \null;
			$type      = \null;

			if ($parameter) {
				$type = $parameter->getClass();
				$type = $type === \null ? $parameter->getType() : $type->getName();
			}

			if ($parameter === \null || $type === \null) {
				throw $cause;
			}

			/* @noinspection ExceptionsAnnotatingAndHandlingInspection */
			throw UncomparableException::new(
				'Expected {} but got {:?} on left- and {:?} on right-hand side',
				[$type, $lhs, $rhs],
				$cause
			);
		}
	}
}
