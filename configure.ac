#                                               -*- Autoconf -*-
# Process this file with autoconf to produce a configure script.

AC_PREREQ([2.68])
AC_INIT([fabscript-php], [0.4.3], [tbollmeier@web.de])
AC_CONFIG_MACRO_DIR([m4])

AM_INIT_AUTOMAKE([1.11])
AM_SILENT_RULES([yes])

# Check for dependencies:
FABSCRIPT_CHECK_FOR_BOVINUS(
	[1.6.1],
	[$srcdir/m4/bovinus_check.sh]
	)
FABSCRIPT_CHECK_FOR_BOVINUS_PHP()

# If given use environment variable for PHP library installation directory:

AC_ARG_VAR([PHP_LIBDIR], [installation directory for FabscriptPHP library]) 
if test -z "$PHP_LIBDIR"; then
    PHP_LIBDIR=${prefix}/lib/php
fi
AC_SUBST([PHP_LIBDIR])

AC_OUTPUT([
Makefile
Fabscript/Makefile
grammar/Makefile
m4/Makefile
unit-test/Makefile
])
