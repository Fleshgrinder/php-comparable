<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

namespace Fleshgrinder\Core\ArrayComparators;

use Fleshgrinder\Core\ArrayComparators\Traits\{
	DefaultComparatorTrait, RecursiveArrayComparatorTrait, SparseArrayComparatorTrait
};
use Fleshgrinder\Core\Comparators\Comparator;

/**
 * The **sparse recursive array comparator** compares multidimensional arrays
 * with an unequal amount of elements and matching types.
 */
final class SparseRecursiveArrayComparator implements Comparator {
	use DefaultComparatorTrait,
		RecursiveArrayComparatorTrait,
		SparseArrayComparatorTrait;
}
