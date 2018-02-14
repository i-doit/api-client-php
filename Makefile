TITLE = $(shell make -s get-setting-title)
VERSION = $(shell make -s get-setting-version)

get-setting-% :
	php -r '$$project = json_decode(trim(file_get_contents("project.json")), true); echo $$project["$*"];'

tag :
	git tag -s -m "Release version $(VERSION)" $(VERSION)

gource :
	gource -1280x720 --seconds-per-day 3 --auto-skip-seconds 1 --title "$(TITLE)"

gitstats :
	gitstats -c project_name="$(TITLE)" . gitstats

phpdox :
	phpdox

phploc :
	phploc --exclude=vendor --exclude=tests .

phpunit :
	./vendor/bin/phpunit --configuration tests/phpunit.xml
