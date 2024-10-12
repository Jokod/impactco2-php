# —— Inspired by ———————————————————————————————————————————————————————————————
# http://fabien.potencier.org/symfony4-best-practices.html
# https://speakerdeck.com/mykiwi/outils-pour-ameliorer-la-vie-des-developpeurs-symfony?slide=47
# https://blog.theodo.fr/2018/05/why-you-need-a-makefile-on-your-project/

# Setup ————————————————————————————————————————————————————————————————————————

# Executables

# Executables: vendors
COMPOSER	  = composer
PHPUNIT       = ./vendor/bin/phpunit
PHPSTAN       = ./vendor/bin/phpstan
PHP_CS_FIXER  = ./vendor/bin/php-cs-fixer

# Misc
.DEFAULT_GOAL = help
.PHONY       =  # Not needed here, but you can put your all your targets to be sure
                # there is no name conflict between your files and your targets.

## —— 🐝 The Symfony Makefile 🐝 ———————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Composer 🧙‍♂️ ————————————————————————————————————————————————————————————
composer-install: composer.lock ## Install vendors according to the current composer.lock file
	$(COMPOSER) install

composer-update: composer.lock ## Update vendors to last version
	$(COMPOSER) update

## —— Quality insurance ✨ ——————————————————————————————————————————————————————
check: lint test ## Run all coding standards checks

lint: ## Lint files with php-cs-fixer
	$(PHP_CS_FIXER) fix --dry-run
	$(PHPSTAN) analyse src/ --level=9 --memory-limit 1G

fix: ## Fix files with php-cs-fixer
	$(PHP_CS_FIXER) fix

test: ## Run tests
	$(PHPUNIT) --configuration phpunit.xml

test-coverage: ## Run tests with coverage
	$(PHPUNIT) --coverage-html=var/coverage --configuration phpunit.xml
