<?php
declare (strict_types = 1);

use isszz\hashids\facade\Hashids;

if (!function_exists('id_encode')) {
	/**
	 * 生成加密ID
	 * 
	 * @param int|string $id
	 * @param string|null $mode
	 *
	 * @throws \isszz\sensitive\HashidsException
	 * @return string
	 */
	function id_encode(...$arguments)
	{
		if (count($arguments) === 1  && is_array($arguments[0])) {
			$arguments = $arguments[0];
		}

		// 先尝试拿最后一个数组为模式名
		$mode = array_pop($arguments);
		if (ctype_digit((string) $mode)) {
			$arguments = array_merge($arguments, [$mode]);
			$mode = null;
		}

		// B站模式，只拿第一个参数作为ID，如果传入多个忽略
		if ($mode && $mode === 'bilibili') {
			$id = array_shift($arguments);
			return Hashids::mode($mode)->encode($id);
		}

		if (count($arguments) === 1  && is_array($arguments[0])) {
			$arguments = $arguments[0];
		}

		return Hashids::mode($mode)->encode($arguments);
	}
}

if (!function_exists('id_decode')) {
	/**
	 * 解密生成的ID
	 * 
	 * @param string $string
	 * @param string|null $mode
	 *
	 * @throws \isszz\sensitive\HashidsException
	 * @return int
	 */
	function id_decode(string $string, string|null $mode = null)
	{
		if ($mode) {
			return Hashids::mode($mode)->decode($string);
		}

		return Hashids::decode($string);
	}
}

if (!function_exists('id_mode')) {
	/**
	 * 切换解密模式
	 * 
	 * @param string $name
	 *
	 * @throws \isszz\sensitive\HashidsException
	 * @return \isszz\sensitive\Hashids
	 */
	function id_mode(string $name)
	{
		return Hashids::mode($name);
	}
}

if (!function_exists('id_build_alphabet')) {
	/**
	 * 获取字母表
	 * 
	 * @return string
	 */
	function id_build_alphabet()
	{
		return str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
	}
}
