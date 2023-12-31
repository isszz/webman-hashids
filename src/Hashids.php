<?php
declare (strict_types = 1);

namespace isszz\hashids;

class Hashids
{
	/**
	 * config
	 *
	 * @var array
	 */
	protected array $config;

	/**
	 * The active modes instances.
	 *
	 * @var array<string,object>
	 */
	protected array $modes = [];

	/**
	 * Create a new hashids instance.
	 *
	 * @throws \isszz\hashids\HashidsException
	 * @return void
	 */
	public function __construct()
	{
		$this->config = config('plugin.isszz.webman-hashids.app', []);

		if (empty($this->config) || empty($this->config['modes'])) {
			throw new HashidsException('Get configuration is null');
		}
	}

	/**
	 * Get a mode instance.
	 *
	 * @param string|null $name
	 * @param string|null $prefix Applicable to bilibili mode 
	 * 
	 * @throws \isszz\hashids\HashidsException
	 * @return object
	 */
	public function mode(?string $name = null, string|null $prefix = null)
	{
		$name = $name ?: $this->getDefaultMode();

		if (!isset($this->modes[$name])) {
			$this->modes[$name] = $this->makeMode($name, $prefix);
		}

		return $this->modes[$name];
	}

	/**
	 * Make the mode instance.
	 *
	 * @param string $name
	 * @param string|null $prefix Applicable to bilibili mode 
	 * 
	 * @throws \isszz\hashids\HashidsException
	 * @return object
	 */
	protected function makeMode(string $name, string|null $prefix = null): object
	{
		$config = $this->getModeConfig($name);

		if (!empty($name) && $name == 'bilibili') {
			return new Bilibili(
				$prefix ?? ($config['prefix'] ?: null)
			);
		}

		$config['salt'] = $config['salt'] ?? '';
		$config['length'] = $config['length'] ?? 0;
		$config['alphabet'] = $config['alphabet'] ?? 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

		return new BaseHashids($config['salt'], $config['length'], $config['alphabet']);
	}

	/**
	 * Get the configuration for a mode.
	 *
	 * @param string|null $name
	 * 
	 * @throws \isszz\hashids\HashidsException
	 * @return array
	 */
	protected function getModeConfig(string $name = null): array
	{
		$name = $name ?: $this->getDefaultMode();
		$config = $this->config['modes'][$name] ?? [];

		if (!$config) {
			throw new HashidsException('Hashids modes ['. $name .'] not configured.');
		}

		return $config;
	}

	/**
	 * Get the default mode name.
	 *
	 * @return string
	 */
	protected function getDefaultMode(): string
	{
		return $this->config['default'] ?? 'main';
	}

	/**
	 * Dynamically pass methods to the default mode.
	 *
	 * @param string $method
	 * @param array  $parameters
	 *
	 * @return mixed
	 */
	public function __call(string $method, array $parameters)
	{
		return $this->mode()->$method(...$parameters);
	}
}
