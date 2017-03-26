<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\ArrayComparators\Traits;

use Fleshgrinder\Core\Comparators\DefaultComparator;

/**
 * The **default comparator trait** can be used together with the
 * {@see ArrayComparatorTrait} to use a {@see DefaultComparator} for the actual
 * comparison of the input arrays elements.
 *
 * @mixin \Fleshgrinder\Core\ArrayComparators\Traits\ArrayComparatorTrait
 */
trait DefaultComparatorTrait {
	/** Construct new array comparator instance. */
	final protected function __construct() {
		$this->comparator = DefaultComparator::new();
	}
}
