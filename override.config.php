<?php
/**
 * Override control: relocate or share Wikka components between installations.
 *
 * This file allows the administrator to override standard or default paths used
 * by Wikka. This makes it possible to:
 * - locate the site configuration file outside the webroot for security
 * - share Wikka code between several Wikka sites running on the same server
 * - share Wikka language files between several Wikka sites running on the same
 *   server: useful if you make adaptations to the texts or add a new translation
 * - use an already-installed 3rd-party component from its own location instead
 *   of the one bundled with Wikka
 *
 * To use an override, uncomment the corresponding define and adapt the path
 * to reflect your situation.
 *
 * There are two types of overrides:
 * - Direct overrides will be used directly by Wikka
 * - Configuration overrides define a <b>default</b> value for the configuration,
 *   which may be changed during installation or later by modifying the site
 *   configuration file. By using the overrides here you can provide consistent
 *   setup values for a collection of Wikka installations
 * 
 * Naming indicates purpose:
 * - All constants have a prefix "LOCAL": 'LOCAL' in this context means applicable
 *   to this particular instance of Wikka, although it these constants can
 *   actually be used to share system files between different installations, by
 *   pointing this all at the same location in all instances.
 * - Constant names ending in "_PATH" define a <b>filesystem directory</b>; they
 *   should not end in a (back)slash
 * - Constant names ending in "_CONFIGFILE" define a <b>filesystem path</b> for a
 *   configuration file; LOCAL_SITE_CONFIGFILE is for the site configuration file
 *   and does not need to exist before installation.
 * - Constant names ending in "_URIPATH" define a <b>URI path component</b> to
 *   be used by Wikka to build a fully-qualified URL; they should not end in
 *   a slash.
 *
 * Tip 1:
 * When defining <b>filesystem paths</b>, you can use relative, absolute, or
 * fully_qualified paths (with a drive letter on Windows).
 * You may also use a (normal) slash instead of a backslash on a Windows server:
 * Wikka canonicalizes the paths, which automatically provides the slash type
 * appropriate for the system it's running on.
 *
 * Tip 2:
 * When defining <b>URI path components</b>, you can use the little "Compatibility"
 * function <b>filesys2uri()</b> to convert any backslashes in an already-built
 * or default filesystem path into forward slashes to use as a URI path.
 * 
 * @package		Configuration
 * @subpackage	Overrides
 */

/**#@+
 * String constant used as 'LOCAL' override for a standard Wikka path.
 */
/**
 * Alternative path for Wikka's languages library.
 * 
 * The path must be a (filesystem) path for a <b>directory</b>, and must
 * <b>not</b> end in a (back)slash.
 * The default is 'lang' within the installation directory.
 * 
 * This is where all files with language-dependent content are stored; this
 * includes a file with all messages for each language, as well as content for
 * sytem pages that are installed with Wikka. If you make any changes to any of
 * these or add any new language(s) you may want to share this library between
 * Wikka sites on your server.
 */
#if (!defined('LOCAL_LANG_PATH')) define('LOCAL_LANG_PATH','path/to/your/lang');

/**
 * Alternative path where (all) libaries are stored.
 *
 * The path must be a (filesystem) path for a <b>directory</b>, and must
 * <b>not</b> end in a (back)slash.
 * The default is 'libs' within the installation directory.
 * 
 * Use this only if <b>all</b> your libraries (including the default configuration
 * and the core Wakka class!) are to be found in a different location.
 *
 * NOTE: the location of (some) other directories can be defined in the
 * configuration - but the configuration files must be found first!
 */
#if (!defined('LOCAL_LIBRARY_PATH')) define('LOCAL_LIBRARY_PATH','path/to/your/libs');

/**#@+
 * String constant used as 'LOCAL' override for a standard Wikka configuration file path.
 */
/**
 * Alternative path for the default configuration file.
 *
 * The path must be a (filesystem) path for your default configuration <b>file</b>.
 * The default is 'Config.class.php' in the 'libs' directory.
 *
 * If you define a LOCAL_LIBRARY_PATH you don't need this unless Config.class.php
 * is somewhere else again. If you relocate (or share) only Config.class.php, use
 * this constant to define its full (filesystem) path.
 */
