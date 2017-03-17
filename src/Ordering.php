<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core;

class Ordering implements Comparable {
	use ComparableTrait, Immutable;

	/** A value is equal to another. */
	const EQ = 0;

	/** A value is greater than another. */
	const GT = 1;

	/** A value is less than another. */
	const LT = -1;

	/** @var int */
	private $order;

	final protected function __construct(int $order) {
		$this->order = $order;
	}

	/** Construct new ordering instance. */
	public static function new(int $order): self {
		return new self($order);
	}

	/** An ordering where a compared value is less than another. */
	public static function Less(): self {
		return new self(self::LT);
	}

	/** An ordering where a compared value is equal to another. */
	public static function Equal(): self {
		return new self(self::EQ);
	}

	/** An ordering where a compared value is greater than another. */
	public static function Greater(): self {
		return new self(self::GT);
	}

	/** Whether this ordering is less. */
	public function isLess(): bool {
		return $this->order < static::EQ;
	}

	/** Whether this ordering is less or equal. */
	public function isLessOrEqual(): bool {
		return $this->order <= static::EQ;
	}

	/** Whether this ordering is equal. */
	public function isEqual(): bool {
		return $this->order === static::EQ;
	}

	/** Whether this ordering is greater or equal. */
	public function isGreaterOrEqual(): bool {
		return $this->order >= static::EQ;
	}

	/** Whether this ordering is greater. */
	public function isGreater(): bool {
		return $this->order > static::EQ;
	}

	/** Returns `$this` when it’s not equal, otherwise returns `$other`. */
	public function then(self $other): self {
		return $this->order === static::EQ ? $other : $this;
	}

	/**
	 * Returns `$this` when it’s not {@see Equal}, otherwise calls the given
	 * callback and returns the {@see Ordering} that is returned by it.
	 */
	public function thenWith(callable $f): self {
		return $this->order === static::EQ ? $f() : $this;
	}

	/** Get the order this instance corresponds to. */
	public function toInt(): int {
		/** @noinspection PhpStrictTypeCheckingInspection */
		return ($this->order > 0) - ($this->order < 0);
	}

	/**
	 * Reverse the ordering:
	 * - {@see Less} becomes {@see Greater}
	 * - {@see Equal} stays {@see Equal}
	 * - {@see Greater} becomes {@see Less}
	 */
	public function toReverse(): self {
		$clone = clone $this;
		$clone->order -= $this->order;

		return $clone;
	}

	/** @inheritDoc */
	protected function doCompareTo($other): self {
		if ($other instanceof $this) {
			$other = $other->order;
		}

		if (\is_int($other)) {
			$clone = clone $this;
			$clone->order -= $other;

			return $clone;
		}

		return NullOrdering::new();
	}
}
