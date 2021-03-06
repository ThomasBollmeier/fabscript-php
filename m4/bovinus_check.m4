AC_DEFUN([FABSCRIPT_CHECK_FOR_BOVINUS],[

# FABSCRIPT_CHECK_FOR_BOVINUS(required_min_version, script_file)

	AC_MSG_CHECKING([for bovinus parser library >= $1])
	
	script_call="$2 $1"
	installed_version=`$script_call`
	case $? in
		0)
		AC_MSG_RESULT([yes ($installed_version)])
		;;
		1)
		AC_MSG_RESULT([no])
		AC_MSG_ERROR([bovinus version is too old ($installed_version)])
		;;
		2)
		AC_MSG_RESULT([no])
		AC_MSG_ERROR([bovinus parser library is needed for faber-scriptorum (see https://github.com/ThomasBollmeier/bovinus)])
		;;
	esac
	
])

AC_DEFUN([FABSCRIPT_CHECK_FOR_BOVINUS_PHP],[

# FABSCRIPT_CHECK_FOR_BOVINUS_PHP()

	AC_MSG_CHECKING([for bovinus PHP support])

	cmd="set_include_path('${BOVINUS_PHP}'); require_once 'Bovinus/parser.php';"

	${PHP} -r "$cmd" 1>/dev/null 2>/dev/null

	if test "$?" == "0"; then
		AC_MSG_RESULT([yes])
	else 
		AC_MSG_RESULT([no])
		AC_MSG_ERROR([No bovinus runtime could be found in path ${BOVINUS_PHP}])
	fi

])
