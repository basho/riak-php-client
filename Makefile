.PHONY: build all test clean

build:
	phar-build --src=./src/ -nq --phar=./riak.phar

docs:
	doxygen php-doxyfile ./

test:
	sh ./test/run.sh

clean:
	rm -rf docs/ ./riak.phar
