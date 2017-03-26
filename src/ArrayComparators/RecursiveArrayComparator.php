<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

namespace Fleshgrinder\Core\ArrayComparators;

use Fleshgrinder\Core\ArrayComparators\Traits\{
	DefaultComparatorTrait, RecursiveArrayComparatorTrait, StrictArrayComparatorTrait
};
use Fleshgrinder\Core\Comparators\Comparator;

/**
 * The **recursive array comparator** compares multidimensional arrays with an
 * equal amount of elements and matching types.
 */
final class RecursiveArrayComparator implements Comparator {
	use DefaultComparatorTrait,
		RecursiveArrayComparatorTrait,
		StrictArrayComparatorTrait;
}
