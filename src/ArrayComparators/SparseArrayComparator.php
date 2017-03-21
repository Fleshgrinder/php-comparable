<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

namespace Fleshgrinder\Core\ArrayComparators;

use Fleshgrinder\Core\ArrayComparators\Traits\{ArrayComparatorTrait, DefaultComparatorTrait, SparseArrayComparatorTrait};
use Fleshgrinder\Core\Comparators\Comparator;

/**
 * The **sparse array comparator** compares unidimensional arrays with an
 * unequal amount of items and matching types.
 */
final class SparseArrayComparator implements Comparator {
	use ArrayComparatorTrait, DefaultComparatorTrait, SparseArrayComparatorTrait;
}
