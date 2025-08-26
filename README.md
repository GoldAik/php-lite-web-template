# PHP Lite Web Template

PHP Lite Web Template is a lightweight and configurable template for building web applications in [PHP](https://www.php.net/). The project is based on [Slim](https://www.slimframework.com/) and [PHP-DI](https://php-di.org/), with additional custom implementations for error handling and sessions.

**Note:** This template requires PHP version 8.2 or higher. Currently, it has only been tested with PHP 8.2.

The goal of the project is to provide a simple and flexible tool for developers, especially for smaller and medium-sized projects, enabling quick start without the need to use large frameworks.

> **Note:** The implementation is not yet fully tested and may require additional configuration or adjustments. It is recommended to thoroughly review the code before deploying in a production environment. Particular attention should be paid to custom error handling and session solutions.

Based on this project (Slim + PHP-DI), there are extended implementations with [Doctrine](https://www.doctrine-project.org/) and [Twig](https://twig.symfony.com/).

Future plans include adding:
- support for [Redis](https://redis.io/)
- REST API
- error notifications via email
- [i18n](https://en.wikipedia.org/wiki/Internationalization_and_localization)

## Be aware

> **Note:** The project contains custom, non-standard solutions for error handling and sessions. Due to the lack of comprehensive testing and the preliminary nature of these implementations, please exercise caution when using them, especially in production environments.

**I do not assume responsibility for any errors, failures, or damages resulting from the use of these features.** It is recommended to thoroughly test and verify functionality before deploying in critical systems.

Please also report any bugs or issues related to error handling and sessions to help improve them.

*In the future, alternative solutions for session handling are planned.*

## Project structure

#### The project includes various implementations based on the core (Slim + PHP-DI), with implementations located on different branches:
- `main` — project overview (current branch) [Go to main](https://github.com/GoldAik/php-lite-web-template/tree/main)
- `development` — branch where development occurs [Go to development](https://github.com/GoldAik/php-lite-web-template/tree/development)
- `slim+php-di` — basic configuration of Slim + PHP-DI [Go to slim+php-di](https://github.com/GoldAik/php-lite-web-template/tree/slim+php-di)
- `slim+php-di+twig` — Slim + PHP-DI + Twig [Go to slim+php-di+twig](https://github.com/GoldAik/php-lite-web-template/tree/slim+php-di+twig)
- `slim+php-di+doctrine` — Slim + PHP-DI + Doctrine [Go to slim+php-di+doctrine](https://github.com/GoldAik/php-lite-web-template/tree/slim+php-di+doctrine)
- `slim+php-di+doctrine+twig` — Slim + PHP-DI + Doctrine + Twig [Go to slim+php-di+doctrine+twig](https://github.com/GoldAik/php-lite-web-template/tree/slim+php-di+doctrine+twig)

#### Folder and file structure

- `src/` — main source code directory (includes `ErrorHandler/`, `Middlewares/`, `Routes/`, `Session/` and `Config.php`)
- `src/Routes/` — routes folder *(Note: `main.php` is the primary file for defining routes, but you can create additional subroute files and require them within `main.php`.)*
- `config/` — main project configuration
- `tests/` — unit and integration tests
- `public/` — public access folder
- `.env.example` — example environment variables to include in your `.env` file
- `.htaccess` — redirect to `public/` folder *(placed as a fallback — default server configuration should prevent access to files outside `public/`)*
- `application` — CLI application entry point (PHP file without extension)
- `bootstrap.php` — application bootstrap file
- `composer.json` — composer package configuration
- `composer.lock` — exact package version lock *(Note: not present on the [development](https://github.com/GoldAik/php-lite-web-template/tree/development) branch)*
- `phpunit.xml` — test configuration file
- `README.md` — documentation

#### Note
The `application` file, which serves as the CLI entry point, can be renamed to `app` for quicker access with:
```bash
  php app <command>
```
or to your project name, e.g., `cooking-blog`, then use:

```bash
  php cooking-blog <command>
```

---

## How to start?

#### 1. Clone the repository:
```bash
  git clone https://github.com/GoldAik/php-lite-web-template.git
  cd php-lite-web-template
```

#### 2. Choose the appropriate branch (e.g., slim+php-di) and update it:
```bash
  git checkout slim+php-di
```

#### 3. Install dependencies:
```bash
  composer install
```

*Note: For production, use `composer install --no-dev`*

#### 4. Set up project configuration.

Main configuration is in two places:
  1. `.env` file, which must be created to provide configuration. It’s recommended to copy `.env.example` where the variables to set are listed.
  2. `config/app.php` — contains more detailed configuration. Some variables depend on `.env` variables.

#### 5. Create neccessary folders.

  1. Create a folder named `logs/` in the root directory.
  2. Create a folder named `_cache/` in the root directory.

#### 6. Run tests to ensure everything works correctly (see the [Testing section](#running-tests)).

#### 7. Run a local server (e.g., PHP built-in server):
```bash
  php -S localhost:8000 -t public
```
Visit http://localhost:8000 in your browser. Ensure that default route file outputs "Hello World".

---

## Running Tests

To run tests, execute:
```bash
  ./vendor/bin/phpunit
```

To run only unit tests:
```bash
  ./vendor/bin/phpunit --testsuite "Unit Tests"
```

To run only integration tests:
```bash
  ./vendor/bin/phpunit --testsuite "Integration Tests"
```

## License

This project is available under the [MIT license](https://choosealicense.com/licenses/mit/). See the [LICENSE](https://github.com/GoldAik/php-lite-web-template/blob/main/LICENSE.md) file for details.

---
