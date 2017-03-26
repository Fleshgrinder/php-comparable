<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\Comparators;

use Fleshgrinder\Core\Immutable;

/** The **reverse comparator** can be used to reverse any comparator. */
final class ReverseComparator implements Comparator {
	use Immutable;

	/** @var Comparator */
	private $comparator;

	/** Reverse the given comparator. */
	public static function new(Comparator $comparator): self {
		$self = new static;

		$self->comparator = $comparator;

		return $self;
	}

	/**
	 * Reverse the given comparator delegate.
	 *
	 * @throws \InvalidArgumentException
	 *     see {@see ComparatorDelegate::fromCallable} for more information.
	 */
	public static function fromCallable(callable $delegate): self {
		return static::new(ComparatorDelegate::new($delegate));
	}

	/** @inheritDoc */
	public function __invoke($lhs, $rhs): int {
		return ($this->comparator)($rhs, $lhs);
	}
}
