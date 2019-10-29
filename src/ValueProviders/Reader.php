<?php
declare(strict_types = 1);

namespace Damejidlo\Envy\ValueProviders;

use Damejidlo\Envy\EnvironmentVariableNotFoundException;
use Damejidlo\Envy\ValueProviderInterface;
use Nette\SmartObject;



final class Reader implements ValueProviderInterface
{

	use SmartObject;



	public function exists(string $name) : bool
	{
		try {
			self::get($name);
			return TRUE;

		} catch (EnvironmentVariableNotFoundException $exception) {
			return FALSE;
		}
	}



	public function get(string $name) : string
	{
		$value = getenv($name, TRUE);
		if ($value === FALSE) {
			throw EnvironmentVariableNotFoundException::withName($name);
		}

		return $value;
	}

}
