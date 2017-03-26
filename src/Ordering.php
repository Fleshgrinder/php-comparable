<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core;

/** An **ordering** is the result of a comparison between two values. */
final class Ordering implements Comparable {
	use ComparableTrait, Immutable;

	/** A value is less than another. */
	const LT = -1;

	/** A value is equal to another. */
	const EQ = 0;

	/** A value is greater than another. */
	const GT = 1;

	/** @var int */
	private $order;

	protected function __construct(int $order) {
		$this->order = $order;
	}

	/** An ordering where a compared value is less than another. */
	public static function Less(): self {
		return new static(self::LT);
	}

	/** An ordering where a compared value is equal to another. */
	public static function Equal(): self {
		return new static(self::EQ);
	}

	/** An ordering where a compared value is greater than another. */
	public static function Greater(): self {
		return new static(self::GT);
	}

	/** Construct new ordering from scalar integer value. */
	public static function new(int $order): self {
		/* @noinspection PhpStrictTypeCheckingInspection */
		return new static(($order > 0) - ($order < 0));
	}

	/** Developer friendly string representation of this ordering. */
	public function __toString() {
		return __CLASS__ . '::' . \ucfirst($this->toString());
	}

	/** Whether this ordering is less. */
	public function isLess(): bool {
		return $this->order === static::LT;
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
		return $this->order === static::GT;
	}

	/** Returns `$this` when it’s not equal, otherwise returns `$other`. */
	public function then(self $other): self {
		return $this->order === static::EQ ? $other : $this;
	}

	/**
	 * Returns `$this` when it’s not equal, otherwise calls the given callback
	 * and returns the {@see Ordering} that is returned by it.
	 */
	public function thenWith(callable $f): self {
		return $this->order === static::EQ ? $f() : $this;
	}

	/** Get the order this instance corresponds to. */
	public function toInt(): int {
		return $this->order;
	}

	/**
	 * Reverse the ordering:
	 * - {@see Less} becomes {@see Greater}
	 * - {@see Equal} stays {@see Equal}
	 * - {@see Greater} becomes {@see Less}
	 */
	public function toReverse(): self {
		$clone = clone $this;

		$clone->order *= -1;

		return $clone;
	}

	/**
	 * Human-readable form of the order:
	 * - {@see Less} becomes “_less_”
	 * - {@see Equal} becomes “_equal_”
	 * - {@see Greater} becomes “_greater_”
	 */
	public function toString(): string {
		if ($this->order === static::LT) {
			return 'less';
		}

		if ($this->order === static::GT) {
			return 'greater';
		}

		return 'equal';
	}

	/** @inheritDoc */
	protected function compareTypeSafeTo(self $other): Ordering {
		$clone = clone $this;

		$clone->order = $this->order <=> $other->order;

		return $clone;
	}
}
