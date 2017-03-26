<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

namespace Fleshgrinder\Core\ArrayComparators;

use Fleshgrinder\Core\ArrayComparators\Traits\{
	NullableComparatorTrait, RecursiveArrayComparatorTrait, StrictArrayComparatorTrait
};
use Fleshgrinder\Core\Comparators\Comparator;

/**
 * The **nullable recursive array comparator** compares multidimensional arrays
 * with an equal amount of elements and matching nullable types.
 */
final class NullableRecursiveArrayComparator implements Comparator {
	use NullableComparatorTrait,
		RecursiveArrayComparatorTrait,
		StrictArrayComparatorTrait;
}
