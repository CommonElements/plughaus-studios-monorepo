<?php

namespace LicenseManagerForWooCommerce;

use LicenseManagerForWooCommerce\Enums\LicenseStatus;
use LicenseManagerForWooCommerce\Lists\APIKeyList;
use LicenseManagerForWooCommerce\Lists\GeneratorsList;
use LicenseManagerForWooCommerce\Lists\LicensesList;
use LicenseManagerForWooCommerce\Lists\ActivationsList;
use LicenseManagerForWooCommerce\Models\Resources\ApiKey as ApiKeyResourceModel;
use LicenseManagerForWooCommerce\Models\Resources\License as LicenseResourceModel;
use LicenseManagerForWooCommerce\Repositories\Resources\ApiKey as ApiKeyResourceRepository;
use LicenseManagerForWooCommerce\Repositories\Resources\Generator as GeneratorResourceRepository;
use LicenseManagerForWooCommerce\Repositories\Resources\License as LicenseResourceRepository;

defined('ABSPATH') || exit;

if (class_exists('AdminMenus', false)) {
    return new AdminMenus();
}

class AdminMenus
{
    /**
     * @var array
     */
    private $tabWhitelist;

    /**
	 * Product page slug.
	 */
	const PRODUCT_PAGE = 'edit.php?post_type=product';

    /**
     * Licenses page slug.
     */
    const LICENSES_PAGE = 'lmfwc_licenses';

    /**
     * Generators page slug.
     */
    const GENERATORS_PAGE = 'lmfwc_generators';

     /**
     * Generators page slug.
     */
    const ACTIVATIONS_PAGE = 'lmfwc_activations';

    /**
     * Settings page slug.
     */
    const SETTINGS_PAGE = 'lmfwc_settings';

    /**
     * WC Settings page slug.
     */
    const WC_SETTINGS_PAGE = 'wc-settings';

    /**
     * @var LicensesList
     */
    private $licenses;

    /**
     * @var GeneratorsList
     */
    private $generators;

    /**
     * @var ActivationsList
     */
    private $activations;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->tabWhitelist = array('general', 'woocommerce', 'rest_api', 'tools');

        // Plugin pages.
        add_action('admin_menu', array($this, 'createPluginPages'), 10);
        add_action('admin_init', array($this, 'initSettingsAPI'));

        // Screen options
        add_filter('set-screen-option', array($this, 'setScreenOption'), 10, 3);

