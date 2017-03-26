<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core;

final class TestClass {}

trait DataTypeProviderTrait {
	public static function provideDataTypes(): array {
		return [
			Value::TYPE_ARRAY    => [[]],
			Value::TYPE_BOOL     => [\true],
			Value::TYPE_FLOAT    => [42.42],
			Value::TYPE_INT      => [42],
			Value::TYPE_NULL     => [\null],
			Value::TYPE_OBJECT   => [(object) []],
			Value::TYPE_RESOURCE => [\fopen('php://memory', 'rb')],
			Value::TYPE_STRING   => ['str'],
			TestClass::CLASS     => [new TestClass],
		];
	}

	public static function provideMismatchingTypes() {
		$data  = [];
		$types = static::provideDataTypes();

		foreach ($types as $l_type => $l_val) {
			foreach ($types as $r_type => $r_val) {
				if ($l_type !== $r_type) {
					$data["{$l_type} <=> {$r_type}"] = [$l_val[0], $r_val[0]];
				}
			}
		}

		return $data;
	}
}
