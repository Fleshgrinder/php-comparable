<?php

declare(strict_types = 1);

namespace Fleshgrinder\Core;

final class ComparableFake implements Comparable {
	use ComparableTrait;

	public $value;

	public function __construct($value = \null) {
		$this->value = $value;
	}
}
