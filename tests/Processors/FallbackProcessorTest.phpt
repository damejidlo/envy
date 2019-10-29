<?php
declare(strict_types = 1);

namespace DamejidloTests\Envy\Processors;

require_once __DIR__ . '/../bootstrap.php';

use Damejidlo\Envy\Processors\FallbackProcessor;
use Damejidlo\Envy\ValueProviders\SingleValueProvider;
use DamejidloTests\Envy\Fixtures\FailingValueProvider;
use DamejidloTests\Envy\Fixtures\TestException;
use DamejidloTests\Envy\TestCase;
use Tester\Assert;



/**
 * @testCase
 */
class FallbackProcessorTest extends TestCase
{

	private const FALLBACK = 'fallback';
	private const ORIGINAL = 'original';



	public function testOriginalValueIsUsed() : void
	{
		$processor = new FallbackProcessor(self::FALLBACK);
		$valueProvider = new SingleValueProvider(self::ORIGINAL);
		Assert::same(self::ORIGINAL, $processor->process('foo', $valueProvider));
	}



	public function testFallbackIsUsed() : void
	{
		$processor = new FallbackProcessor(self::FALLBACK);
		$valueProvider = new FailingValueProvider();
		Assert::same(self::FALLBACK, $processor->process('foo', $valueProvider));
	}



	public function testExceptionBubblesThrough() : void
	{
		$processor = new FallbackProcessor(self::FALLBACK);
		$valueProvider = new FailingValueProvider(new TestException());
		Assert::exception(
			function () use ($processor, $valueProvider) : void {
				$processor->process('foo', $valueProvider);
			},
			TestException::class,
		);
	}

}



(new FallbackProcessorTest())->run();
