#!/usr/bin/env php
<?php

declare(strict_types = 1);

use Fleshgrinder\Core\{
	Comparable, ComparableTrait, Comparators\ComparatorDelegate, Immutable, Ordering
};

require_once __DIR__ . '/vendor/autoload.php';

final class Currency implements Comparable {
	use ComparableTrait, Immutable;

	/** @var string */
	private $code;

	public static function new(string $code): self {
		$currency = new static;
		$currency->code = $code;

		return $currency;
	}

	protected function compareTypeSafeTo(self $other): Ordering {
		return Ordering::new(\strcmp($this->code, $other->code));
	}

	public function toString(): string {
		return $this->code;
	}
}

final class Money implements Comparable {
	use ComparableTrait, Immutable;

	/** @var int */
	private $amount;

	/** @var \Currency */
	private $currency;

	public static function new(int $amount, Currency $currency): self {
		$money = new static;
		$money->amount   = $amount;
		$money->currency = $currency;

		return $money;
	}

	protected function compareTypeSafeTo(self $other): Ordering {
		return Ordering::new($this->amount <=> $other->amount)->then(
			$this->currency->compareTo($other->currency)
		);
	}

	public function toFloat(): float {
		return $this->amount * 0.01;
	}

	public function toInt(): int {
		return $this->amount;
	}

	public function toString(): string {
		return \sprintf('%s %.2F', $this->currency->toString(), $this->toFloat());
	}
}

$money = [
	Money::new( 10, Currency::new('USD')),
	Money::new(100, Currency::new('EUR')),
	Money::new(100, Currency::new('USD')),
	Money::new( 10, Currency::new('CHF')),
	Money::new(200, Currency::new('CHF')),
	Money::new( 10, Currency::new('EUR')),
];

\usort($money, Money::getReverseComparator());

foreach ($money as $m) {
	echo "{$m->toString()}\n";
}

/*
CHF 2.00
USD 1.00
EUR 1.00
USD 0.10
EUR 0.10
CHF 0.10
*/

// It is easy to create rich domain models where the logic is where it belongs.
final class BankingAccount {
	/** @var \Money */
	private $balance;

	public function canWithdraw(Money $amount): bool {
		return $this->balance->isGreaterThanOrEquals($amount);
	}
}

\usort($money, ComparatorDelegate::new(static function (\Money $lhs, \Money $rhs): int {
	return $lhs->toInt() <=> $rhs->toInt();
}));

foreach ($money as $m) {
	echo "{$m->toString()}\n";
}