        // Footer text
        add_filter('admin_footer_text', array($this, 'adminFooterText'), 1);
        // Add the tab to the tabs array
        add_filter( 'woocommerce_settings_tabs_array', array( $this, 'createSettingsTab' ), 99 );
        // Add settings html
        add_action( 'woocommerce_after_settings_' . self::SETTINGS_PAGE, array( $this, 'settingsPage' ) );
    }

     /**
     * Returns an array of all settings tab.
     *
     * @return array
     */
    public function createSettingsTab($settings_tab) {
        $settings_tab[self::SETTINGS_PAGE] = esc_html__( 'License Manager', 'license-manager-for-woocommerce' );
            return $settings_tab;
    }

    /**
     * Returns an array of all plugin pages.
     *
     * @return array
     */
    public function getPluginPageIDs()
    {
        return array(
            'product_page_lmfwc_licenses',
            'product_page_lmfwc_generators',
            'woocommerce_page_lmfwc_settings'
        );
    }

    /**
     * Sets up all necessary plugin pages.
     */
    public function createPluginPages()
    {

        $licensesHook = add_submenu_page(
            self::PRODUCT_PAGE,
            esc_html__('License Keys', 'license-manager-for-woocommerce'),
            esc_html__('License Keys', 'license-manager-for-woocommerce'),
            'manage_options',
            self::LICENSES_PAGE,
            array($this, 'licensesPage')
        );
        add_action('load-' . $licensesHook, array($this, 'licensesPageScreenOptions'));

        // Generators List Page
        $generatorsHook = add_submenu_page(
			self::PRODUCT_PAGE,
			__('Generators', 'license-manager-for-woocommerce'),
			__('Generators', 'license-manager-for-woocommerce'),
			'manage_options',
			self::GENERATORS_PAGE,
			array( $this, 'generatorsPage' )
		);
		add_action('load-' . $generatorsHook, array( $this, 'generatorsPageScreenOptions' ));

        $activationsHook = add_submenu_page(
            self::PRODUCT_PAGE,
            esc_html__('Activations', 'license-manager-for-woocommerce'),
            esc_html__('Activations', 'license-manager-for-woocommerce'),
            'manage_options',
            self::ACTIVATIONS_PAGE,
            array($this, 'activationsPage')
        );
        add_action('load-' . $activationsHook, array($this, 'activationsPageScreenOptions'));
    }

    /**
     * Adds the supported screen options for the licenses list.
     */
    public function licensesPageScreenOptions()
    {
        $option = 'per_page';
        $args = array(
            'label' => esc_html__('License keys per page', 'license-manager-for-woocommerce'),
            'default' => 10,
            'option' => 'lmfwc_licenses_per_page'
        );

        add_screen_option($option, $args);

        $this->licenses = new LicensesList();
    }

    /**
     * Adds the supported screen options for the generators list.
     */
    public function generatorsPageScreenOptions()
    {
        $option = 'per_page';
        $args = array(
            'label'   => esc_html__('Generators per page', 'license-manager-for-woocommerce'),
            'default' => 10,
            'option'  => 'generators_per_page'
        );

        add_screen_option($option, $args);

        $this->generators = new GeneratorsList;
    }

    /**
     * Set up the activations page
     */
    public function activationsPage() {

        $activations = $this->activations;
        $action      = $this->getCurrentAction( $default = 'list' );

        include LMFWC_TEMPLATES_DIR . 'page-activations.php';
    }

    /**
     * Adds the supported screen options for the generators list.
     */
    public function activationsPageScreenOptions()
    {
        $option = 'per_page';
        $args = array(
            'label'   => esc_html__('Activations per page', 'license-manager-for-woocommerce'),
            'default' => 10,
            'option'  => 'activations_per_page'
        );

        add_screen_option($option, $args);

        $this->activations = new ActivationsList;
    }
