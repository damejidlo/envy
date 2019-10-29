<?php
declare(strict_types = 1);

namespace Damejidlo\Envy\DI;

use Damejidlo\Envy\Envy;
use Damejidlo\Envy\LoaderFactory;
use Damejidlo\Envy\ValueProviders\Reader;
use Nette\DI\CompilerExtension;



class EnvyExtension extends CompilerExtension
{

	public function loadConfiguration() : void
	{
		$containerBuilder = $this->getContainerBuilder();

		$containerBuilder->addDefinition($this->prefix('reader'))
			->setType(Reader::class);

		$containerBuilder->addDefinition($this->prefix('loaderFactory'))
			->setType(LoaderFactory::class);

		$containerBuilder->addDefinition($this->prefix('envy'))
			->setType(Envy::class);

		$containerBuilder->addAlias($this->name, $this->prefix('envy'));
	}

}
