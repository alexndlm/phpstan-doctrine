<?php declare(strict_types = 1);

namespace PHPStan\Rules\Doctrine\ORM;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPStan\Type\Doctrine\ObjectMetadataResolver;
use const PHP_VERSION_ID;

/**
 * @extends RuleTestCase<EntityRelationRule>
 */
class EntityRelationRuleTest extends RuleTestCase
{

	/** @var bool */
	private $allowNullablePropertyForRequiredField;

	/** @var string|null */
	private $objectManagerLoader;

	protected function getRule(): Rule
	{
		return new EntityRelationRule(
			new ObjectMetadataResolver($this->objectManagerLoader),
			$this->allowNullablePropertyForRequiredField,
			true
		);
	}

	/**
	 * @dataProvider ruleProvider
	 * @param mixed[] $expectedErrors
	 */
	public function testRule(string $file, array $expectedErrors): void
	{
		$this->allowNullablePropertyForRequiredField = false;
		$this->objectManagerLoader = __DIR__ . '/entity-manager.php';
		$this->analyse([$file], $expectedErrors);
	}

	/**
	 * @dataProvider ruleProvider
	 * @param mixed[] $expectedErrors
	 */
	public function testRuleWithoutObjectManagerLoader(string $file, array $expectedErrors): void
	{
		$this->allowNullablePropertyForRequiredField = false;
		$this->objectManagerLoader = null;
		$this->analyse([$file], $expectedErrors);
	}

	/**
	 * @return Iterator<mixed[]>
	 */
	public function ruleProvider(): Iterator
	{
		yield [
			__DIR__ . '/data/EntityWithRelations.php',
			[
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithRelations::$genericCollection4 type mapping mismatch: property can contain Doctrine\Common\Collections\Collection but database expects Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity>.',
					77,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithRelations::$genericCollection5 type mapping mismatch: database can contain Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity> but property expects Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\MyEntity>.',
					83,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithRelations::$genericCollection5 type mapping mismatch: property can contain Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\MyEntity> but database expects Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity>.',
					83,
				],
			],
		];

