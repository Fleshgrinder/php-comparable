<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\ArrayComparators\Traits;

use Fleshgrinder\Core\{
	Disenchant, Ordering, Uncloneable, UncomparableException
};
use Fleshgrinder\Core\Comparators\{
	Comparator, ComparatorTrait
};

/**
 * The **array comparator trait** provides the algorithm that all array
 * comparator implementations have in common. Multiple hooks are provided to
 * alter the behavior of the algorithm at various stages.
 */
trait ArrayComparatorTrait {
	use ComparatorTrait, Disenchant, Uncloneable;

	/** @var Comparator which is used to compare individual items. */
	private $comparator;

	/**
	 * Handle the case where two arrays have different sizes.
	 *
	 * @throws \Fleshgrinder\Core\UncomparableException
	 *     may be thrown if this concrete implementation requires both arrays
	 *     to be of the same size.
	 */
	abstract protected static function handleSizeMismatch(int $l_len, int $r_len, int $order): int;

	/**
	 * Handle the case where a key from the left-hand side is missing from the
	 * right.
	 *
	 * @throws \Fleshgrinder\Core\UncomparableException
	 *     may be thrown if this concrete implementation requires both arrays
	 *     to have the same keys set.
	 */
	abstract protected static function handleMissingKey($l_val, $key): int;

	/**
	 * Perform the actual comparison of the individual items.
	 *
	 * @throws \Fleshgrinder\Core\UncomparableException
	 *     may be thrown if this concrete implementation requires both arrays
	 *     to have the same keys set.
	 */
	protected static function doCompare(callable $comparator, array $lhs, array $rhs): int {
		foreach ($lhs as $key => $l_val) {
			if (\array_key_exists($key, $rhs) === \false) {
				return static::handleMissingKey($l_val, $key);
			}

			$order = $comparator($l_val, $rhs[$key]);
			if ($order !== Ordering::EQ) {
				return $order;
			}
		}

		return Ordering::EQ;
	}

	/** @inheritDoc */
	final public function __invoke($lhs, $rhs): int {
		if ($lhs === [] && $rhs === []) {
			return Ordering::EQ;
		}

		if (\is_array($lhs) === \false || \is_array($rhs) === \false) {
			/* @noinspection ExceptionsAnnotatingAndHandlingInspection */
			throw UncomparableException::new(
				'Can compare arrays only, got {:?} for left- and {:?} for right-hand side',
				[$lhs, $rhs]
			);
		}

		$l_len = \count($lhs);
		$r_len = \count($rhs);
		$order = $l_len <=> $r_len;

		return $order === Ordering::EQ
			? static::doCompare($this->comparator, $lhs, $rhs)
			: static::handleSizeMismatch($l_len, $r_len, $order);
	}
}
