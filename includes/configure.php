<?php


/***************       The 2 files should be kept separate and not used to overwrite each other.      ***********/

// Define the webserver and path parameters
// HTTP_SERVER is your Main webserver: eg-http://www.your_domain.com
// HTTPS_SERVER is your Secure webserver: eg-https://www.your_domain.com
define('HTTP_SERVER', 'http://test.whgxwl.com:8000');
define('HTTPS_SERVER', 'https://test.whgxwl.com:8000');

define('ENABLE_SSL', 'false');
//define('ENABLE_SSL', 'true');

// NOTE: be sure to leave the trailing '/' at the end of these lines if you make changes!
// * DIR_WS_* = Webserver directories (virtual/URL)
// these paths are relative to top of your webspace ... (ie: under the public_html or httpdocs folder)
define('DIR_WS_CATALOG', '/');
define('DIR_WS_HTTPS_CATALOG', '/');

define('DIR_WS_IMAGES', 'images/');
define('DIR_WS_NEW_IMAGES', '../fiberstoreControl/images/');
define('DIR_WS_INCLUDES', 'includes/');
define('DIR_WS_FUNCTIONS', DIR_WS_INCLUDES . 'functions/');
define('DIR_WS_CLASSES', DIR_WS_INCLUDES . 'classes/');
define('DIR_WS_MODULES', DIR_WS_INCLUDES . 'modules/');
define('DIR_WS_LANGUAGES', DIR_WS_INCLUDES . 'languages/');
define('DIR_WS_DOWNLOAD_PUBLIC', DIR_WS_CATALOG . 'pub/');
define('DIR_WS_TEMPLATES', DIR_WS_INCLUDES . 'templates/');

define('DIR_WS_PHPBB', '/');

// * DIR_FS_* = Filesystem directories (local/physical)
//the following path is a COMPLETE path to your Zen Cart files. eg: /var/www/vhost/accountname/public_html/store/
define('DIR_FS_CATALOG', 'D:/xampp/htdocs/');

define('DIR_FS_DOWNLOAD', DIR_FS_CATALOG . 'download/');
define('DIR_FS_DOWNLOAD_PUBLIC', DIR_FS_CATALOG . 'pub/');
define('DIR_WS_UPLOADS', DIR_WS_IMAGES . 'uploads/');
define('DIR_FS_UPLOADS', DIR_FS_CATALOG . DIR_WS_UPLOADS);
define('DIR_FS_PIC_UPLOADS', DIR_FS_CATALOG . DIR_WS_UPLOADS . 'picture/');
define('DIR_FS_EMAIL_TEMPLATES', DIR_FS_CATALOG . 'email/');

// define our database connection
define('DB_TYPE', 'mysql');
define('DB_CHARSET', 'utf8');
define('DB_PREFIX', '');
define('DB_SERVER', 'localhost');
//	define('DB_SERVER', '192.168.1.139');
define('DB_SERVER_USERNAME', 'root');
define('DB_SERVER_PASSWORD', '12345678');
define('DB_DATABASE', 'fiberstore_spain');
//  define('DB_DATABASE', 'fiberstore_online');
define('USE_PCONNECT', 'false');
define('STORE_SESSIONS', '');
// for STORE_SESSIONS, use 'db' for best support, or '' for file-based storage

// The next 2 "defines" are for SQL cache support.
// For SQL_CACHE_METHOD, you can select from:  none, database, or file
// If you choose "file", then you need to set the DIR_FS_SQL_CACHE to a directory where your apache
// or webserver user has write privileges (chmod 666 or 777). We recommend using the "cache" folder inside the Zen Cart folder
// ie: /path/to/your/webspace/public_html/zen/cache   -- leave no trailing slash
define('SQL_CACHE_METHOD', 'file');
define('DIR_FS_SQL_CACHE', 'D:/xampp/htdocs/cache');
define('SESSION_WRITE_DIRECTORY', 'D:/xampp/htdocs/cache');


//date_default_timezone_set('America/Los_Angeles');
define('AJAX_NUM', '6');
define('TABLE_DOC_CATEGORIES', 'doc_categories');
define('TABLE_DOC_CATEGORIES_DESCRIPTION', 'doc_categories_description');
define('TABLE_DOC_ARTICLE', 'doc_article');
define('TABLE_DOC_ARTICLE_DESCRIPTION', 'doc_article_description');
define('TABLE_DOC_ARTICLE_CATEGORY', 'doc_article_category');

define('CHAIN_FTP_IP', '120.24.215.173');
define('CHAIN_FTP_USERNAME', 'www');
define('CHAIN_FTP_PASSWORD', 'yuxuan2016?');


// EOF