		yield 'one to one' => [__DIR__ . '/data/EntityWithBrokenOneToOneRelations.php',
			[
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenOneToOneRelations::$oneToOneNullableProperty type mapping mismatch: property can contain PHPStan\Rules\Doctrine\ORM\AnotherEntity|null but database expects PHPStan\Rules\Doctrine\ORM\AnotherEntity.',
					31,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenOneToOneRelations::$oneToOneNullableColumn type mapping mismatch: database can contain PHPStan\Rules\Doctrine\ORM\AnotherEntity|null but property expects PHPStan\Rules\Doctrine\ORM\AnotherEntity.',
					37,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenOneToOneRelations::$oneToOneWrongClass type mapping mismatch: database can contain PHPStan\Rules\Doctrine\ORM\AnotherEntity|null but property expects PHPStan\Rules\Doctrine\ORM\MyEntity|null.',
					50,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenOneToOneRelations::$oneToOneWrongClass type mapping mismatch: property can contain PHPStan\Rules\Doctrine\ORM\MyEntity|null but database expects PHPStan\Rules\Doctrine\ORM\AnotherEntity|null.',
					50,
				],
			]];

		yield 'many to one' => [__DIR__ . '/data/EntityWithBrokenManyToOneRelations.php',
			[
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenManyToOneRelations::$manyToOneNullableProperty type mapping mismatch: property can contain PHPStan\Rules\Doctrine\ORM\AnotherEntity|null but database expects PHPStan\Rules\Doctrine\ORM\AnotherEntity.',
					31,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenManyToOneRelations::$manyToOneNullableColumn type mapping mismatch: database can contain PHPStan\Rules\Doctrine\ORM\AnotherEntity|null but property expects PHPStan\Rules\Doctrine\ORM\AnotherEntity.',
					37,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenManyToOneRelations::$manyToOneWrongClass type mapping mismatch: database can contain PHPStan\Rules\Doctrine\ORM\AnotherEntity|null but property expects PHPStan\Rules\Doctrine\ORM\MyEntity|null.',
					50,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenManyToOneRelations::$manyToOneWrongClass type mapping mismatch: property can contain PHPStan\Rules\Doctrine\ORM\MyEntity|null but database expects PHPStan\Rules\Doctrine\ORM\AnotherEntity|null.',
					50,
				],
			]];

		yield 'one to many' => [__DIR__ . '/data/EntityWithBrokenOneToManyRelations.php',
			[
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenOneToManyRelations::$oneToManyWithIterableAnnotation type mapping mismatch: property can contain iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity> but database expects Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity>.',
					24,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenOneToManyRelations::$oneToManyWithCollectionAnnotation type mapping mismatch: property can contain Doctrine\Common\Collections\Collection but database expects Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity>.',
					30,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenOneToManyRelations::$oneToManyWithArrayAnnotation type mapping mismatch: database can contain Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity> but property expects array<PHPStan\Rules\Doctrine\ORM\AnotherEntity>.',
					36,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenOneToManyRelations::$oneToManyWithArrayAnnotation type mapping mismatch: property can contain array<PHPStan\Rules\Doctrine\ORM\AnotherEntity> but database expects Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity>.',
					36,
				],
			]];

		yield 'many to many' => [__DIR__ . '/data/EntityWithBrokenManyToManyRelations.php',
			[
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenManyToManyRelations::$manyToManyWithIterableAnnotation type mapping mismatch: property can contain iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity> but database expects Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity>.',
					24,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenManyToManyRelations::$manyToManyWithCollectionAnnotation type mapping mismatch: property can contain Doctrine\Common\Collections\Collection but database expects Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity>.',
					30,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenManyToManyRelations::$manyToManyWithArrayAnnotation type mapping mismatch: database can contain Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity> but property expects array<PHPStan\Rules\Doctrine\ORM\AnotherEntity>.',
					36,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenManyToManyRelations::$manyToManyWithArrayAnnotation type mapping mismatch: property can contain array<PHPStan\Rules\Doctrine\ORM\AnotherEntity> but database expects Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity>.',
					36,
				],
			]];

		yield 'primary key as relation' => [
			__DIR__ . '/data/MyEntityRelationPrimaryKey.php',
			[],
		];

		yield 'primary key as nullable relation' => [
			__DIR__ . '/data/MyEntityRelationNullablePrimaryKey.php',
			[
				[
					'Property PHPStan\Rules\Doctrine\ORM\MyEntityRelationNullablePrimaryKey::$id type mapping mismatch: property can contain PHPStan\Rules\Doctrine\ORM\MyEntity|null but database expects PHPStan\Rules\Doctrine\ORM\MyEntity.',
					18,
				],
			],
		];

		yield 'composite primary key' => [
			__DIR__ . '/data/CompositePrimaryKeyEntity2.php',
			[],
		];
	}

	/**
	 * @dataProvider ruleWithAllowedNullablePropertyProvider
	 * @param mixed[] $expectedErrors
	 */
	public function testRuleWithAllowedNullableProperty(string $file, array $expectedErrors): void
	{
		$this->allowNullablePropertyForRequiredField = true;
		$this->objectManagerLoader = __DIR__ . '/entity-manager.php';
		$this->analyse([$file], $expectedErrors);
	}

	/**
	 * @dataProvider ruleWithAllowedNullablePropertyProvider
	 * @param mixed[] $expectedErrors
	 */
	public function testRuleWithAllowedNullablePropertyWithoutObjectManagerLoader(string $file, array $expectedErrors): void
	{
		$this->allowNullablePropertyForRequiredField = true;
		$this->objectManagerLoader = null;
		$this->analyse([$file], $expectedErrors);
	}

	/**
	 * @return Iterator<mixed[]>
	 */
	public function ruleWithAllowedNullablePropertyProvider(): Iterator
	{
		yield [
			__DIR__ . '/data/EntityWithRelations.php',
			[
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithRelations::$genericCollection4 type mapping mismatch: property can contain Doctrine\Common\Collections\Collection but database expects Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity>.',
					77,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithRelations::$genericCollection5 type mapping mismatch: database can contain Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity> but property expects Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\MyEntity>.',
					83,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithRelations::$genericCollection5 type mapping mismatch: property can contain Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\MyEntity> but database expects Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity>.',
					83,
				],
			],
		];

		yield 'one to one' => [__DIR__ . '/data/EntityWithBrokenOneToOneRelations.php',
			[
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenOneToOneRelations::$oneToOneNullableColumn type mapping mismatch: database can contain PHPStan\Rules\Doctrine\ORM\AnotherEntity|null but property expects PHPStan\Rules\Doctrine\ORM\AnotherEntity.',
					37,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenOneToOneRelations::$oneToOneWrongClass type mapping mismatch: database can contain PHPStan\Rules\Doctrine\ORM\AnotherEntity|null but property expects PHPStan\Rules\Doctrine\ORM\MyEntity|null.',
					50,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenOneToOneRelations::$oneToOneWrongClass type mapping mismatch: property can contain PHPStan\Rules\Doctrine\ORM\MyEntity|null but database expects PHPStan\Rules\Doctrine\ORM\AnotherEntity|null.',
					50,
				],
			]];

		yield 'many to one' => [__DIR__ . '/data/EntityWithBrokenManyToOneRelations.php',
			[
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenManyToOneRelations::$manyToOneNullableColumn type mapping mismatch: database can contain PHPStan\Rules\Doctrine\ORM\AnotherEntity|null but property expects PHPStan\Rules\Doctrine\ORM\AnotherEntity.',
					37,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenManyToOneRelations::$manyToOneWrongClass type mapping mismatch: database can contain PHPStan\Rules\Doctrine\ORM\AnotherEntity|null but property expects PHPStan\Rules\Doctrine\ORM\MyEntity|null.',
					50,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenManyToOneRelations::$manyToOneWrongClass type mapping mismatch: property can contain PHPStan\Rules\Doctrine\ORM\MyEntity|null but database expects PHPStan\Rules\Doctrine\ORM\AnotherEntity|null.',
					50,
				],
			]];

		yield 'one to many' => [__DIR__ . '/data/EntityWithBrokenOneToManyRelations.php',
			[
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenOneToManyRelations::$oneToManyWithIterableAnnotation type mapping mismatch: property can contain iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity> but database expects Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity>.',
					24,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenOneToManyRelations::$oneToManyWithCollectionAnnotation type mapping mismatch: property can contain Doctrine\Common\Collections\Collection but database expects Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity>.',
					30,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenOneToManyRelations::$oneToManyWithArrayAnnotation type mapping mismatch: database can contain Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity> but property expects array<PHPStan\Rules\Doctrine\ORM\AnotherEntity>.',
					36,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenOneToManyRelations::$oneToManyWithArrayAnnotation type mapping mismatch: property can contain array<PHPStan\Rules\Doctrine\ORM\AnotherEntity> but database expects Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity>.',
					36,
				],
			]];

		yield 'many to many' => [__DIR__ . '/data/EntityWithBrokenManyToManyRelations.php',
			[
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenManyToManyRelations::$manyToManyWithIterableAnnotation type mapping mismatch: property can contain iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity> but database expects Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity>.',
					24,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenManyToManyRelations::$manyToManyWithCollectionAnnotation type mapping mismatch: property can contain Doctrine\Common\Collections\Collection but database expects Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity>.',
					30,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenManyToManyRelations::$manyToManyWithArrayAnnotation type mapping mismatch: database can contain Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity> but property expects array<PHPStan\Rules\Doctrine\ORM\AnotherEntity>.',
					36,
				],
				[
					'Property PHPStan\Rules\Doctrine\ORM\EntityWithBrokenManyToManyRelations::$manyToManyWithArrayAnnotation type mapping mismatch: property can contain array<PHPStan\Rules\Doctrine\ORM\AnotherEntity> but database expects Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity>.',
					36,
				],
			]];

		yield 'primary key as relation' => [
			__DIR__ . '/data/MyEntityRelationPrimaryKey.php',
			[],
		];

		yield 'primary key as nullable relation' => [
			__DIR__ . '/data/MyEntityRelationNullablePrimaryKey.php',
			[],
		];

		yield 'composite primary key' => [
			__DIR__ . '/data/CompositePrimaryKeyEntity2.php',
			[],
		];
	}

	public function testBug306(): void
	{
		if (PHP_VERSION_ID < 80000) {
			self::markTestSkipped('Test requires PHP 8.0');
		}
		$this->allowNullablePropertyForRequiredField = false;
		$this->objectManagerLoader = __DIR__ . '/entity-manager.php';
		$this->analyse([__DIR__ . '/data/bug-306-relation.php'], [
			[
				'Property PHPStan\Rules\Doctrine\ORM\Bug306Relation\MyBrokenEntity::$genericCollection type mapping mismatch: property can contain Doctrine\Common\Collections\Collection but database expects Doctrine\Common\Collections\Collection&iterable<PHPStan\Rules\Doctrine\ORM\AnotherEntity>.',
				25,
			],
		]);
	}

}
