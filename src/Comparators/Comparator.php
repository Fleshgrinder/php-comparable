<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\Comparators;

use Fleshgrinder\Core\Ordering;

/** Foundation for custom comparators. */
abstract class Comparator {
	/**
	 * Compare the left- with the right-hand side.
	 *
	 * @throws \Fleshgrinder\Core\UncomparableException
	 *     if the left-hand side cannot be compared with the right-hand side.
	 */
	abstract public function __invoke($lhs, $rhs): int;

	/**
	 * Compare the left- with the right-hand side.
	 *
	 * @throws \Fleshgrinder\Core\UncomparableException
	 *     if the left-hand side cannot be compared with the right-hand side.
	 */
	public static function compare($lhs, $rhs): Ordering {
		return Ordering::new((new static)($lhs, $rhs));
	}
}
