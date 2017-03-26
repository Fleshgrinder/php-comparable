<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

namespace Fleshgrinder\Core\ArrayComparators;

use Fleshgrinder\Core\ArrayComparators\Traits\{
	ArrayComparatorTrait, DefaultComparatorTrait, StrictArrayComparatorTrait
};
use Fleshgrinder\Core\Comparators\Comparator;

/**
 * The **array comparator** compares unidimensional arrays with an equal amount
 * of elements and matching types.
 */
final class ArrayComparator implements Comparator {
	use ArrayComparatorTrait,
		DefaultComparatorTrait,
		StrictArrayComparatorTrait;
}
