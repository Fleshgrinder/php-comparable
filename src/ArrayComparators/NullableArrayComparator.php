<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

namespace Fleshgrinder\Core\ArrayComparators;

use Fleshgrinder\Core\ArrayComparators\Traits\{
	ArrayComparatorTrait, NullableComparatorTrait, StrictArrayComparatorTrait
};
use Fleshgrinder\Core\Comparators\Comparator;

/**
 * The **nullable array comparator** compares unidimensional arrays with an
 * equal amount of elements and matching nullable types.
 */
final class NullableArrayComparator implements Comparator {
	use ArrayComparatorTrait,
		NullableComparatorTrait,
		StrictArrayComparatorTrait;
}
