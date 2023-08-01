<?php
declare (strict_types = 1);

namespace isszz\hashids;

use Hashids\Hashids as HashidsParent;

class BaseHashids
{
	protected HashidsParent|null $instance = null;
	/**
	 * Create a new Hashids instance.
	 *
	 * @param array $config
	 *
	 * @return void
	 */
	public function __construct(
		string $salt = '',
		int $length = 0,
		string $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
	)
	{
		if (is_null($this->instance)) {
			$this->instance = new HashidsParent($salt, $length, $alphabet);
		}

		return $this->instance;
	}

	/**
	 * Encode parameters to generate a hash.
	 *
	 * @param mixed $numbers
	 *
	 * @throws \isszz\hashids\HashidsException
	 * @return string
	 */
	public function encode(...$numbers): string
	{
		if (count($numbers) === 1 && is_array($numbers[0])) {
			$numbers = $numbers[0];
		}

		if (!$numbers) {
			return '';
		}

		$errIdx = [];
		foreach ($numbers as $key => $number) {
			$isNumber = ctype_digit((string) $number);
			if (!$isNumber) {
				$errIdx[] = $key + 1;
			}
		}

		if (count($errIdx) > 0) {
			throw new HashidsException('Parameters '. implode(', ', $errIdx) .' passed to the encode method must be positive integers.');
		}
		
		return $this->instance->encode($numbers);
	}

	/**
	 * Decode a hash to the original parameter values.
	 *
	 * @param string $hash
	 *
	 * @throws \isszz\hashids\HashidsException
	 * @return int|array|strnig
	 */
	public function decode($hash): int|array|strnig
	{
		if (!preg_match('/^[0-9a-zA-Z]{2,18}$/', $hash)) {
			throw new HashidsException('The decoded hash must be a combination of letters and numbers between 2 and 18 digits');
		}

		$result = $this->instance->decode($hash);

		$length = count($result);

		if ($length <= 0) {
			throw new HashidsException('Unable to decoded');
		}

		if ($length > 1) {
			return $result;
		}

		return $result[0];
	}
}
