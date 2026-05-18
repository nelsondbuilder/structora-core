.PHONY: validate test lint hygiene security cli-json

PHP ?= php

validate: lint test hygiene security cli-json

test:
	$(PHP) vendor/bin/phpunit

lint:
	$(PHP) scripts/php-syntax-check.php

hygiene:
	$(PHP) scripts/repository-hygiene.php

security:
	$(PHP) scripts/security-check.php

cli-json:
	$(PHP) scripts/validate-cli-json.php
