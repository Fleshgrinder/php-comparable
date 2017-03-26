<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

namespace Fleshgrinder\Core\ArrayComparators;

use Fleshgrinder\Core\ArrayComparators\Traits\{
	NullableComparatorTrait, RecursiveArrayComparatorTrait, SparseArrayComparatorTrait
};
use Fleshgrinder\Core\Comparators\Comparator;

/**
 * The **nullable sparse recursive array comparator** compares multidimensional
 * arrays with an unequal amount of elements and matching nullable types.
 */
final class NullableSparseRecursiveArrayComparator implements Comparator {
	use NullableComparatorTrait,
		RecursiveArrayComparatorTrait,
		SparseArrayComparatorTrait;
}
