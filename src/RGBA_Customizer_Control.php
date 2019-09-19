<?php
/**
 * RGBA_Customizer_Control Class
 *
 * Sets up Parallax within WordPress.
 *
 * You may copy, distribute and modify the software as long as you track changes/dates in source files.
 * Any modifications to or software including (via compiler) GPL-licensed code must also be made
 * available under the GPL along with build & install instructions.
 *
 * @package    WPS\WP
 * @author     Travis Smith <t@wpsmith.net>
 * @copyright  2015-2019 Travis Smith
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License v2
 * @link       https://github.com/wpsmith/WPS
 * @version    1.0.0
 * @since      0.1.0
 */

namespace WPS\WP\Customizer;

if ( ! class_exists( __NAMESPACE__ . '\RGBA_Customizer_Control' ) ) {
	/**
	 * RGBA Color Picker Customizer Control
	 *
	 * This control adds a second slider for opacity to the stock
	 * WordPress color picker, and it includes logic to seamlessly
	 * convert between RGBa and Hex color values as opacity is
	 * added to or removed from a color.
	 */
	class RGBA_Customizer_Control extends \WP_Customize_Control {
		/**
		 * Official control name.
		 *
		 * @var string $type Control name.
		 */
		public $type = 'alpha-color';

		/**
		 * Add support for palettes to be passed in.
		 *
		 * Supported values are true, false, or an array of RGBa and Hex colors.
		 *
		 * @var array $palette Color palettes.
		 */
		public $palette;

		/**
		 * Add support for showing the opacity value on the slider handle.
		 *
		 * @var bool $show_opacity Show opacity.
		 */
		public $show_opacity;

		/**
		 * Constructor.
		 *
		 * Supplied `$args` override class property defaults.
		 *
		 * If `$args['settings']` is not defined, use the $id as the setting ID.
		 *
		 * @since 3.4.0
		 *
		 * @param WP_Customize_Manager $manager Customizer bootstrap instance.
		 * @param string $id Control ID.
		 * @param array $args {
		 *     Optional. Arguments to override class property defaults.
		 *
		 * @type int $instance_number Order in which this instance was created in relation
		 *                                                 to other instances.
		 * @type WP_Customize_Manager $manager Customizer bootstrap instance.
		 * @type string $id Control ID.
		 * @type array $settings All settings tied to the control. If undefined, `$id` will
		 *                                                 be used.
		 * @type string $setting The primary setting for the control (if there is one).
		 *                                                 Default 'default'.
		 * @type int $priority Order priority to load the control. Default 10.
		 * @type string $section Section the control belongs to. Default empty.
		 * @type string $label Label for the control. Default empty.
		 * @type string $description Description for the control. Default empty.
		 * @type array $choices List of choices for 'radio' or 'select' type controls, where
		 *                                                 values are the keys, and labels are the values.
		 *                                                 Default empty array.
		 * @type array $input_attrs List of custom input attributes for control output, where
		 *                                                 attribute names are the keys and values are the values. Not
		 *                                                 used for 'checkbox', 'radio', 'select', 'textarea', or
		 *                                                 'dropdown-pages' control types. Default empty array.
		 * @type array $json Deprecated. Use WP_Customize_Control::json() instead.
		 * @type string $type Control type. Core controls include 'text', 'checkbox',
		 *                                                 'textarea', 'radio', 'select', and 'dropdown-pages'. Additional
		 *                                                 input types such as 'email', 'url', 'number', 'hidden', and
		 *                                                 'date' are supported implicitly. Default 'text'.
		 * }
		 */
		public function __construct( $manager, $id, $args = array() ) {
			// Bail if not in the Customizer.
			if ( ! is_customize_preview() ) {
				return;
			}

			parent::__construct( $manager, $id, $args );
		}

		/**
		 * Enqueue scripts and styles.
		 *
		 * Ideally these would get registered and given proper paths before this
		 * object is initialized, then we could simply enqueue them here, but
		 * for completeness as a stand alone class we'll enqueue them here.
		 *
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function enqueue() {
			wp_enqueue_script(
				'rgba-color-picker',
				CHILD_THEME_URI . '/lib/js/customizer.js',
				array( 'jquery', 'wp-color-picker' ),
				'1.0.0',
				true
			);
			wp_enqueue_style(
				'rgba-color-picker',
				CHILD_THEME_URI . '/lib/css/customizer.css',
				array( 'wp-color-picker' ),
				'1.0.0'
			);
		}

		/**
		 * Render the control.
		 *
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function render_content() {
			// Process the palette.
			if ( is_array( $this->palette ) ) {
				$palette = implode( '|', $this->palette );
			} else {
				// Default to true.
				$palette = ( false === $this->palette || 'false' === $this->palette ) ? 'false' : 'true';
			}
			// Support passing show_opacity as string or boolean. Default to true.
			$show_opacity = ( false === $this->show_opacity || 'false' === $this->show_opacity ) ? 'false' : 'true';
			// Begin the output.
			if ( isset( $this->label ) && '' !== $this->label ) {
				echo '<span class="customize-control-title">' . esc_html( $this->label ) . '</span>';
			}
			?>
            <label>
				<?php
				if ( isset( $this->description ) && '' !== $this->description ) {
					echo '<span class="description customize-control-description">' . esc_html( $this->description ) . '</span>';
				}
				?>
                <input class="alpha-color-control" type="text" data-show-opacity="<?php echo esc_html( $show_opacity ); ?>"
                       data-palette="<?php echo esc_attr( $palette ); ?>"
                       data-default-color="<?php echo esc_attr( $this->settings['default']->default ); ?>" <?php $this->link(); ?> />
            </label>
			<?php
		}
	}
}