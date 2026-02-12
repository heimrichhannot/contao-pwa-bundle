# Repository Guidelines

## Project Structure & Module Organization
Core PHP bundle code lives in `src/` (commands, controllers, generators, models, event listeners, notifications).  
Contao integration files are in `contao/` (`dca/`, `config/`, `languages/`, and Twig/HTML templates).  
Frontend runtime assets are split into `public/frontend/` (PWA JS/service worker helpers) and `public/backend/` (backend JS/CSS/icons).  
Symfony service and route wiring is in `config/services.yaml` and `config/routes.yaml`.  
Translation resources also exist in `translations/`.

## Build, Test, and Development Commands
This repository is a Contao bundle, so commands are typically run inside a host Contao project.

- `composer require heimrichhannot/contao-pwa-bundle`: install the bundle.
- `composer require minishlink/web-push:^8.0`: enable push delivery support.
- `vendor/bin/contao-console huh:pwa:build`: regenerate manifest, service worker, and config files.
- `vendor/bin/contao-console huh:pwa:send-push`: process unsent push notifications.
- `php -l src/Command/BuildPwaFilesCommand.php`: quick syntax check for edited PHP files.

## Coding Style & Naming Conventions
Use PSR-4 namespaces under `HeimrichHannot\\PwaBundle\\...` and 4-space indentation.  
Follow the existing PHP brace style used in this repo (opening braces often on a new line for conditionals/loops).  
Class names are descriptive PascalCase (`PushNotificationSendCommand`), methods/properties use camelCase, and Twig template names use snake_case.  
Keep JavaScript modular in `public/frontend/` with clear class-based responsibilities.

## Testing Guidelines
There is currently no committed automated test suite in this repository.  
For changes, run syntax checks and validate behavior in a clean Contao installation:

- rebuild files with `huh:pwa:build`,
- verify service worker + manifest generation,
- test push subscribe/unsubscribe flows in browser dev tools.

If you add tests, place them under `tests/` and mirror `src/` namespaces.

## Commit & Pull Request Guidelines
Recent history favors short, imperative commit subjects (for example: `fix tl_module`, `refactor content elements`).  
Keep commits focused and avoid mixing refactors with behavior changes.

For PRs, include:
- clear summary of changed bundle behavior,
- linked issue(s),
- Contao/PHP/bundle versions used for validation,
- reproduction steps and screenshots/log output for bug fixes.
