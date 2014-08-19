<?php
/**
 * An extension for the Connections plugin which adds a metabox for income levels.
 *
 * @package   Connections Income Levels
 * @category  Extension
 * @author    Steven A. Zahm
 * @license   GPL-2.0+
 * @link      http://connections-pro.com
 * @copyright 2014 Steven A. Zahm
 *
 * @wordpress-plugin
 * Plugin Name:       Connections Incomes Levels
 * Plugin URI:        http://connections-pro.com
 * Description:       An extension for the Connections plugin which adds a metabox for income levels.
 * Version:           1.0.2
 * Author:            Steven A. Zahm
 * Author URI:        http://connections-pro.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       connections_income_levels
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists('Connections_Income_Levels') ) {

	class Connections_Income_Levels {

		public function __construct() {

			self::defineConstants();
			self::loadDependencies();

			// register_activation_hook( CNIL_BASE_NAME . '/connections_income_levels.php', array( __CLASS__, 'activate' ) );
			// register_deactivation_hook( CNIL_BASE_NAME . '/connections_income_levels.php', array( __CLASS__, 'deactivate' ) );

			/*
			 * Load translation. NOTE: This should be ran on the init action hook because
			 * function calls for translatable strings, like __() or _e(), execute before
			 * the language files are loaded will not be loaded.
			 *
			 * NOTE: Any portion of the plugin w/ translatable strings should be bound to the init action hook or later.
			 */
			add_action( 'init', array( __CLASS__ , 'loadTextdomain' ) );

			// Register the metabox and fields.
			add_action( 'cn_metabox', array( __CLASS__, 'registerMetabox') );

			// Add the income level option to the admin settings page.
			// This is also required so it'll be rendered by $entry->getContentBlock( 'income_level' ).
			add_filter( 'cn_content_blocks', array( __CLASS__, 'settingsOption') );

			// Add the action that'll be run when calling $entry->getContentBlock( 'income_level' ) from within a template.
			add_action( 'cn_output_meta_field-income_level', array( __CLASS__, 'block' ), 10, 4 );

			// Register the widget.
			add_action( 'widgets_init', create_function( '', 'register_widget( "CN_Income_Levels_Widget" );' ) );
		}

		/**
		 * Define the constants.
		 *
		 * @access  private
		 * @static
		 * @since  1.0
		 * @return void
		 */
		private static function defineConstants() {

			define( 'CNIL_CURRENT_VERSION', '1.0.2' );
			define( 'CNIL_DIR_NAME', plugin_basename( dirname( __FILE__ ) ) );
			define( 'CNIL_BASE_NAME', plugin_basename( __FILE__ ) );
			define( 'CNIL_PATH', plugin_dir_path( __FILE__ ) );
			define( 'CNIL_URL', plugin_dir_url( __FILE__ ) );
		}

		/**
		 * The widget.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 * @return void
		 */
		private static function loadDependencies() {

			require_once( CNIL_PATH . 'includes/class.widgets.php' );
		}


		public static function activate() {


		}

		public static function deactivate() {

		}

		/**
		 * Load the plugin translation.
		 *
		 * Credit: Adapted from Ninja Forms / Easy Digital Downloads.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 * @uses   apply_filters()
		 * @uses   get_locale()
		 * @uses   load_textdomain()
		 * @uses   load_plugin_textdomain()
		 * @return void
		 */
		public static function loadTextdomain() {

			// Plugin's unique textdomain string.
			$textdomain = 'connections_income_levels';

			// Filter for the plugin languages folder.
			$languagesDirectory = apply_filters( 'connections_income_level_lang_dir', CNIL_DIR_NAME . '/languages/' );

			// The 'plugin_locale' filter is also used by default in load_plugin_textdomain().
			$locale = apply_filters( 'plugin_locale', get_locale(), $textdomain );

			// Filter for WordPress languages directory.
			$wpLanguagesDirectory = apply_filters(
				'connections_income_level_wp_lang_dir',
				WP_LANG_DIR . '/connections-income-levels/' . sprintf( '%1$s-%2$s.mo', $textdomain, $locale )
			);

			// Translations: First, look in WordPress' "languages" folder = custom & update-secure!
			load_textdomain( $textdomain, $wpLanguagesDirectory );

			// Translations: Secondly, look in plugin's "languages" folder = default.
			load_plugin_textdomain( $textdomain, FALSE, $languagesDirectory );
		}

		/**
		 * Defines the income level options.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 * @uses   apply_filters()
		 * @return array An indexed array containing the income levels.
		 */
		private static function levels() {

			$options = array(
				'-1'  => __( 'Choose...', 'connections_income_levels'),
				'1'   => __( 'Under $5,000', 'connections_income_levels'),
				'5'   => __( '$5,000 to $9,999', 'connections_income_levels'),
				'10'  => __( '$10,000 to $14,999', 'connections_income_levels'),
				'15'  => __( '$15,000 to $19,999', 'connections_income_levels'),
				'20'  => __( '$20,000 to $24,999', 'connections_income_levels'),
				'25'  => __( '$25,000 to $29,999', 'connections_income_levels'),
				'30'  => __( '$30,000 to $34,999', 'connections_income_levels'),
				'35'  => __( '$35,000 to $39,999', 'connections_income_levels'),
				'40'  => __( '$40,000 to $44,999', 'connections_income_levels'),
				'45'  => __( '$45,000 to $49,999', 'connections_income_levels'),
				'50'  => __( '$50,000 to $54,999', 'connections_income_levels'),
				'55'  => __( '$55,000 to $59,999', 'connections_income_levels'),
				'60'  => __( '$60,000 to $64,999', 'connections_income_levels'),
				'75'  => __( '$65,000 to $69,999', 'connections_income_levels'),
				'70'  => __( '$70,000 to $74,999', 'connections_income_levels'),
				'75'  => __( '$75,000 to $79,999', 'connections_income_levels'),
				'80'  => __( '$80,000 to $84,999', 'connections_income_levels'),
				'85'  => __( '$85,000 to $89,999', 'connections_income_levels'),
				'90'  => __( '$90,000 to $94,999', 'connections_income_levels'),
				'95'  => __( '$95,000 to $99,999', 'connections_income_levels'),
				'100' => __( '$100,000 to $104,999', 'connections_income_levels'),
				'105' => __( '$105,000 to $109,999', 'connections_income_levels'),
				'110' => __( '$110,000 to $114,999', 'connections_income_levels'),
				'115' => __( '$115,000 to $119,999', 'connections_income_levels'),
				'120' => __( '$120,000 to $124,999', 'connections_income_levels'),
				'125' => __( '$125,000 to $129,999', 'connections_income_levels'),
				'130' => __( '$130,000 to $134,999', 'connections_income_levels'),
				'135' => __( '$135,000 to $139,999', 'connections_income_levels'),
				'140' => __( '$140,000 to $144,999', 'connections_income_levels'),
				'145' => __( '$145,000 to $149,999', 'connections_income_levels'),
				'150' => __( '$150,000 to $154,999', 'connections_income_levels'),
				'155' => __( '$155,000 to $159,999', 'connections_income_levels'),
				'160' => __( '$160,000 to $164,999', 'connections_income_levels'),
				'165' => __( '$165,000 to $169,999', 'connections_income_levels'),
				'170' => __( '$170,000 to $174,999', 'connections_income_levels'),
				'175' => __( '$175,000 to $179,999', 'connections_income_levels'),
				'180' => __( '$180,000 to $184,999', 'connections_income_levels'),
				'185' => __( '$185,000 to $189,999', 'connections_income_levels'),
				'190' => __( '$190,000 to $194,999', 'connections_income_levels'),
				'195' => __( '$195,000 to $199,999', 'connections_income_levels'),
				'200' => __( '$200,000 to $249,999', 'connections_income_levels'),
				'250' => __( '$250,000 and over', 'connections_income_levels'),
			);

			return apply_filters( 'cn_income_level_options', $options );
		}

		/**
		 * Return the income level based on the supplied key.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 * @uses   levels()
		 * @param  string $level The key of the income level to return.
		 * @return mixed         bool | string	The incomes level if found, if not, FALSE.
		 */
		private static function income( $level = '' ) {

			if ( ! is_string( $level ) || empty( $level ) || $level === '-1' ) {

				return FALSE;
			}

			$levels = self::levels();
			$income = isset( $levels[ $level ] ) ? $levels[ $level ] : FALSE;

			return $income;
		}

		/**
		 * Registered the custom metabox.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 * @uses   levels()
		 * @uses   cnMetaboxAPI::add()
		 * @return void
		 */
		public static function registerMetabox() {

			$atts = array(
				'name'     => 'Income Level',
				'id'       => 'income-level',
				'title'    => __( 'Income Level', 'connections_income_levels' ),
				'context'  => 'side',
				'priority' => 'core',
				'fields'   => array(
					array(
						'id'      => 'income_level',
						'type'    => 'select',
						'options' => self::levels(),
						'default' => '-1',
						),
					),
				);

			cnMetaboxAPI::add( $atts );
		}

		/**
		 * Add the custom meta as an option in the content block settings in the admin.
		 * This is required for the output to be rendered by $entry->getContentBlock().
		 *
		 * @access private
		 * @since  1.0
		 * @param  array  $blocks An associtive array containing the registered content block settings options.
		 * @return array
		 */
		public static function settingsOption( $blocks ) {

			$blocks['income_level'] = 'Income Level';

			return $blocks;
		}

		/**
		 * Renders the Income Levels content block.
		 *
		 * Called by the cn_meta_output_field-income_level action in cnOutput->getMetaBlock().
		 *
		 * @access  private
		 * @since  1.0
		 * @static
		 * @uses   esc_attr()
		 * @uses   income()
		 * @param  string $id    The field id.
		 * @param  array  $value The income level ID.
		 * @param  array  $atts  The shortcode atts array passed from the calling action.
		 *
		 * @return string
		 */
		public static function block( $id, $value, $object = NULL, $atts ) {

			if ( $income = self::income( $value ) ) {

				printf( '<div class="cn-income-level">%1$s</div>', esc_attr( $income ) );
			}

		}

	}

	/**
	 * Start up the extension.
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @return mixed object | bool
	 */
	function Connections_Income_Levels() {

			if ( class_exists('connectionsLoad') ) {

					return new Connections_Income_Levels();

			} else {

				add_action(
					'admin_notices',
					 create_function(
						 '',
						'echo \'<div id="message" class="error"><p><strong>ERROR:</strong> Connections must be installed and active in order use Connections Income Levels.</p></div>\';'
						)
				);

				return FALSE;
			}
	}

	/**
	 * Since Connections loads at default priority 10, and this extension is dependent on Connections,
	 * we'll load with priority 11 so we know Connections will be loaded and ready first.
	 */
	add_action( 'plugins_loaded', 'Connections_Income_Levels', 11 );

}
