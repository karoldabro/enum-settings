# Enum settings
Extend your enums by settings provided by attributes.

## Installation
```shell
composer require kdabrow/enum-settings
```

## How to use it
### Prepare enum

Add trait UseAttributeSettings to your enum and implement attribute to enum's cases. It's possible to use multiple attributes.
```php
use \Kdabrow\EnumSettings\UseAttributeSettings;

enum Countries 
{
    use UseAttributeSettings;
    
    #[CountrySettings('polish', 38)]
    case poland;
    
    #[CountrySettings('spanish', 47)]
    case spain;
}
```

Your attribute class might look like this. Name should end with 'Settings' but it is customizable.
```php
#[\Attribute]
class CountrySettings
{
    public function __construct(
        public readonly string $language,
        public readonly int $population,
    ) {}
}
```
### Usage
Get all available settings

```php
Countries::poland->getSettings(); 

// return array with all settings
[
    'language' => 'polish',
    'population' => 38,
]
```

Get one setting

```php
Countries::poland->getSetting('population'); 

// return value of population setting
38
```

### Customisation
It's possible to set up attribute name. For example, you have attributes: Details and Cities
```php
use \Kdabrow\EnumSettings\UseAttributeSettings;

enum Countries 
{
    use UseAttributeSettings;
    
    public function settingsAttributeName()
    {
        return [Details::class, Cities::class];
    }
    
    #[Details('polish', 38)]
    #[Cities('Warsaw', 'Cracow', 'Gdansk')]
    case poland;
    
    #[Details('spanish', 47)]
    #[Cities('Madrid', 'Barcelona', 'Valencia')]
    case spain;
}
```

## Testing
```shell
vendor/bin/phpunit
```