#if (!defined('LOCAL_DEFAULT_CONFIGFILE')) define('LOCAL_DEFAULT_CONFIGFILE','path/to/your/Config.class.php');
/**
 * Alternative path for local configuration file.
 *
 * The path must be a (filesystem) path for your configuration <b>file</b>.
 * The default is 'wikka.config.php' in the installation directory.
 *
 * Whatever is in your configuration file will always override the defaults in
 * the default configuration file (see above).
 * Use this if you need to store the configuration file outside of the webroot
 * for security reasons, or to share one configuration file between several
 * Wikka Wiki installations.
 *
 * NOTE: This replaces the use of the environment variable WAKKA_CONFIG for
 * security reasons. [SEC]
 */
#if (!defined('LOCAL_SITE_CONFIGFILE')) define('LOCAL_SITE_CONFIGFILE','path/to/your/wikka.config.php');
/**#@-*/

/**#@+
 * String constant used as 'LOCAL' override for a standard Wikka component directory.
 */
/**
 * Alternative path to action files.
 * Name of the <b>directory</b> under which Wikka action files are stored.
 *
 * The path must be a (filesystem) path for a <b>directory</b>, and must
 * <b>not</b> end in a (back)slash.
 * The default is 'actions' within the installation directory.
 */
#if (!defined('LOCAL_ACTION_PATH'))	define('LOCAL_ACTION_PATH', 'path/to/your/actions');
/**
 * Alternative path to handler files.
 * Name of the <b>directory</b> under which Wikka handler files are stored.
 *
 * The path must be a (filesystem) path for a <b>directory</b>, and must
 * <b>not</b> end in a (back)slash.
 * The default is 'handlers' within the installation directory.
 */
#if (!defined('LOCAL_HANDLER_PATH'))	define('LOCAL_HANDLER_PATH', 'path/to/your/handlers');
/**
 * Alternative path to formatter files.
 * Name of the <b>directory</b> under which Wikka formatter files are stored.
 *
 * The path must be a (filesystem) path for a <b>directory</b>, and must
 * <b>not</b> end in a (back)slash.
 * The default is 'formatters' within the installation directory.
 */
#if (!defined('LOCAL_FORMATTER_PATH'))	define('LOCAL_FORMATTER_PATH', 'path/to/your/formatters');
/**
 * Alternative path to Wikka highlighter files.
 * Name of the <b>directory</b> under which Wikka highlighter files are stored.
 *
 * The path must be a (filesystem) path for a <b>directory</b>, and must
 * <b>not</b> end in a (back)slash.
 * The default is 'formatters' within the installation directory. This is the
 * same path as that used for formatters; so if you override that path and keep
 * the files all together, you don't need an override for this one.
 */
#if (!defined('LOCAL_HIGHLIGHTER_PATH'))	define('LOCAL_HIGHLIGHTER_PATH', LOCAL_FORMATTER_PATH);
/**
 * Alternative path to template files.
 * Name of the <b>directory</b> under which Wikka template files are stored.
 *
 * The path must be a (filesystem) path for a <b>directory</b>, and must
 * <b>not</b> end in a (back)slash.
 * The default is 'templates' within the installation directory.
 */
#if (!defined('LOCAL_TEMPLATE_PATH'))	define('LOCAL_TEMPLATE_PATH', 'path/to/your/templates');
/**#@-*/


/**
 * Base directory for where 3rd-party components bundled with Wikka are stored.
 *
 * The path must be a (filesystem) path for a <b>directory</b>, and must
 * <b>not</b> end in a slash.
 * The default is '3rdparty' within the installation directory.
 *
 * May be used to build a path to specific (groups of) 3rd-patry components; or
 * leave this alone, and define those paths directly.
 */
#if (!defined('LOCAL_3RDPARTY_PATH'))	define('LOCAL_3RDPARTY_PATH', 'path/to/your/3rdparty');


/**#@+
 * String constant used as 'LOCAL' override for a path to a 3rd-party component used with Wikka
 *
 * The path must be a (filesystem) path for a <b>directory</b>, and must
 * <b>not</b> end in a slash.
 */
