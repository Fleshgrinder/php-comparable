<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\ArrayComparators\Traits;

use Fleshgrinder\Core\UncomparableException;

/**
 * The **strict array comparator trait** will throw an exception if a size
 * mismatch is encountered, or a key from the left-hand side is missing from
 * the right-hand side.
 *
 * @mixin \Fleshgrinder\Core\ArrayComparators\Traits\ArrayComparatorTrait
 */
trait StrictArrayComparatorTrait {
	/** @inheritDoc */
	final protected static function handleSizeMismatch(int $l_len, int $r_len, int $order): int {
		/* @noinspection ExceptionsAnnotatingAndHandlingInspection */
		throw UncomparableException::new(
			'Cannot compare sparse arrays, got {} on left- and {} on right-hand side',
			[
				"{$l_len} element" . ($l_len === 1 ? '' : 's'),
				"{$r_len} element" . ($r_len === 1 ? '' : 's'),
			]
		);
	}

	/** @inheritDoc */
	final protected static function handleMissingKey($l_val, $key): int {
		/* @noinspection ExceptionsAnnotatingAndHandlingInspection */
		throw UncomparableException::new(
			'Cannot compare {:?} against void, key `{#p}` missing from right-hand side',
			[$l_val, $key]
		);
	}
}
