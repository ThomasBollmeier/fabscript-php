#                                               -*- Autoconf -*-
# Process this file with autoconf to produce a configure script.

AC_PREREQ([2.68])
AC_INIT([fabscript-php], [0.6.1], [tbollmeier@web.de])
AC_CONFIG_MACRO_DIR([m4])

AM_INIT_AUTOMAKE([1.11])
AM_SILENT_RULES([yes])

PHP=${prefix}/bin/php

AC_ARG_WITH(
    [php],
    AC_HELP_STRING([--with-php=<path_to_php_interpreter>], [Path to PHP interpreter]),
    [
    PHP=$with_php
    ]
    )

BOVINUS_PHP=${prefix}

AC_ARG_WITH(
	[bovinus-php],
	AC_HELP_STRING([--with-bovinus-php=<path_to_bovinus_php_runtime], [Path to bovinus PHP runtime]),
	[BOVINUS_PHP=$with_bovinus_php]
)

# Check for dependencies:
FABSCRIPT_CHECK_FOR_BOVINUS(
	[1.6.1],
	[$srcdir/m4/bovinus_check.sh]
	)
FABSCRIPT_CHECK_FOR_BOVINUS_PHP()

AC_OUTPUT([
Makefile
Fabscript/Makefile
grammar/Makefile
m4/Makefile
unit-test/Makefile
])