/**
 * Base directory for 3rd-party <b>core</b> components.
 * The default is 'core' within the '3rdparty' directory.
 *
 * Core 3rd-party components are required for a basic Wikka installation.
 *
 * Can be used to build a path to specific components; or leave this alone, and
 * define those paths directly.
 */
#if (!defined('LOCAL_3RDPARTY_CORE_PATH'))		define('LOCAL_3RDPARTY_CORE_PATH', 'path/to/your/3rdparty-core');
/**
 * Base directory for 3rd-party <b>plugin</b> components.
 * The default is 'plugins' within the '3rdparty' directory.
 *
 * Plugin 3rd-party components are optional; their usage is determined by a
 * configuration flag, so if its flag is on, the component must be present and
 * be able to be found.
 *
 * Can be used to build a path to specific components; or leave this alone, and
 * define those paths directly
 */
#if (!defined('LOCAL_3RDPARTY_PLUGIN_PATH'))	define('LOCAL_3RDPARTY_PLUGIN_PATH', 'path/to/your/3rdparty-plugins');

/**
 * Path where core component FeedCreator is located.
 * The default is 'feedcreator' within the '3rdparty/core' directory.
 */
#if (!defined('LOCAL_FEEDCREATOR_PATH'))		define('LOCAL_FEEDCREATOR_PATH', 'path/to/your/feedcreator');
/**
 * Path where core component SafeHTML is located.
 * The default is 'safehtml' within the '3rdparty/core' directory.
 */
#if (!defined('LOCAL_SAFEHTML_PATH'))			define('LOCAL_SAFEHTML_PATH', 'path/to/your/safehtml');

/**
 * Path where plugin component GeSHi (Code syntax highlighter) is located.
 * The default is 'geshi' within the '3rdparty/plugins' directory.
 *
 * Used together with GeSHi languages path: you may use this to build the path to that.
 */
#if (!defined('LOCAL_GESHI_PATH'))				define('LOCAL_GESHI_PATH', 'path/to/your/geshi');
/**
 * Path where the language files for plugin component GeSHi are located.
 * Used together with GeSHi component path which you may use to build this path:
 * normally the languages directory is located within the GeSHi component directory.
 */
#if (!defined('LOCAL_GESHI_LANG_PATH'))			define('LOCAL_GESHI_LANG_PATH', 'path/to/your/geshi-languages');
/**
 * Path where plugin component Onyx-RSS (feed aggregator) is located.
 * The default is 'onyx-rss' within the '3rdparty/plugins' directory.
 */
#if (!defined('LOCAL_ONYX_PATH'))				define('LOCAL_ONYX_PATH', 'path/to/your/onyx-rss');
/**#@-*/

/**#@+
 * String constant used as 'LOCAL' override for a URL path component.
 */
/**
 * Path to the <b>directory</b> where plugin component FreeMind is located.
 * The default is '3rdparty/plugins/freemind'.
 *
 * FreeMind is an open source Java-based MindMap utility; Wikka bundles only the
 * jar archive for the display applet.
 *
 * This is not a filesystem path because this will be used to build a URL for
 * the browser to retrieve.
 * The path must be a valid URI path component, and can be either an absolute
 * path (starting with a slash) or a relative path (not starting with a slash);
 * in the latter case it will result in URI relative to the current Wikka
 * base URI.
 */
#if (!defined('LOCAL_FREEMIND_URIPATH'))	define('LOCAL_FREEMIND_URIPATH', 'path/to/your/freemind');
/**
 * Path to the <b>directory</b> where plugin component WikiEdit is located.
 * The default is '3rdparty/plugins/wikiedit'.
 *
 * WikiEdit is a JavaScript-based editor toolbar.
 *
 * This is not a filesystem path because this will be used to build a URL for
 * the browser to retrieve.
 * The path must be a valid URI path component, and can be either an absolute
 * path (starting with a slash) or a relative path (not starting with a slash);
 * in the latter case it will result in URI relative to the current Wikka
 * base URI.
 * 
 * Wikk a will not only use this path to tell the browser where the WikiEdit
 * scripts are located but also to tell WikiEdit where is toolbar button
 * images are located.
 */
#if (!defined('LOCAL_WIKIEDIT_URIPATH'))	define('LOCAL_WIKIEDIT_URIPATH', 'path/to/your/wikiedit');
/**#@-*/
?>