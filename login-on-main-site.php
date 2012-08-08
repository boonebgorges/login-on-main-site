<?php
/**
 * Ensure that all logins happen on the main site
 *
 * @license GPLv2
 * @author Boone Gorges
 */
class BBG_Main_Site_Login {
	var $main_site_id;

	function __construct() {
		global $current_site, $current_blog;

		$this->main_site_id = $current_site->blog_id;

		add_action( 'login_url', array( $this, 'login_url' ) );
		add_action( 'init', array( $this, 'protect_login' ) );
	}

	/**
	 * When the login url is called in the template, swap out with the main site's
	 */
	function login_url( $url, $redirect ) {
		global $current_site, $current_blog;

		if ( (int) $current_blog->blog_id == (int) $current_site->blog_id ) {
			return;
		}

		return $this->get_main_site_login( $redirect );
	}

	/**
	 * If someone goes to the admin URL directly, redirect them to the main site
	 */
	function protect_login( $redirect ) {
		global $current_site, $current_blog;

		if ( false === strpos( $_SERVER['SCRIPT_NAME'], 'wp-login.php' ) ) {
			return;
		}

		if ( (int) $current_blog->blog_id == (int) $current_site->blog_id ) {
			return;
		}

		wp_redirect( $this->get_main_site_login() );
	}

	/**
	 * Utility function to get the URL of the main site's login page
	 */
	function get_main_site_login( $redirect = '' ) {

		$main_site_url = get_blog_option( $this->main_site_id, 'siteurl' );
		$main_site_login = $main_site_url . '/' . 'wp-login.php';

		if ( empty( $redirect ) ) {
			if ( !empty( $_GET['redirect_to'] ) ) {
				$redirect = $_GET['redirect_to'];
			} else {
				$redirect = urlencode( admin_url() );
			}
		}

		if ( !empty( $redirect ) ) {
			$main_site_login = add_query_arg( 'redirect_to', $redirect, $main_site_login );
		}

		return $main_site_login;
	}
}
new BBG_Main_Site_Login();
?>
