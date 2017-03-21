<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\Comparators;

use Fleshgrinder\Core\Uncloneable;

/** The **reverse comparator** can be used to reverse any comparator. */
final class ReverseComparator implements Comparator {
	use Uncloneable;

	/** @var Comparator */
	private $comparator;

	/** Reverse the given comparator. */
	public function __construct(Comparator $comparator) {
		$this->comparator = $comparator;
	}

	/**
	 * Reverse the given comparator delegate.
	 *
	 * @throws \InvalidArgumentException
	 *     see {@see ComparatorDelegate::fromCallable} for more information.
	 */
	public static function fromCallable(callable $delegate): self {
		return new static(new ComparatorDelegate($delegate));
	}

	/** @inheritDoc */
	public function __invoke($lhs, $rhs): int {
		return ($this->comparator)($rhs, $lhs);
	}
}
