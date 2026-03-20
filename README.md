# Enum Converter for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/gearbox-solutions/enum-converter-laravel.svg?style=flat-square)](https://packagist.org/packages/gearbox-solutions/enum-converter-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/gearbox-solutions/enum-converter-laravel/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/gearbox-solutions/enum-converter-laravel/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/gearbox-solutions/enum-converter-laravel/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/gearbox-solutions/enum-converter-laravel/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/gearbox-solutions/enum-converter-laravel.svg?style=flat-square)](https://packagist.org/packages/gearbox-solutions/enum-converter-laravel)

This package adds a command that will take PHP enums and turn them into TS/JS enums for use in frontend development.

## Installation

You can install the package via composer:

```bash
composer require --dev gearbox-solutions/enum-converter-laravel
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="enum-converter-laravel-config"
```

This is the contents of the published config file:

```php
return [
    /*
     * relative paths from the project root
     */
    'enum_paths' => [
        'app/Enums' => 'resources/js/types/enums',
        // 'input/folder' => 'output/folder'
    ],

    /*
     * extension to use for the enum files
     */
    'enum_extension' => '.ts',

    /*
     * Enable File hash check
     * - adds a hash check to determine if a file has changed to speed up performance
     */
    'enable_file_hash_check' => true,
];
```
## Usage 

`php artisan  convert-enums`

### Options

    --js output in .js format
    --force Force processing of enums
    
## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Michael Deck](https://github.com/6399755+likeadeckofcards)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