/**
	 * Sets up the license page.
	 */
    public function licensesPage() {
		$lmfwc_data = $_REQUEST;
		$default = 'list';
		$action = $this->getCurrentAction($default);
		$licenses = $this->licenses;
		$addLicenseUrl = admin_url(
			sprintf(
				'%s&page=%s&action=add&_wpnonce=%s', self::PRODUCT_PAGE,
				self::LICENSES_PAGE,
				wp_create_nonce('add')
			)
		);
		$importLicenseUrl = admin_url(
			sprintf(
				'%s&page=%s&action=import&_wpnonce=%s', self::PRODUCT_PAGE,
				self::LICENSES_PAGE,
				wp_create_nonce('import')
			)
		);

		// Edit license keys
		if ( 'edit' === $action ) {
			if (!current_user_can('manage_options')) {
				wp_die(esc_html__('Insufficient permission', 'license-manager-for-woocommerce'));
			}

			/**
			 *  LicenseResourceRepository find license
			 * 
			 * @var LicenseResourceRepository $license 
			**/
			$license = LicenseResourceRepository::instance()->find(absint($lmfwc_data['id']));
			$expiresAt = null;

			if ($license->getExpiresAt()) {
				try {
					$expiresAtDateTime = new \DateTime($license->getExpiresAt());
					$expiresAt = $expiresAtDateTime->format('Y-m-d');
				} catch (\Exception $e) {
					$expiresAt = null;
				}
			}

			if (!$license) {
				wp_die(esc_html__('Invalid license key ID', 'license-manager-for-woocommerce'));
			}

			$licenseKey = $license->getDecryptedLicenseKey();
		}

		// Edit, add or import license keys
		if ( 'edit' === $action   || 'add' === $action   || 'import'  === $action ) {
			wp_enqueue_style('lmfwc-jquery-ui-datepicker');
			wp_enqueue_script('jquery-ui-datepicker');
			$statusOptions = LicenseStatus::dropdown();
		}

		include LMFWC_TEMPLATES_DIR . 'page-licenses.php';
	}

	/**
	 * Sets up the generators page.
	 */
	public function generatorsPage() {
		$lmfwc_data = $_REQUEST;
		$generators = $this->generators;
		$default = 'list';
		$action = $this->getCurrentAction($default);

		// List generators
		if ( 'list' === $action || 'delete'  === $action ) {
			$addGeneratorUrl = wp_nonce_url(
				sprintf(
					admin_url('%s&page=%s&action=add'), self::PRODUCT_PAGE,
					self::GENERATORS_PAGE
				),
				'lmfwc_add_generator'
			);
			$generateKeysUrl = wp_nonce_url(
				sprintf(
					admin_url('%s&page=%s&action=generate'), self::PRODUCT_PAGE,
					self::GENERATORS_PAGE
				),
				'lmfwc_generate_keys'
			);
		}

		// Edit generators
		if ( 'edit'  === $action ) {
			if (!current_user_can('manage_options')) {
				wp_die(esc_html__('Insufficient permission', 'license-manager-for-woocommerce'));
			}

			if (!array_key_exists('edit', $lmfwc_data) && !array_key_exists('id', $lmfwc_data)) {
				return;
			}
			$generator = GeneratorResourceRepository::instance()->find($lmfwc_data['id']);
			if (! $generator) {
				return;
			}
			/**
			* Filter lmfwc_get_assigned_products
			* 
			* @since 1.0
			**/
			$products = apply_filters('lmfwc_get_assigned_products', $lmfwc_data['id']);
		}

		// Generate license keys
		if ( 'generate' === $action  ) {
			$generatorsDropdown = GeneratorResourceRepository::instance()->findAll();
			$statusOptions      = LicenseStatus::dropdown();

			if (!$generatorsDropdown) {
				$generatorsDropdown = array();
			}
		}

		include LMFWC_TEMPLATES_DIR . 'page-generators.php';
	}


    /**
     * Sets up the settings page.
     */
    public function settingsPage()
    {
        $section            = $this->getCurrentSection();
        $urlGeneral     = admin_url( sprintf( 'admin.php?page=%s&tab=%2s&section=general',      self::WC_SETTINGS_PAGE, self::SETTINGS_PAGE ) );
        $urlWooCommerce = admin_url( sprintf( 'admin.php?page=%s&tab=%2s&section=woocommerce', self::WC_SETTINGS_PAGE, self::SETTINGS_PAGE ) );
        $urlRestApi     = admin_url( sprintf( 'admin.php?page=%s&tab=%2s&section=rest_api',     self::WC_SETTINGS_PAGE, self::SETTINGS_PAGE ) );
        $urlTools       = admin_url( sprintf( 'admin.php?page=%s&tab=%2s&section=tools',        self::WC_SETTINGS_PAGE, self::SETTINGS_PAGE ) );

        if ($section == 'rest_api') {
            if (isset($_GET['create_key'])) {
                $action = 'create';
            } elseif (isset($_GET['edit_key'])) {
                $action = 'edit';
            } elseif (isset($_GET['show_key'])) {
                $action = 'show';
            } else {
                $action = 'list';
            }

            switch ($action) {
                case 'create':
                case 'edit':
                    $keyId   = 0;
                    $keyData = new ApiKeyResourceModel();
                    $userId  = null;
                    $date    = null;

                    if (array_key_exists('edit_key', $_GET)) {
                        $keyId = absint($_GET['edit_key']);
                    }

                if ($keyId !== 0) {
                        /** @var ApiKeyResourceModel $keyData */
                        $keyData = ApiKeyResourceRepository::instance()->find($keyId);

                        if ($keyData !== null) {
                            $userId  = (int)$keyData->getUserId();

                            $lastAccess = $keyData->getLastAccess();
                            if ($lastAccess !== null) {
                                $date = sprintf(
                                    // Translators: Date and time format for displaying last access date.
                                    esc_html__('%1$s at %2$s', 'license-manager-for-woocommerce'),
                                    date_i18n(wc_date_format(), strtotime($lastAccess)),
                                    date_i18n(wc_time_format(), strtotime($lastAccess))
                                );
                            } 
                        } 
                    }

                    $users       = apply_filters('lmfwc_get_users', get_users(array( 'fields' => array( 'user_login', 'user_email', 'ID' ))));
                    $permissions = array(
                        'read'       => esc_html__('Read', 'license-manager-for-woocommerce'),
                        'write'      => esc_html__('Write', 'license-manager-for-woocommerce'),
                        'read_write' => esc_html__('Read/Write', 'license-manager-for-woocommerce'),
                    );

                    if ($keyId && $userId && ! current_user_can('edit_user', $userId)) {
                        if (get_current_user_id() !== $userId) {
                            wp_die(
                                esc_html__(
                                    'You do not have permission to edit this API Key',
                                    'license-manager-for-woocommerce'
                                )
                            );
                        }
                    }
                    break;
                case 'list':
                    $keys = new APIKeyList();
                    break;
                case 'show':
                    $keyData     = get_transient('lmfwc_api_key');
                    $consumerKey = get_transient('lmfwc_consumer_key');

                    delete_transient('lmfwc_api_key');
                    delete_transient('lmfwc_consumer_key');
                    break;
            }

            // Add screen option.
            add_screen_option(
                'per_page',
                array(
                    'default' => 10,
                    'option'  => 'lmfwc_keys_per_page',
                )
            );
        }

        include LMFWC_TEMPLATES_DIR . 'page-settings.php';
    }

    /**
     * Initialized the plugin Settings API.
     */
    public function initSettingsAPI()
    {
        new Settings();
    }

    /**
     * Displays the new screen options.
     *
     * @param bool   $keep
     * @param string $option
     * @param int    $value
     *
     * @return int
     */
    public function setScreenOption($keep, $option, $value)
    {
        return $value;
    }

    /**
     * Sets the custom footer text for the plugin pages.
     *
     * @param string $footerText
     *
     * @return string
     */
    public function adminFooterText($footerText)
    {
        if (!current_user_can('manage_options') || !function_exists('wc_get_screen_ids')) {
            return $footerText;
        }

        $currentScreen = get_current_screen();

        // Check to make sure we're on a WooCommerce admin page.
        if (isset($currentScreen->id) && in_array($currentScreen->id, $this->getPluginPageIDs())) {
            // Change the footer text
            $footerText = sprintf(
                // Translators: Placeholder 1 is replaced with "License Manager for WooCommerce" (HTML strong tag), Placeholder 2 is replaced with a link to rate the plugin with 5 stars.
                __('If you like %1$s please leave us a %2$s rating. A huge thanks in advance!', 'license-manager-for-woocommerce'),
                sprintf('<strong>%s</strong>', esc_html__('License Manager for WooCommerce', 'license-manager-for-woocommerce')),
                '<a href="https://wordpress.org/support/plugin/license-manager-for-woocommerce/reviews/?rate=5#new-post" target="_blank" class="wc-rating-link" data-rated="' . esc_attr__('Thanks :)', 'license-manager-for-woocommerce') . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
            );
            
        }

        return $footerText;
    }

    /**
     * Retrieves the currently active tab.
     *
     * @return string
     */
    protected function getCurrentSection()
    {
        $section = 'general';

        if (isset($_GET['section']) && in_array($_GET['section'], $this->tabWhitelist)) {
            $section = sanitize_text_field($_GET['section']);
        }

        return $section;
    }

    /**
     * Returns the string value of the "action" GET parameter.
     *
     * @param string $default
     *
     * @return string
     */
    protected function getCurrentAction($default)
    {
        $action = $default;
        
        if (!isset($_GET['action']) || !is_string($_GET['action'])) {
            return $action;
        }

        return sanitize_text_field($_GET['action']);
    }

}