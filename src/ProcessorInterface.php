<?php
declare(strict_types = 1);

namespace Damejidlo\Envy;

interface ProcessorInterface
{

	/**
	 * @param string $name
	 * @param ValueProviderInterface $valueProvider
	 * @return mixed
	 */
	public function process(string $name, ValueProviderInterface $valueProvider);

}
