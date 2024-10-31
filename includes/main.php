<?php
namespace WPFM_Persian;

class WPFMMain {

	/**
	 * The single instance of the class.
	 *
	 * @var Main
	 */
	protected static $_instance = null;

	public static $version = '3.13.3';

	private $name;

	private $description;

	private $author;

	private $fonts;

	/**
	 * Main constructor.
	 */
	public function __construct() {
		$this->name         = __('Wordpress Persian Font Manager', 'wpfm-persian');
		$this->description  = __('This plugin is for translating y admin pages to persian.', 'wpfm-persian');
		$this->author       = __('DJ', 'wpfm-persian');

		add_action( 'plugins_loaded', array($this, 'wpfm_load_text_domain') );

		// Frontend assets
		add_action('wp_enqueue_scripts', array($this, 'wpfm_enqueue_scripts'));
		add_action('admin_enqueue_scripts', array($this, 'wpfm_admin_enqueue_scripts'));

		add_action('init', array($this, 'wpfm_set_fonts'));

		// Add settings menu page
		add_action('admin_menu', array($this, 'wpfm_admin_menu'), 99);
		add_action( 'admin_init', array($this, 'wpfm_settings_init'));
	}

	public function wpfm_set_fonts() {
		$this->fonts = array(
			'iransans' => __('Iran sans', 'wpfm-persian'),
			'iransans-farsi-numbers' => __('Iran sans (Persian number)', 'wpfm-persian'),
			'iransans-dn' => __('Iran sans Hand writing', 'wpfm-persian'),
			'iranyekan' => __('Iran Yekan', 'wpfm-persian'),
			'iranyekan-farsi-numbers' => __('Iran Yekan (Persian number)', 'wpfm-persian'),
			'iransharp' => __('Iran Sharp', 'wpfm-persian'),
			'vazir' => __('Vazir', 'wpfm-persian'),
			'sahel' => __('Sahel', 'wpfm-persian'),
			'shabnam' => __('Shabnam', 'wpfm-persian'),
			'samim' => __('Samim', 'wpfm-persian'),
			'gandom' => __('Gandom', 'wpfm-persian'),
			'tanha' => __('Tanha', 'wpfm-persian'),
			'parastoo' => __('Parastoo', 'wpfm-persian'),
			'dirooz' => __('Dirooz', 'wpfm-persian'),
			'anjoman' => __('Anjoman', 'wpfm-persian'),
			'dana' => __('Dana', 'wpfm-persian'),
			'dana-fa' => __('Dana Fa Numbers', 'wpfm-persian'),
		);
	}


	/**
	 * Load plugin textdomain.
	 *
	 * @since 1.0.0
	 */
	public function wpfm_load_text_domain() {
		$plugin_rel_path = plugin_basename( WPFMBootstrap::$path ) . '/languages';
		load_plugin_textdomain( 'wpfm-persian', false, $plugin_rel_path );
	}

	public function wpfm_enqueue_scripts() {
		$font = self::wpfm_get_option('font', 'typography');
		if( isset($font) && !empty($font) ) {
			wp_enqueue_style('wpfm-persian-'.$font.'font', WPFMBootstrap::$url.'/assets/public/css/'.$font.'-font.css', array(), self::$version);
		}
	}

	public function wpfm_admin_enqueue_scripts() {
		$font = self::wpfm_get_option('dashboard_font', 'typography');
		if( isset($font) && !empty($font) ) {
			wp_enqueue_style('wpfm-persian-'.$font.'font', WPFMBootstrap::$url.'/assets/public/css/'.$font.'-font.css', array(), self::$version);
		}
	}


	public function wpfm_admin_menu() {
		add_submenu_page(
			'tools.php',
			__('Persian Font Manager', 'wpfm-persian'),
			__('Persian Font Manager', 'wpfm-persian'),
			'manage_options',
			'wpfm-persian',
			array($this, 'wpfm_admin_menu_content')
		);
	}

