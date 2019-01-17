.PHONY: docker structure structure-overwrite
.PHONY: composer composer-install npm npm-install permission
.PHONY: console
.PHONY: build tools-install tools-update
.PHONY: dephpend gitstat pdepend phan phpcpd phpcbf phpcs phpdox phploc phpmd phpstan psalm tester
.PHONY: check

# If the first argument is "console"...
ifeq (console,$(firstword $(MAKECMDGOALS)))
  RUN_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
  $(eval $(RUN_ARGS):;@:)
endif

########################################################################################################################

docker:
	@if [ ! "$$(docker ps -q -f name=apache)" ]; then\
		if [ "$$(docker ps -aq -f status=exited -f name=apache)" ]; then\
			docker rm apache;\
		fi;\
		docker-compose -f docker/docker-compose.yml up --build -d;\
	fi

########################################################################################################################

dirs := analysis App bin data docker log node_modules sql temp tests vendor vendor-bin
subdirs := App/Command App/config App/Consumer App/lang App/Enum App/Model App/Module App/Table App/TI\
	www/js/action www/js/component www/js/module www/js/presenter\
	www/scss/action www/scss/component www/scss/module www/scss/presenter
download := App/bootstrap.php bin/console www/index.php .gitignore\
 	www/js/.htaccess www/scss/.htaccess www/webloader/.htaccess www/.htaccess .htaccess

structure:
	$(foreach dir, $(dirs),(\
		if [ ! -d "$(dir)" ]; then\
			mkdir --parent $(dir);\
		fi;\
		if [ ! -f "$(dir)/.htaccess" ]; then\
			echo "order Allow, Deny" | tee -a $(dir)/.htaccess >/dev/null;\
			echo "Deny from all" | tee -a $(dir)/.htaccess >/dev/null;\
		fi\
	) &&) :
	$(foreach dir, $(subdirs),(\
		if [ ! -d "$(dir)" ]; then\
	 		mkdir --parent $(dir);\
		fi\
	) &&) :
	$(foreach file, $(download),(\
		if [ ! -f "$(file)" ]; then\
			wget -O $(file) https://github.com/nepttune/nepttune/tree/master/copy/$(file)\
		fi\
	) &&) :

structure-overwrite:
	$(foreach dir, $(dirs),(\
		mkdir --parent $(dir);\
		echo "order Allow, Deny" >> $(dir)/.htaccess;\
		echo "Deny from all" >> $(dir)/.htaccess;\
	) &&) :
	$(foreach dir, $(subdirs),(\
		mkdir --parent $(dir);\
	) &&) :
	$(foreach file, $(download),(\
		wget -O $(file) https://github.com/nepttune/nepttune/tree/master/copy/$(file)\
	) &&) :

########################################################################################################################

composer: docker
	docker-compose -f docker/docker-compose.yml exec apache composer update

composer-install: docker
	@if [ ! "$$(docker-compose -f docker/docker-compose.yml exec apache composer show)" ]; then\
		make composer;\
	fi

npm: docker
	docker-compose -f docker/docker-compose.yml exec apache npm install

npm-install: docker
	@if [ ! "$$(docker-compose -f docker/docker-compose.yml exec apache npm list --depth=0)" ]; then\
		make npm;\
	fi

permission: docker
	docker-compose -f docker/docker-compose.yml exec apache sh /usr/local/bin/permission.sh

########################################################################################################################

console: composer-install
	docker-compose -f docker/docker-compose.yml exec apache php /var/www/html/bin/console $(RUN_ARGS)

########################################################################################################################

build: structure composer-install npm-install

########################################################################################################################

