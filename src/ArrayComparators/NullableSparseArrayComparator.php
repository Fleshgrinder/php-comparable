<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

namespace Fleshgrinder\Core\ArrayComparators;

use Fleshgrinder\Core\ArrayComparators\Traits\{
	ArrayComparatorTrait, NullableComparatorTrait, SparseArrayComparatorTrait
};
use Fleshgrinder\Core\Comparators\Comparator;

/**
 * The **sparse nullable array comparator** compares unidimensional arrays with
 * an unequal amount of elements and matching nullable types.
 */
final class NullableSparseArrayComparator implements Comparator {
	use ArrayComparatorTrait,
		NullableComparatorTrait,
		SparseArrayComparatorTrait;
}
