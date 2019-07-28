<?php
/**
 * Author: MTCode (matt@mtcode.co.uk)
 * Created: 2019-07-25 11:55
 */

define('FS_METHOD', 'direct');
define('SITE_CACHE', '20190725');
define('TIMBER_CACHE', false);

/**
 * If you are installing Timber as a Composer dependency in your theme, you'll need this block
 * to load your dependencies and initialize Timber. If you are using Timber via the WordPress.org
 * plug-in, you can safely delete this block.
 */
$composer_autoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($composer_autoload)) {
    require_once($composer_autoload);
    $timber = new Timber\Timber();
}

/**
 * This ensures that Timber is loaded and available as a PHP class.
 * If not, it gives an error message to help direct developers on where to activate
 */
if (!class_exists('Timber')) {

    add_action('admin_notices', function () {
        echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url(admin_url('plugins.php#timber')) . '">' . esc_url(admin_url('plugins.php')) . '</a></p></div>';
    });
    return;
}

/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = array('templates', 'views');

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;

/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
class StarterSite extends Timber\Site
{
    /** Add timber support. */
    public function __construct() {
        add_theme_support('post-formats');
        add_theme_support('post-thumbnails');
        add_theme_support('menus');

        add_filter('timber/context', array($this, 'add_to_context'));
        add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );
        add_action('init', array($this, 'register_post_types'));
        add_action('init', array($this, 'register_taxonomies'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        parent::__construct();
    }

    /** This is where you can register custom post types. */
    public function register_post_types() {

    }

    /** This is where you can register custom taxonomies. */
    public function register_taxonomies() {

    }

    public function enqueue_assets() {
        // Styles
        wp_enqueue_style('core', get_template_directory_uri() . '/assets/css/build/main.css', array(), SITE_CACHE);

        // Scripts
        wp_enqueue_script('core', get_template_directory_uri() . '/assets/js/build/main.js', array(), SITE_CACHE, true);
    }

    /** This is where you add some context
     *
     * @param string $context context['this'] Being the Twig's {{ this }}.
     * @return string
     */
    public function add_to_context($context) {
        $context['site'] = $this;
        $context['cache'] = array(
            'version'   => SITE_CACHE,
            'timber'    => TIMBER_CACHE
        );
        return $context;
    }

    /** This is where you can add your own functions to twig.
     *
     * @param string $twig get extension.
     * @return string
     */
    public function add_to_twig( $twig ) {
        $twig->addFunction(new Timber\Twig_Function('img', array($this, 'img')));
        return $twig;
    }

    /**
     * Returns an absolute image path using the provided relative path.
     *
     * @param $path
     * @return string
     */
    public static function img($path) {
        return get_template_directory_uri() . '/assets/img/' . $path;
    }

}

new StarterSite();