<?php

namespace Fleshgrinder\Core;

/**
 * Custom doCompareTo method to circumvent the default trait implementation
 * which uses the ArrayComparator and leads to recursion in tests that tests
 * that particular implementation.
 */
class ComparableFake extends ComparableTraitFake {

	protected function doCompareTo($other)/*71: ?Ordering*/ {
		if ($other instanceof $this) {
			if ($this->value < $other->value) {
				return Ordering::Less();
			}

			if ($this->value > $other->value) {
				return Ordering::Greater();
			}

			return Ordering::Equal();
		}

		return \null;
	}

}
