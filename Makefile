NAME = $(shell make -s get-setting-name)

get-setting-% :
	php -r '$$composer = json_decode(trim(file_get_contents("composer.json")), true); echo $$composer["$*"];'

gource :
	gource -1280x720 --seconds-per-day 3 --auto-skip-seconds 1 --title "$(NAME)"

gitstats :
	gitstats -c project_name="$(NAME)" . gitstats

phpdox :
	phpdox

phploc :
	phploc --exclude=vendor --exclude=tests .

phpunit :
	./vendor/bin/phpunit --configuration tests/phpunit.xml
