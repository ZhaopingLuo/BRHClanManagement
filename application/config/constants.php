<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code



//-----------------------------------Ace CI User --------------------------
defined('TABLE_USER')   	  OR define('TABLE_USER', 'users');
defined('TABLE_USER_GROUP')   OR define('TABLE_USER_GROUP', 'user_groups');
defined('TABLE_ORG')   		  OR define('TABLE_ORG', 'organizations');
defined('TABLE_TOKEN')   		  OR define('TABLE_TOKEN', 'user_tokens');

defined('SUPER_ORGANIZATION')   OR define('SUPER_ORGANIZATION', 1);

defined('UPLOAD_FOLDER')     	OR define('UPLOAD_FOLDER', 'uploads');
defined('DEFAULT_LOGO')     	OR define('DEFAULT_LOGO', "logo.png");
defined('LIMIT_UPLOAD_SIZE')    OR define('LIMIT_UPLOAD_SIZE', 1024); //KB

// roles.
// Caution: here is the level of role, not id. However, just for convinience, it's better keep the id number as same as the level .
// You can insert any role here or change the level, but pleas keep VISITOR and ADMINISTRATOR role to satisfy user functions
// VISITOR level should be always 0
defined('VISITOR')     			OR define('VISITOR', 0);


defined('INTERN')      	OR define('INTERN', 1);
defined('RECRUIT')      	OR define('RECRUIT', 2);
defined('VETERAN')      	OR define('VETERAN', 3);
defined('VANGUARD')      	OR define('VANGUARD', 4);
defined('ANVIL_GUARD')      	OR define('ANVIL_GUARD', 5);
defined('PRAETORIAN')      	OR define('PRAETORIAN', 6);
defined('VICE_CAPTION')      	OR define('VICE_CAPTION', 7);
defined('CAPTION')      	OR define('CAPTION', 8);
defined('ADMINISTRATOR')      	OR define('ADMINISTRATOR', 9);


defined('STATUS_0')      	OR define('STATUS_0', 12);
defined('STATUS_1')      	OR define('STATUS_1', 13);
defined('STATUS_2')      	OR define('STATUS_2', 14);
defined('STATUS_3')      	OR define('STATUS_3', 15);


// jump to the page after login
defined('LOGIN_REDIRECTION')    OR define('LOGIN_REDIRECTION', 'users/view_login_success');
defined('LOGIN_ATTEMPTING_LIMIT')   OR define('LOGIN_ATTEMPTING_LIMIT', 5);
defined('EXPIRY_TOKEN')   OR define('EXPIRY_TOKEN', 86400 * 30); // 30 days
defined('EXPIRY_USER_ACTIVE')   OR define('EXPIRY_USER_ACTIVE', 60 * 10); // 10 minutes

// This page is the only permission-free page, to display error message when the user is denied.
// format here please be: ControllerName/FunctionName; Case insensitive
defined('NO_PERMISSION_ERROR_PAGE')    OR define('NO_PERMISSION_ERROR_PAGE', 'users/func_kick_out');


// token types, dont change
defined('TOKEN_TITLE')     	OR define('TOKEN_TITLE', 'Ace Space');
defined('TOKEN_TYPE_LOGIN')     	OR define('TOKEN_TYPE_LOGIN', 0);
defined('TOKEN_TYPE_CHANGE_PASSWORD')     	OR define('TOKEN_TYPE_CHANGE_PASSWORD', 1);
defined('TOKEN_TYPE_ACTIVE_USER')     	OR define('TOKEN_TYPE_ACTIVE_USER', 2);


defined('ERROR_PIC')    OR define('ERROR_PIC', 'error.php');


//-------------------------------------------------------------------------
