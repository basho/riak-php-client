docs:
	doxygen php-doxyfile ./

test:
	php unit_tests.php
