<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\Comparators;

use Fleshgrinder\Core\Ordering;

/**
 * The **comparator trait** provides a single static method to directly invoke
 * the custom comparator.
 *
 * @mixin \Fleshgrinder\Core\Comparators\Comparator
 */
trait ComparatorTrait {
	/**
	 * Compare the left- with the right-hand side.
	 *
	 * @throws \Fleshgrinder\Core\UncomparableException
	 *     if the left-hand side cannot be compared with the right-hand side.
	 */
	public static function compare($lhs, $rhs): Ordering {
		return new Ordering((new static)($lhs, $rhs));
	}
}
