<?php
declare (strict_types = 1);

namespace isszz\hashids;

class Bilibili
{
	const x = 177451812;
	const a = 8728348608;
	const s = [11, 10, 3, 8, 4, 6];
	const t = 'fZodR9XQDSUm21yCkr6zBqiveYah8bt4xsWpHnJE7jL5VG3guMTKNPAwcF';

	/**
	 * prefix
	 *
	 * @var array
	 */
	public $prefix = ['', ''];

	/**
	 * Create a new Bilibili instance.
	 *
	 * @param string|null $prefix
	 *
	 * @throws \isszz\hashids\HashidsException
	 * @return void
	 */
	public function __construct(string|null $prefix = null)
	{
		if (!$prefix) {
			return;
		}

		if (!preg_match("/^[a-zA-Z]+$/", $prefix)) {
			throw new HashidsException('The prefix parameter can only be letters.');
		}

		[$this->prefix[0], $this->prefix[1]] = str_split($prefix);
	}

	/**
	 * Encode ID
	 *
	 * @param int|string $id
	 *
	 * @throws \isszz\hashids\HashidsException
	 * @return string
	 */
	public function encode(int|string $id): string
	{
		if (!ctype_digit((string) $id)) {
			throw new HashidsException('The encode ID must be a positive integer.');
		}

		$id = ($id ^ self::x) + self::a;

		$r = array_merge($this->prefix, ['1', ' ', ' ', '4', ' ', '1', ' ', '7', ' ', ' ']);

		for ($i = 0; $i < 6; $i++) {
			$r[self::s[$i]] = self::t[bcmod((string) floor($id / pow(58, $i)), '58')];
		}

		return implode('', $r);
	}

	/**
	 * Decode to the original ID values
	 *
	 * @param string $hash
	 *
	 * @throws \isszz\hashids\HashidsException
	 * @return int
	 */
	public function decode(string $hash): int
	{
		if (!preg_match("/^[0-9a-zA-Z]+$/", $hash)) {
			throw new HashidsException('The decoded hash must be a combination of numbers and letters.');
		}

		$r = $this->getTable();

		$prefix = implode('', $this->prefix);

		if ($prefix) {
			// 解码字符串未携带前缀补上
			!str_contains($hash, $prefix) && $hash = $prefix . $hash;
		} else {
			// 未设置前缀时补位
			$hash = '  '. $hash;
		}

		try {

			$s = 0;
			for ($i = 0; $i < 6; $i++) {
				$s += $r[$hash[self::s[$i]]] * pow(58, $i);
			}

			return ($s - self::a) ^ self::x;

		} catch(\Exception $e) {
			throw new HashidsException('Unable to decoded');
		}
	}

	public function getTable(): array
	{
		$r = [];
		for ($i = 0; $i < 58; $i++) {
			$r[self::t[$i]] = $i;
		}
		
		return $r;
	}
}
