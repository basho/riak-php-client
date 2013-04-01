.PHONY: build all test clean

build:
	./build_phar.sh

docs:
	doxygen php-doxyfile ./

test:
	./test/run.sh

clean:
	rm -rf docs/ ./riak.phar*
