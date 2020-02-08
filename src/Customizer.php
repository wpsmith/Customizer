<?php
/**
 * Genesis Framework.
 *
 * @package WPS\WP\Customizer
 * @author  Travis Smith <t@wpsmith.net>
 * @license GPL-2.0-or-later
 * @link    https://wpsmith.net/
 */

namespace WPS\WP\Customizer;

use WPS\Core\Singleton;

/**
 * Create panels, sections, and controls in the Customizer.
 */
class Customizer extends Singleton {

	/**
	 * The $wp_customize object.
	 *
	 * @var \WP_Customize_Manager
	 */
	protected $wp_customize;

	/**
	 * Initialize registration.
	 *
	 * By leaving a hook here, it allows other plugins and child themes to also setup and register
	 * their own panels, sections, settings, and controls.
	 *
	 * @param \WP_Customize_Manager $wp_customize WP Customizer Manager.
	 */
	public function init( \WP_Customize_Manager $wp_customize ) {

		$this->wp_customize = $wp_customize;

		/**
		 * Fires (when hooked correctly) on `wp_customize_register`, allowing
		 * the `$genesis_customizer` object to be used to create Customizer
		 * panels, sections, and controls.
		 *
		 * @param Customizer $this WPS_Customizer instance.
		 */
		do_action( 'wps_customizer', $this );

	}

	/**
	 * Register Customizer panel, sections, settings, and controls via a `$config` array.
	 *
	 * @param array $config Customizer configuration.
	 */
	public function register( array $config ) {

		foreach ( $config as $panel_name => $panel ) {

			$this->register_panel( $panel_name, $panel );

			foreach ( (array) $panel['sections'] as $section_name => $section ) {

				$this->register_section( $section_name, $section );

				foreach ( (array) $section['controls'] as $setting_key => $control ) {

					$this->register_setting( $setting_key, $control['settings'], $panel );
					$this->register_control( $setting_key, $control, $panel );

				}
			}
		}

	}

	/**
	 * Helper alias for $wp_customize->add_panel().
	 *
	 * @param string $panel_name Name of the panel.
	 * @param array  $panel      Panel properties.
	 */
	public function register_panel( $panel_name, array $panel ) {

		unset( $panel['sections'] );

		$this->wp_customize->add_panel(
			$panel_name,
			$panel
		);

	}

	/**
	 * Helper alias for $wp_customize->add_section().
	 *
	 * @param string $section_name Section name.
	 * @param array  $section      Section properties.
	 */
	public function register_section( $section_name, array $section ) {

		unset( $section['settings'] );

		$this->wp_customize->add_section(
			$section_name,
			$section
		);

	}

	/**
	 * Helper alias for $wp_customize->add_setting().
	 *
	 * @param string $setting_name Setting name.
	 * @param mixed  $setting      Setting default value.
	 * @param array  $panel        Panel properties.
	 */
	public function register_setting( $setting_name, $setting, $panel ) {

		$defaults = [
			'type' => 'option',
		];

		$setting = wp_parse_args( $setting, $defaults );

		$setting_name = isset( $panel['settings_field'] ) ? sprintf( '%s[%s]', $panel['settings_field'], $setting_name ) : $setting_name;

		$this->wp_customize->add_setting(
			$setting_name,
			$setting
		);

	}

	/**
	 * Helper alias for $wp_customize->add_control().
	 *
	 * @param string $control_name Control name.
	 * @param array  $control      Control properties.
	 * @param array  $panel        Panel properties.
	 */
	public function register_control( $control_name, array $control, $panel ) {

		$control['settings'] = sprintf( '%s[%s]', $panel['settings_field'], $control_name );

		$control_name = isset( $panel['control_prefix'] ) ? $panel['control_prefix'] . '_' . $control_name : $control_name;

		$this->wp_customize->add_control(
			$control_name,
			$control
		);

	}

}
