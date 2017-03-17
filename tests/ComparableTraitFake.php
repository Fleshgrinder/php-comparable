<?php

namespace Fleshgrinder\Core;

class ComparableTraitFake implements Comparable {

	use ComparableTrait;

	public $value;

	public function __construct($value = \null) {
		$this->value = $value;
	}

}
