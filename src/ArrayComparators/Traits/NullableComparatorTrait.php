<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\ArrayComparators\Traits;

use Fleshgrinder\Core\Comparators\NullableComparator;

/**
 * The **nullable comparator trait** can be used together with the
 * {@see ArrayComparatorTrait} to use a {@see NullableComparator} for the actual
 * comparison of the individual items of the input arrays.
 *
 * @mixin \Fleshgrinder\Core\ArrayComparators\Traits\ArrayComparatorTrait
 */
trait NullableComparatorTrait {
	/** Construct new array comparator instance. */
	public function __construct() {
		$this->comparator = new NullableComparator;
	}
}
