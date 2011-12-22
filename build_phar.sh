#!/usr/bin/env php
<?php
$file = __DIR__ . '/riak.phar'; 
exec("phar-build --src=./src/ -nq --phar=$file --strip-files=" . '.php$');
$gzip_file = $file . '.tar.gz';
if( file_exists( $gzip_file ) ) unlink( $gzip_file ); 
$phar = new Phar($file); 
$phar->convertToExecutable(Phar::TAR, Phar::GZ);
