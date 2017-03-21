<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

namespace Fleshgrinder\Core\Comparators;

/** Contract for custom comparators. */
interface Comparator {
	/**
	 * Compare the left- with the right-hand side.
	 *
	 * @throws \Fleshgrinder\Core\UncomparableException
	 *     if the left-hand side cannot be compared with the right-hand side.
	 */
	function __invoke($lhs, $rhs): int;
}
