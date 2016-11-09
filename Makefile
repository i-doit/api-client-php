TITLE = $(shell make -s get-setting-title)
VERSION = $(shell make -s get-setting-version)
TAG = $(shell make -s get-setting-tag)
DISTFILES = examples/ src/ COPYING idoitapi.php project.json README ChangeLog
DISTDIR = $(TAG)
DISTTARBALL = $(TAG)-$(VERSION).tar.gz


get-setting-% :
	php -r '$$project = json_decode(file_get_contents("project.json"), true); echo $$project["$*"];'

readme :
	pandoc --from markdown --to plain --smart README.md > README

changelog :
	git log --date-order --date=short | \
	sed -e '/^commit.*$$/d' | \
	awk '/^Author/ {sub(/\\$$/,""); getline t; print $$0 t; next}; 1' | \
	sed -e 's/^Author: //g' | \
	sed -e 's/>Date:   \([0-9]*-[0-9]*-[0-9]*\)/>\t\1/g' | \
	sed -e 's/^\(.*\) \(\)\t\(.*\)/\3    \1    \2/g' > ChangeLog ; \

dist : readme changelog
	rm -rf $(DISTDIR)/
	mkdir $(DISTDIR)/
	cp -r $(DISTFILES) $(DISTDIR)/
	tar czf $(DISTTARBALL) $(DISTDIR)/
	rm -r $(DISTDIR)/

tag :
	git tag -s -m "Tagging version $(VERSION)" $(VERSION)


## Clean up

clean :
	rm -f *.tar.gz README ChangeLog


## Development

gource :
	gource -1280x720 --seconds-per-day 3 --auto-skip-seconds 1 --title "$(TITLE)"

gitstats :
	gitstats -c project_name="$(TITLE)" . gitstats

phpdox :
	phpdox

phploc :
	phploc --exclude=lib --exclude=tests .

phpunit :
	phpunit --configuration tests/phpunit.xml
