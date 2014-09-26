<?php

   // essential internal stuff -- skip it. Go down to Part One. There
   // are four parts to this file that interest you, all labeled Part
   // One, Two, Three and Four.

   set_magic_quotes_runtime(0);
   error_reporting(E_ALL ^ E_NOTICE);
   if (! (bool)ini_get('register_globals')) {
       import_request_variables('gps');
       if (! (bool)ini_get('register_long_arrays')) {
	   $HTTP_SERVER_VARS =& $_SERVER;
       }
       extract($HTTP_SERVER_VARS);
   }

   if (!function_exists('rcs_id')) {
      function rcs_id($id) { echo "<!-- $id -->\n"; };
   }
   rcs_id('$Id: config.php,v 1.24.2.15 2005/07/24 07:35:40 rurban Exp $'); 
   define('PHPWIKI_VERSION', '1.2.11');
   // end essential internal stuff


   /////////////////////////////////////////////////////////////////////
   // Part One:
   // Constants and settings. Edit the values below for your site.
   /////////////////////////////////////////////////////////////////////


   // URL of index.php e.g. http://yoursite.com/phpwiki/index.php
   // you can leave this empty - it will be calculated automatically
   $ScriptUrl = "";
   // URL of admin.php e.g. http://yoursite.com/phpwiki/admin.php
   // you can leave this empty - it will be calculated automatically
   // if you fill in $ScriptUrl you *MUST* fill in $AdminUrl as well!
   $AdminUrl = "";

   // Select your language - default language "C": English
   // other languages available: Dutch "nl", Spanish "es", German "de",
   // and Swedish "sv"
   $LANG="C";

   /////////////////////////////////////////////////////////////////////
   // Part Two:
   // Database section
   // set your database here and edit the according section below.
   // For PHP 4.0.4 and later you must use "dba" if you are using 
   // DBM files for storage. "dbm" uses the older deprecated interface.
   // The option 'default' will choose either dbm or dba, depending on
   // the version of PHP you are running.
   /////////////////////////////////////////////////////////////////////

   $WhichDatabase = 'default'; // use one of "dbm", "dba", "mysql",
                               // "pgsql", "msql", "mssql", or "file"

   if ($WhichDatabase == 'default' and function_exists("dba_open"))
       $WhichDatabase = 'dba';
   if ($WhichDatabase == 'default' and function_exists("dbmopen") 
       and floor(phpversion()) == 3)
       $WhichDatabase = 'dbm';
       
   // DBM and DBA settings (default)
   if ($WhichDatabase == 'dbm' or $WhichDatabase == 'dba' or
       $WhichDatabase == 'default') {
      $DBMdir = "/tmp";
      $WikiPageStore = "wiki";
      $ArchivePageStore = "archive";
      $WikiDB['wiki']      = "$DBMdir/wikipagesdb";
      $WikiDB['archive']   = "$DBMdir/wikiarchivedb";
      $WikiDB['wikilinks'] = "$DBMdir/wikilinksdb";
      $WikiDB['hottopics'] = "$DBMdir/wikihottopicsdb";
      $WikiDB['hitcount']  = "$DBMdir/wikihitcountdb";

      // This is the type of DBA handler for your system. For most Linuxen
      // 'gdbm' is best; 'db2', 'db3' or 'db4' (BerkeleyDB) are another common types. 
      // 'ndbm' appears on Solaris but won't work because it won't store pages larger
      // than 1000 bytes.
      // We do now some smart auto-detection, which will be the best.
      // You can override it by defining DBM_FILE_TYPE earlier.
      if (!defined("DBM_FILE_TYPE")) {
          if (function_exists("dba_handlers")) { // since 4.3.0
              foreach (array('gdbm','db4','db3','db2','sdbm','ndbm') as $handler) {
                  if (in_array($handler, dba_handlers())) {
                      define("DBM_FILE_TYPE", $handler);
                      break;
                  }
              }
          }
      }
      if (!defined("DBM_FILE_TYPE"))
          define("DBM_FILE_TYPE", (substr(PHP_OS,0,3) == 'WIN') ? 'db3' : 'gdbm');

      // time in seconds to try if the dbm is unavailable
      define("MAX_DBM_ATTEMPTS", 20);

      // for PHP3 use dbmlib, else use dbalib for PHP4
      if ($WhichDatabase == 'default') {
         if ( floor(phpversion()) == 3) {
            $WhichDatabase = 'dbm';
         } else {
            $WhichDatabase = 'dba';
         }
      }

      if ($WhichDatabase == 'dbm') {
          include "lib/dbmlib.php"; 
      } else {
          include "lib/dbalib.php";
      }

   // MySQL settings -- see INSTALL.mysql for details on using MySQL
   } elseif ($WhichDatabase == 'mysql') {
      // MySQL server host:
      $mysql_server = 'localhost';

      // username as used in step 2 of INSTALL.mysql:
      $mysql_user = 'wikiuser';

      // password of above user (or leave blank if none):
      $mysql_pwd = '';

      // name of the mysql database
      //  (this used to default to 'wiki' prior to phpwiki-1.2.2)
      $mysql_db = 'phpwiki';

      // Names of the tables.
      // You probably don't need to change these.  If you do change
      // them you will also have to make corresponding changes in
      // schemas/schema.mysql before you initialize the database.
      $WikiPageStore = "wiki";
      $ArchivePageStore = "archive";
      $WikiLinksStore = "wikilinks";
      $WikiScoreStore = "wikiscore";
      $HitCountStore = "hitcount";

      include "lib/mysql.php";

   // PostgreSQL settings -- see INSTALL.pgsql for more details
   } elseif ($WhichDatabase == 'pgsql') {
      $pg_dbhost    = "localhost";
      $pg_dbport    = "5432";
      $pg_dbuser    = "";      // username as used in step 2 of INSTALL.mysql
      $pg_dbpass    = "";      // password of above user (or leave blank if none)	
      $WikiDataBase  = "wiki"; // name of the database in Postgresql
      $WikiPageStore = "wiki";
      $ArchivePageStore = "archive";
      $WikiLinksPageStore = "wikilinks";
      $HotTopicsPageStore = "hottopics";
      $HitCountPageStore = "hitcount";
      include "lib/pgsql.php";

   // MiniSQL (mSQL) settings -- see INSTALL.msql for details on using mSQL
   } elseif ($WhichDatabase == 'msql') {
      $msql_db = "wiki";
      $WikiPageStore = array();
      $ArchivePageStore = array();
      $WikiPageStore['table']         = "wiki";
      $WikiPageStore['page_table']    = "wikipages";
      $ArchivePageStore['table']      = "archive";
      $ArchivePageStore['page_table'] = "archivepages";
      // should be the same as wikipages.line
      define("MSQL_MAX_LINE_LENGTH", 128);
      include "lib/msql.php";

   // Filesystem DB settings
   } elseif ($WhichDatabase == 'file') {
      $DBdir = "/tmp/wiki";
      $WikiPageStore = "wiki";
      $ArchivePageStore = "archive";
      $WikiDB[$WikiPageStore]      = "$DBdir/pages";
      $WikiDB[$ArchivePageStore]   = "$DBdir/archive";
      //$WikiDB['wikilinks'] = "$DBdir/links";
      //$WikiDB['hottopics'] = "$DBdir/hottopics";
      $WikiDB['hitcount']    = "$DBdir/hitcount";
      include "lib/db_filesystem.php";

   // MS SQLServer settings
   } elseif ($WhichDatabase == 'mssql') {
      $WikiPageStore = "wiki";
      $ArchivePageStore = "archive";
      $WikiLinksStore = "wikilinks";
      $WikiScoreStore = "wikiscore";
      $HitCountStore = "hitcount";
      $mssql_server = 'servername';
      $mssql_user = '';
      $mssql_pwd = '';
      $mssql_db = '';
      include "lib/mssql.php";

   } else die("Invalid '\$WhichDatabase' in lib/config.php"); 


   /////////////////////////////////////////////////////////////////////
   // Part Three:
   // Miscellaneous
   /////////////////////////////////////////////////////////////////////

   // logo image (path relative to index.php)
   $logo = "images/wikibase.png";

   // Signature image which is shown after saving an edited page
   // If this is left blank (or unset), the signature will be omitted.
   $SignatureImg = "images/signature.png";

   // this turns on url indicator icons, inserted before embedded links
   //define("USE_LINK_ICONS", 1);
   if (defined('USE_LINK_ICONS') and !defined('DATA_PATH'))
       define("DATA_PATH", dirname($SCRIPT_NAME));

   // date & time formats used to display modification times, etc.
   // formats are given as format strings to PHP date() function
   $datetimeformat = "F j, Y";	// may contain time of day
   $dateformat = "F j, Y";	// must not contain time

   // this defines how many page names to list when displaying
   // the MostPopular pages; the default is to show the 20 most popular pages
   define("MOST_POPULAR_LIST_LENGTH", 20);

   // this defines how many page names to list when displaying related pages
   define("NUM_RELATED_PAGES", 5);

   // number of user-defined external references, i.e. "[1]"
   define("NUM_LINKS", 12);

   // allowed protocols for links - be careful not to allow "javascript:"
   // within a named link [name|uri] one more protocol is defined: phpwiki
   $AllowedProtocols = "http|https|mailto|ftp|news|nntp|gopher";

   // URLs ending with the following extension should be inlined as images
   $InlineImages = "png|jpg|gif";

   // Uncomment this to automatically split WikiWords by inserting spaces.
   // The default is to leave WordsSmashedTogetherLikeSo in the body text.
   //define("AUTOSPLIT_WIKIWORDS", 1);

   // Perl regexp for WikiNames
   // (?<!..) & (?!...) used instead of '\b' because \b matches '_' as well
   //$WikiNameRegexp = "(?<![A-Za-z0-9])([A-Z][a-z]+){2,}(?![A-Za-z0-9])";
   // This should work for all ISO-8859-1 languages:
   $WikiNameRegexp = "(?<![A-Za-z0-9��-��-��-�])([A-Z�-��-�][a-z��-��-�]+){2,}(?![A-Za-z0-9��-��-��-�])";

   /////////////////////////////////////////////////////////////////////
   // Part Four:
   // Original pages and layout
   /////////////////////////////////////////////////////////////////////

   // need to define localization function first -- skip this
   if (!function_exists ('gettext')) {
      $lcfile = "locale/$LANG/LC_MESSAGES/phpwiki.php";
      if (file_exists($lcfile)) { include($lcfile); }
      else { $locale = array(); }

      function gettext ($text) { 
         global $locale;
         if (!empty ($locale[$text]))
           return $locale[$text];
         return $text;
      }
   } else {
      // This putenv() fails when safe_mode is on.
      @putenv("LC_ALL=$LANG");
      @putenv("LANG=$LANG");
      @putenv("LANGUAGE=$LANG");
      if (!defined("LC_ALL")) {
         // Backwards compatibility (for PHP < 4.0.5)
         define("LC_ALL", "LC_ALL");
      }
      setlocale(LC_ALL, "$LANG");
      bindtextdomain ("phpwiki", "./locale");
      textdomain ("phpwiki");
   }
   // end of localization function

   // Template files (filenames are relative to script position)
   $templates = array(
   	"BROWSE" =>    gettext("templates/browse.html"),
	"EDITPAGE" =>  gettext("templates/editpage.html"),
	"EDITLINKS" => gettext("templates/editlinks.html"),
	"MESSAGE" =>   gettext("templates/message.html")
	);

   /* WIKI_PGSRC -- specifies the source for the initial page contents
    * of the Wiki.  The setting of WIKI_PGSRC only has effect when
    * the wiki is accessed for the first time (or after clearing the
    * database.) WIKI_PGSRC can either name a directory or a zip file.
    * In either case WIKI_PGSRC is scanned for files --- one file per page.
    *
    * If the files appear to be MIME formatted messages, they are
    * scanned for application/x-phpwiki content-types.  Any suitable
    * content is added to the wiki.
    * The files can also be plain text files, in which case the page name
    * is taken from the file name.
    */

   define('WIKI_PGSRC', gettext("./pgsrc")); // Default (old) behavior.
   //define('WIKI_PGSRC', './wiki.zip'); // New style.

   // DEFAULT_WIKI_PGSRC is only used when the language is *not*
   // the default (English) and when reading from a directory:
   // in that case some English pages are inserted into the wiki as well
   // DEFAULT_WIKI_PGSRC defines where the English pages reside 
   define('DEFAULT_WIKI_PGSRC', "./pgsrc");



   //////////////////////////////////////////////////////////////////////
   // you shouldn't have to edit anything below this line
   function compute_default_scripturl() {
      global $HTTP_SERVER_VARS, $SERVER_PORT, $SERVER_NAME, $SCRIPT_NAME, $HTTPS;

      if (! (bool)ini_get('register_globals')) {
	  import_request_variables('gps');
	  if (! (bool)ini_get('register_long_arrays')) {
	      $HTTP_SERVER_VARS =& $_SERVER;
	  }
	  extract($HTTP_SERVER_VARS);
      }
      
      if (!empty($HTTPS) && $HTTPS != 'off') {
         $proto = 'https';
         $dflt_port = 443;
      }
      else {
         $proto = 'http';
         $dflt_port = 80;
      }
      $port = ($SERVER_PORT == $dflt_port) ? '' : ":$SERVER_PORT";
      return $proto . '://' . $SERVER_NAME . $port . $SCRIPT_NAME;
   }

   if (empty($ScriptUrl)) {
      $ScriptUrl = compute_default_scripturl();
   }
   if (defined('WIKI_ADMIN') && !empty($AdminUrl))
      $ScriptUrl = $AdminUrl;

   $FieldSeparator = "\263";

   if (isset($PHP_AUTH_USER)) {
      $remoteuser = $PHP_AUTH_USER;
   } else {

      // Apache won't show REMOTE_HOST unless the admin configured it
      // properly. We'll be nice and see if it's there.

      getenv('REMOTE_HOST') ? ($remoteuser = getenv('REMOTE_HOST'))
                            : ($remoteuser = getenv('REMOTE_ADDR'));
   }

   // constants used for HTML output. HTML tags may allow nesting
   // other tags always start at level 0
   define("ZERO_LEVEL", 0);
   define("NESTED_LEVEL", 1);

   // constants for flags in $pagehash
   define("FLAG_PAGE_LOCKED", 1);
?>
