<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\ArrayComparators\Traits;

use Fleshgrinder\Core\Ordering;

/**
 * The **sparse array comparator trait** returns the difference of size
 * mismatches, and considers the left-hand side to be greater if an element is
 * missing from the right-hand side.
 *
 * @mixin \Fleshgrinder\Core\ArrayComparators\Traits\ArrayComparatorTrait
 */
trait SparseArrayComparatorTrait {
	/** @inheritDoc */
	final protected static function handleSizeMismatch(int $l_len, int $r_len, int $order): int {
		return $order;
	}

	/** @inheritDoc */
	final protected static function handleMissingKey($l_val, $key): int {
		return Ordering::GT;
	}
}
