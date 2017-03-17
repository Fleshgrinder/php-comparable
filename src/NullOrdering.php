<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core;

/**
 * The **null ordering** may be used to replace an {@see Ordering} while
 * allowing safe method chaining.
 */
final class NullOrdering extends Ordering {
	/**
	 * Value that is returned from the {@see toInt} conversion of a null
	 * ordering. {@see Ordering} always returns a value between [-1, 1] and
	 * returning 0 would mean that null orderings are equal to actually equal
	 * orderings without the ability to distinguish them at all.
	 *
	 * The -2 value ensures that null orderings always sort to the bottom of
	 * any sorting since they are even less than {@see Less}.
	 *
	 * Implementers of other comparable null objects are encouraged to use
	 * this constant in their {@see toInt} methods. Note well that throwing
	 * of an exception (e.g. like {@see \BadMethodCallException} is not
	 * permitted. The conversion method **MUST** return a meaningful value at
	 * all times.
	 *
	 * @see toInt
	 */
	const NIL = -2;

	/** Construct new null ordering instance. */
	public static function new(int $order = self::NIL): parent {
		return new static($order);
	}

	/** @inheritDoc */
	public function isLess(): bool {
		return \false;
	}

	/** @inheritDoc */
	public function isLessOrEqual(): bool {
		return \false;
	}

	/** @inheritDoc */
	public function isEqual(): bool {
		return \false;
	}

	/** @inheritDoc */
	public function isGreaterOrEqual(): bool {
		return \false;
	}

	/** @inheritDoc */
	public function isGreater(): bool {
		return \false;
	}

	/** @inheritDoc */
	public function then(Ordering $other): parent {
		return $other;
	}

	/** @inheritDoc */
	public function thenWith(callable $f): parent {
		return $f();
	}

	/** @inheritDoc */
	public function toInt(): int {
		return static::NIL;
	}

	/** @inheritDoc */
	public function toReverse(): parent {
		return $this;
	}

	/** @inheritDoc */
	protected function doCompareTo($other): parent {
		return $this;
	}
}
