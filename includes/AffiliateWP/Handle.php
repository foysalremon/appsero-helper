<?php
namespace Appsero\Helper\AffiliateWP;

// use Affiliate_WP_Base;

/**
 * Handle class
 *
 * Manage everything that are needed for AffiliateWP
 */
class Handle  {

    /**
     * Constructor
     */
    public function __construct() {
        add_filter( 'affwp_extended_integrations', [ $this, 'add_integration' ] );

        // add_action( 'wp_head', [ $this, 'wp_head' ] );

        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

        add_filter( 'script_loader_tag', [ $this, 'script_tag_loader' ], 10, 3 );
    }

    /**
     * Add new integration to AffiliateWP
     */
    public function add_integration( $integrations ) {
        $path = __DIR__ . '/FastSpring_Integration.php';

        require_once $path;

        $integrations['fastspring'] = [
            'name'     => 'FastSpring',
            'class'    => FastSpring_Integration::class,
            'file'     => $path,
            // 'enabled'  => true,
            // 'supports' => [ 'sales_reporting' ],
        ];

        return $integrations;
    }

    /**
     * Place codes in header
     */
    public function wp_head() {
        ?>
        <script
            id="fsc-api"
            src="https://d1f8f9xcsvx3ha.cloudfront.net/sbl/0.8.3/fastspring-builder.min.js"
            type="text/javascript"
            data-storefront="sourov.test.onfastspring.com/popup-sourov"
            data-popup-closed="appseroFastSpringPopupClosed"
        >
        </script>

        <script>
        function appseroFastSpringPopupClosed( order ) {
            console.log(order);
        }
        </script>
        <?php
    }

    /**
     * Load scripts to frontend
     */
    public function enqueue_scripts() {
        wp_enqueue_script( 'fastspring-builder', 'https://d1f8f9xcsvx3ha.cloudfront.net/sbl/0.8.3/fastspring-builder.min.js', [], false, false );

        wp_enqueue_script( 'fastspring-affiliate-wp', ASHP_ROOT_URL . 'assets/js/fastspring-affiliate-wp.js', [ 'jquery' ], false, true );

        wp_localize_script( 'fastspring-affiliate-wp', 'appseroFastSpringAffwp', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        ] );
    }

    /**
     * Modify script tag
     */
    public function script_tag_loader( $tag, $handle, $src ) {
        if ( 'fastspring-builder' === $handle ) {
            $storefront = get_option( 'appsero_fastspring_storefront_path', '' );

            $tag = str_replace( 'src=', 'id="fsc-api" data-storefront="' . $storefront . '" data-popup-closed="appseroFastSpringPopupClosed" src=', $tag );
        }

        return $tag;
    }

}
