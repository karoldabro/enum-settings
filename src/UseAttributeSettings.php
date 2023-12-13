<?php

namespace Kdabrow\EnumSettings;

trait UseAttributeSettings
{
	public function getSettings(): array
	{
		$reflectionAttributes = $this->findSettingsAttribute();

		$result = [];

		foreach ($reflectionAttributes as $attribute) {
			$result = [...$result, ...array_combine(
				$this->getAttributeParameters($attribute),
				$attribute->getArguments()
			)];
		}

		return $result;
	}
	public function getSetting(string $settingName): mixed
	{
		if (!array_key_exists($settingName, $this->getSettings())) {
			throw new \Exception("Setting $settingName isn't defined");
		}

		return $this->getSettings()[$settingName];
	}

	/**
	 * @return array<int, \ReflectionAttribute>
	 * @throws \Exception
	 */
	private function findSettingsAttribute(): array
	{
		$result = [];

		$customDefinedAttributes = $this->getCustomDefinedAttributes();

		foreach ($this->makeReflectionClass()->getAttributes() as $attribute) {
			if (
				str_ends_with($attribute->getName(), "Settings") ||
				(!empty($customDefinedAttributes) && in_array($attribute->getName(), $customDefinedAttributes))
			) {
				$result[] = $attribute;
			}
		}

		if (empty($result)) {
			throw new \Exception("Not found any setting attribute");
		}

		return $result;
	}

	private function getCustomDefinedAttributes(): array
	{
		if (method_exists($this, 'settingsAttributeName')) {
			$customDefinedAttributes = $this->settingsAttributeName();

			if (is_string($customDefinedAttributes)) {
				return [$customDefinedAttributes];
			}

			return $customDefinedAttributes;
		}

		return [];
	}

	private function getAttributeParameters(\ReflectionAttribute $attribute): array
	{
		$reflection = new \ReflectionClass($attribute->getName());

		$names = [];

		foreach ($reflection->getConstructor()->getParameters() as $parameter) {
			$names[] = $parameter->getName();
		}

		return $names;
	}

	private function makeReflectionClass(): \ReflectionEnumUnitCase
	{
		return new \ReflectionEnumUnitCase(self::class, $this->name);
	}
}
