<?php

namespace Kdabrow\EnumSettings\Tests;

use Kdabrow\EnumSettings\UseAttributeSettings;

class UseAttributeSettingsTest extends \PHPUnit\Framework\TestCase
{
	/** @test */
	public function return_all_available_settings()
	{
	    $this->assertEquals(
			[
				'language' => 'polski',
				'population' => 38,
				'isSunny' => false,
				'capitalCity' => 'Warsaw',
			],
			Countries::poland->getSettings()
		);
	}

	/** @test */
	public function trows_exception_when_settings_attribute_is_not_found_or_defined()
	{
		$this->expectException(\Exception::class);

		Countries::france->getSettings();
	}

	/** @test */
	public function trows_exception_when_not_found_required_setting()
	{
		$this->expectException(\Exception::class);

		Countries::spain->getSetting('not_existing_setting');
	}

	/** @test */
	public function automatically_detects_settings_attribute_by_matching_pattern()
	{
	    $this->assertEquals(47, Countries::spain->getSetting('population'));
	}

	/** @test */
	public function get_settings_attribute_from_custom_attributes()
	{
		$this->assertEquals('polski', CountriesWithCustomAttribute::poland->getSetting('language'));
	}

	/** @test */
	public function get_settings_attribute_from_many_custom_attributes()
	{
		$this->assertEquals(
			[
				'language' => 'polski',
				'population' => 38,
			], CountriesWithManyCustomAttributes::poland->getSettings());
	}
}

enum CountriesWithManyCustomAttributes
{
	use UseAttributeSettings;

	public function settingsAttributeName()
	{
		return [CustomSettingAttribute::class, SecondCustomSettingAttribute::class];
	}

	#[CustomSettingAttribute('polski')]
	#[SecondCustomSettingAttribute(38)]
	case poland;
}

enum CountriesWithCustomAttribute
{
	use UseAttributeSettings;

	public function settingsAttributeName()
	{
		return CustomSettingAttribute::class;
	}

	#[CustomSettingAttribute('polski')]
	case poland;
}

enum Countries
{
	use UseAttributeSettings;

	#[CitySettings('Warsaw')]
	#[CountrySettings('polski', 38, false)]
	case poland;

	#[SomeOtherAttribute]
	#[CountrySettings('español', 47, true)]
	case spain;

	case france;
}

#[\Attribute]
class SomeOtherAttribute {}

#[\Attribute]
class CountrySettings {
	public function __construct(public readonly string $language, public readonly int $population, public readonly bool $isSunny)
	{
	}
}

#[\Attribute]
class CitySettings {
	public function __construct(public readonly string $capitalCity)
	{
	}
}

#[\Attribute]
class CustomSettingAttribute {
	public function __construct(public readonly string $language)
	{
	}
}

#[\Attribute]
class SecondCustomSettingAttribute {
	public function __construct(public readonly int $population)
	{
	}
}