tools-install: composer-install
	@if [ ! -d "vendor-bin/other" ]; then\
		docker-compose -f docker/docker-compose.yml exec apache composer bin other require --no-dev\
		 	dephpend/dephpend phan/phan rskuipers/php-assumptions;\
	fi
	@if [ ! -d "vendor-bin/phpcs" ]; then\
		docker-compose -f docker/docker-compose.yml exec apache composer bin phpcs require --no-dev\
			 squizlabs/php_codesniffer slevomat/coding-standard consistence/coding-standard;\
	fi
	@if [ ! -d "vendor-bin/phpdox" ]; then\
		docker-compose -f docker/docker-compose.yml exec apache composer bin phpdox require --no-dev\
			 theseer/phpdox;\
	fi
	@if [ ! -d "vendor-bin/phploc" ]; then\
		docker-compose -f docker/docker-compose.yml exec apache composer bin phploc require --no-dev\
			phploc/phploc sebastian/phpcpd;\
	fi
	@if [ ! -d "vendor-bin/phpmd" ]; then\
		docker-compose -f docker/docker-compose.yml exec apache composer bin phpmd require --no-dev\
			phpmd/phpmd pdepend/pdepend;\
	fi
	@if [ ! -d "vendor-bin/phpstan" ]; then\
		docker-compose -f docker/docker-compose.yml exec apache composer bin phpstan require --no-dev\
		 	phpstan/phpstan phpstan/phpstan-nette phpstan/phpstan-strict-rules phpstan/phpstan-deprecation-rules;\
	fi
	@if [ ! -d "vendor-bin/psalm" ]; then\
		docker-compose -f docker/docker-compose.yml exec apache composer bin psalm require --no-dev\
		 	vimeo/psalm;\
	fi
	@if [ ! -d "vendor-bin/tests" ]; then\
		docker-compose -f docker/docker-compose.yml exec apache composer bin tests require --no-dev\
		 	mockery/mockery nette/tester infection/infection j6s/phparch;\
	fi

tools-update: composer-install
	docker-compose -f docker/docker-compose.yml exec apache composer bin all update --no-dev

########################################################################################################################

dephpend: tools-install
	mkdir --parents analysis/docs
	docker-compose -f docker/docker-compose.yml exec apache php\
		vendor/bin/dephpend dsm App --no-classes > analysis/docs/dsm.html

gitstat: tools-install
	mkdir --parents analysis/docs
	docker-compose -f docker/docker-compose.yml exec apache gitinspector\
	 	-T -w -m --since=2010/01/01 --format=html --file-types=** > /var/www/html/analysis/docs/gitstats.html

pdepend: tools-install
	mkdir --parents analysis/build
	docker-compose -f docker/docker-compose.yml exec apache php\
        vendor/bin/pdepend\
        --summary-xml=analysis/build/pdepend.xml \
        --jdepend-chart=analysis/build/pdepend.svg \
        --overview-pyramid=analysis/build/pdepend-pyramid.svg \
        App

phan: tools-install
	mkdir --parents analysis/build
	docker-compose -f docker/docker-compose.yml exec apache php\
        vendor/bin/phan\
        --allow-polyfill-parser \
        -k analysis/phan.php
		--output-mode checkstyle \ > analysis/build/phan.xml

phpa: tools-install
	docker-compose -f docker/docker-compose.yml exec apache php\
	 	vendor/bin/phpa /var/www/html/App

phpcbf: tools-install
	docker-compose -f docker/docker-compose.yml exec apache php\
	 	vendor/bin/phpcbf --parallel=4 --standard=analysis/phpcs-standard.xml --extensions=php App

phpcpd: tools-install
	mkdir --parents analysis/build
	docker-compose exec apache php /var/www/html/vendor/bin/phpcpd --log-pmd analysis/build/phpcpd.xml App

phpcs: tools-install
	docker-compose -f docker/docker-compose.yml exec apache php\
	 	vendor/bin/phpcs --parallel=4 --standard=analysis/phpcs-standard.xml --extensions=php App

phpcs-xml: tools-install
	docker-compose -f docker/docker-compose.yml exec apache php\
		vendor/bin/phpcs --parallel=4 --standard=analysis/phpcs-standard.xml --extensions=php\
        --report=checkstyle --report-file=analysis/build/phpcs.xml App

phpdox: tools-install
	mkdir --parents analysis/docs
	docker-compose -f docker/docker-compose.yml exec apache php\
	 	vendor/bin/phpdox --file analysis/phpdox.xml

phploc: tools-install
	docker-compose -f docker/docker-compose.yml exec apache php\
		vendor/bin/phploc --count-tests --log-xml /var/www/html/analysis/build/phploc.xml App

phpmd: tools-install
	mkdir --parents analysis/build
	docker-compose -f docker/docker-compose.yml exec apache php\
		vendor/bin/phpmd App xml cleancode,codesize,controversial,design,naming,unusedcode\
        --reportfile analysis/build/phpmd.xml

phpstan: tools-install
	docker-compose -f docker/docker-compose.yml exec apache php\
		vendor/bin/phpstan analyse --level max --error-format=table -c analysis/phpstan.neon App

psalm: tools-install
	docker-compose -f docker/docker-compose.yml exec apache php\
     	vendor/bin/psalm --threads=4

tester: tools-install
	docker-compose -f docker/docker-compose.yml exec apache php\
        vendor/bin/tester/src/tester --colors -j 40 -c tests/php.ini tests

########################################################################################################################

check: dephpend gitstat phpcs phpdox phpstan tester

########################################################################################################################