	public function wpfm_admin_menu_content() {
		?>
        <div class="wrap">
            <h1><?php _e('Persian Font Manager settings', 'wpfm-persian') ?></h1>
			<?php $active_tab = sanitize_text_field(isset( $_GET[ 'tab' ] )) ? sanitize_text_field($_GET[ 'tab' ]) : 'general'; ?>
            <h2 class="nav-tab-wrapper" style="margin-bottom: 10px">
                <a href="<?php echo esc_url(admin_url('admin.php?page=wpfm-persian-settings&tab=general')) ?>" class="nav-tab <?php echo esc_html($active_tab) == 'general' ? 'nav-tab-active' : ''; ?>"><?php _e('General', 'wpfm-persian') ?></a>
            </h2>

            <form method="post" action="options.php">
				<?php
				settings_errors();
				if( $active_tab == 'general' ) {
					settings_fields( 'wpfm-persian-group' );
					do_settings_sections( 'wpfm-persian-general-page' );
				} elseif( $active_tab == 'notifications' ) {
					settings_fields( 'wpfm-persian-group' );
					do_settings_sections( 'zhk_notifications_page' );
				}
				submit_button();
				?>
            </form>
        </div>
		<?php
	}

	public function wpfm_settings_init() {
		register_setting(
			'wpfm-persian-group',
			'wpfm-persian-options'
		);

		// Settings Sections
		add_settings_section(
			'typography',
			__('Typography', 'wpfm-persian'),
			NULL,
			'wpfm-persian-general-page'
		);

		// Settings fields
		add_settings_field(
			'font',
			__('Font', 'wpfm-persian'),
			array($this, 'wpfm_font_callback'),
			'wpfm-persian-general-page',
			'typography'
		);
		add_settings_field(
			'dashboard_font',
			__('Dashboard Font', 'wpfm-persian'),
			array($this, 'wpfm_dashboard_font_callback'),
			'wpfm-persian-general-page',
			'typography'
		);
	}

	public function wpfm_font_callback() {
		?>
        <select name="wpfm-persian-options[typography][font]" id="font">
            <option value=""><?php _e('Theme default', 'wpfm-persian'); ?></option>
			<?php foreach ($this->fonts as $font_key => $font_title): ?>
                <option value="<?php echo esc_html($font_key) ?>" <?php selected( self::wpfm_get_option('font', 'typography'), $font_key ) ?>><?php echo esc_html($font_title); ?></option>
			<?php endforeach; ?>
        </select>
		<?php
	}

	public function wpfm_dashboard_font_callback() {
		?>
        <select name="wpfm-persian-options[typography][dashboard_font]" id="dashboard_font">
            <option value=""><?php _e('WordPress default', 'wpfm-persian'); ?></option>
			<?php foreach ($this->fonts as $font_key => $font_title): ?>
                <option value="<?php echo esc_html($font_key) ?>" <?php selected( self::wpfm_get_option('dashboard_font', 'typography'), $font_key ) ?>><?php echo esc_html($font_title); ?></option>
			<?php endforeach; ?>
        </select>
		<?php
	}

	public static function wpfm_get_option($option_name = false, $option_group = false)
	{
		$options = get_option('wpfm-persian-options');
		$sorted_options = array();


		$sorted_options['typography']['font'] = (isset($options['typography']['font']) && !empty($options['typography']['font'])) ? $options['typography']['font'] : '';
		$sorted_options['typography']['dashboard_font'] = (isset($options['typography']['dashboard_font']) && !empty($options['typography']['dashboard_font'])) ? $options['typography']['dashboard_font'] : '';

		if( !empty($option_name) && !empty($option_group) ) {
			return $sorted_options[$option_group][$option_name];
		} else {
			return $options;
		}
	}

	/**
	 * Main Class Instance.
	 *
	 * Ensures only one instance of this class is loaded or can be loaded.
	 *
	 * @static
	 * @return Main - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}