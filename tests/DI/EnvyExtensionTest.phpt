<?php
declare(strict_types = 1);

namespace DamejidloTests\Envy\DI;

require_once __DIR__ . '/../bootstrap.php';

use Damejidlo\Envy\Envy;
use Damejidlo\Envy\LoaderFactory;
use Damejidlo\Envy\ValueProviders\Reader;
use DamejidloTests\Envy\TestCase;
use Nette\Configurator;
use Nette\DI\Container;
use Tester\Assert;



/**
 * @testCase
 */
class EnvyExtensionTest extends TestCase
{

	public function testServices() : void
	{
		$container = $this->createContainer();

		Assert::type(Envy::class, $container->getService('envy'));
		Assert::type(Envy::class, $container->getService('envy.envy'));
		Assert::type(Reader::class, $container->getService('envy.reader'));
		Assert::type(LoaderFactory::class, $container->getService('envy.loaderFactory'));
	}



	public function testParameters() : void
	{
		putenv('FOO=foo');
		$container = $this->createContainer();
		Assert::same(
			[
				'foo' => 'foo',
				'bar' => 'default',
				'baz' => NULL,
			],
			(array) $container->getService('env'),
		);
	}



	private function createContainer() : Container
	{
		$configurator = new Configurator();
		$configurator->setDebugMode(FALSE);
		$configurator->setTempDirectory(TEMP_DIR);
		$configurator->addConfig(__DIR__ . '/config.neon');

		return $configurator->createContainer();
	}

}



(new EnvyExtensionTest())->run();
