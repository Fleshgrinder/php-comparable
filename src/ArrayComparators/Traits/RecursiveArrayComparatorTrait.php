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
 * The **recursive array comparator trait** provides the algorithm that all
 * recursive array comparator implementations have in common. It provides the
 * same hooks as the unidimensional algorithm.
 *
 * The word “recursive” is actually a misnomer, since the algorithm is not
 * recursive. However, it is customary in PHP to call things that are capable
 * of handling multidimensional data structures recursive. This implementation
 * complies with this tradition in the hope that it is easier for developers
 * to understand the purpose of these comparators.
 */
trait RecursiveArrayComparatorTrait {
	use ArrayComparatorTrait;

	/** @inheritDoc */
	final protected static function doCompare(callable $comparator, array $lhs, array $rhs): int {
		$l_stack = [$lhs];
		$r_stack = [$rhs];

		for ($ptr = 0; $ptr > -1;) {
			$key   = \key($l_stack[$ptr]);
			$l_val = $l_stack[$ptr][$key];

			if (\array_key_exists($key, $r_stack[$ptr]) === \false) {
				return static::handleMissingKey($l_val, $key);
			}

			$r_val = $r_stack[$ptr][$key];

			if (\is_array($l_val) && \is_array($r_val)) {
				if ($l_val === [] && $r_val === []) {
					goto pop_stack;
				}

				$l_len = \count($l_val);
				$r_len = \count($r_val);
				$order = $l_len <=> $r_len;

				if ($order !== Ordering::EQ) {
					return static::handleSizeMismatch($l_len, $r_len, $order);
				}

				// Note that simply appending will not work at this point,
				// because the array is not re-indexed if an element is
				// removed (see unset below). This is why the index must
				// be specified explicitly.
				$l_stack[$ptr + 1] =& $l_stack[$ptr][$key];
				$r_stack[$ptr + 1] =& $r_stack[$ptr][$key];
				++$ptr;

				continue;
			}

			$order = $comparator($l_val, $r_val);
			if ($order !== Ordering::EQ) {
				return $order;
			}

			pop_stack: {
				unset($l_stack[$ptr][$key], $r_stack[$ptr][$key]);

				if (!$l_stack[$ptr]) {
					unset($l_stack[$ptr], $r_stack[$ptr]);
					--$ptr;
				}
			}
		}

		return Ordering::EQ;
	}
}
