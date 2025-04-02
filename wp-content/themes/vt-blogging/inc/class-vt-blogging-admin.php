<?php
/**
 * VT Blogging Admin Class.
 *
 * @author  VolThemes
 * @package vt-blogging
 * @since   vt-blogging 1.0.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'VT_Blogging_admin' ) ) :

	/**
	 * VT_Blogging_admin Class.
	 */
	class VT_Blogging_admin {

		/**
		 * Constructor.
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'wp_loaded', array( __CLASS__, 'hide_notices' ) );
			add_action( 'load-themes.php', array( $this, 'admin_notice' ) );
		}

		/**
		 * Add admin menu.
		 */
		public function admin_menu() {
			$theme = wp_get_theme( get_template() );

			$page = add_theme_page( esc_html__( 'About', 'vt-blogging' ) . ' ' . $theme->display( 'Name' ), esc_html__( 'About', 'vt-blogging' ) . ' ' . $theme->display( 'Name' ), 'activate_plugins', 'vt-blogging-welcome', array(
				$this,
				'welcome_screen',
			) );
			add_action( 'admin_print_styles-' . $page, array( $this, 'enqueue_styles' ) );
		}

		/**
		 * Enqueue styles.
		 */
		public function enqueue_styles() {
			global $vt_blogging_version;

			wp_enqueue_style( 'vt-blogging-welcome', get_template_directory_uri() . '/assets/css/welcome.css', array(), $vt_blogging_version );
		}

		/**
		 * Add admin notice.
		 */
		public function admin_notice() {
			global $vt_blogging_version, $pagenow;

			wp_enqueue_style( 'vt-blogging-message', get_template_directory_uri() . '/assets/css/message.css', array(), $vt_blogging_version );

			// Let's bail on theme activation.
			if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) {
				add_action( 'admin_notices', array( $this, 'welcome_notice' ) );
				update_option( 'vt_blogging_admin_notice_welcome', 1 );

				// No option? Let run the notice wizard again..
			} elseif ( ! get_option( 'vt_blogging_admin_notice_welcome' ) ) {
				add_action( 'admin_notices', array( $this, 'welcome_notice' ) );
			}
		}

		/**
		 * Hide a notice if the GET variable is set.
		 */
		public static function hide_notices() {
			if ( isset( $_GET['vt-blogging-hide-notice'] ) && isset( $_GET['_vt_blogging_notice_nonce'] ) ) {
				if ( ! wp_verify_nonce( $_GET['_vt_blogging_notice_nonce'], 'vt_blogging_hide_notices_nonce' ) ) {
					wp_die( __( 'Action failed. Please refresh the page and retry.', 'vt-blogging' ) );
				}

				if ( ! current_user_can( 'manage_options' ) ) {
					wp_die( __( 'Cheatin&#8217; huh?', 'vt-blogging' ) );
				}

				$hide_notice = sanitize_text_field( $_GET['vt-blogging-hide-notice'] );
				update_option( 'vt_blogging_admin_notice_' . $hide_notice, 1 );
			}
		}

		/**
		 * Show welcome notice.
		 */
		public function welcome_notice() {
			?>
			<div id="message" class="updated vt-blogging-message">
				<a class="vt-blogging-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( remove_query_arg( array( 'activated' ), add_query_arg( 'vt-blogging-hide-notice', 'welcome' ) ), 'vt_blogging_hide_notices_nonce', '_vt_blogging_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'vt-blogging' ); ?></a>
				<p><?php printf( esc_html__( 'Welcome! Thank you for choosing VT Blogging Theme! To fully take advantage of the best our theme can offer please make sure you visit our %swelcome page%s.', 'vt-blogging' ), '<a href="' . esc_url( admin_url( 'themes.php?page=vt-blogging-welcome' ) ) . '">', '</a>' ); ?></p>
				<p class="submit">
					<a class="button-secondary" href="<?php echo esc_url( admin_url( 'themes.php?page=vt-blogging-welcome' ) ); ?>"><?php esc_html_e( 'Get started with VT Blogging Lite', 'vt-blogging' ); ?></a>
				</p>
			</div>
			<?php
		}

		/**
		 * Intro text/links shown to all about pages.
		 *
		 * @access private
		 */
		private function intro() {
			global $vt_blogging_version;

			$theme = wp_get_theme( get_template() );

			// Drop minor version if 0
			$major_version = substr( $vt_blogging_version, 0, 3 );
			?>
			<div class="vt-blogging-theme-info">
				<h1>
					<?php esc_html_e( 'About', 'vt-blogging' ); ?>
					<?php echo $theme->display( 'Name' ); ?>
					<?php printf( '%s', $major_version ); ?>
				</h1>

				<div class="welcome-description-wrap">
					<div class="about-text"><?php echo $theme->display( 'Description' ); ?></div>

					<div class="vt-blogging-screenshot">
						<img src="<?php echo esc_url( get_template_directory_uri() ) . '/screenshot.png'; ?>" />
					</div>
				</div>
			</div>

			<p class="vt-blogging-actions">
				<a href="<?php echo esc_url( 'https://volthemes.com/themes/vt-blogging/?utm_source=vt-blogging-about&utm_medium=theme-info-link' ); ?>" class="button button-secondary" target="_blank"><?php esc_html_e( 'Theme Info', 'vt-blogging' ); ?></a>

				<a href="<?php echo esc_url( 'https://volthemes.com/demo/?theme=VT-Blogging' ); ?>" class="button button-secondary docs" target="_blank"><?php esc_html_e( 'View Demo', 'vt-blogging' ); ?></a>

				<a href="<?php echo esc_url( 'https://volthemes.com/theme/vt-blogging-pro/?utm_source=vt-blogging-about' ); ?>" class="button button-primary docs" target="_blank"><?php esc_html_e( 'View PRO version', 'vt-blogging' ); ?></a>

				<a href="<?php echo esc_url( 'https://wordpress.org/support/theme/vt-blogging/reviews/?filter=5' ); ?>" class="button button-secondary docs" target="_blank"><?php esc_html_e( 'Rate this theme', 'vt-blogging' ); ?></a>
			</p>

			<h2 class="nav-tab-wrapper">
				<a class="nav-tab <?php if ( empty( $_GET['tab'] ) && $_GET['page'] == 'vt-blogging-welcome' ) {
					echo 'nav-tab-active';
				} ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'vt-blogging-welcome' ), 'themes.php' ) ) ); ?>">
					<?php echo $theme->display( 'Name' ); ?>
				</a>
				<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'supported_plugins' ) {
					echo 'nav-tab-active';
				} ?>" href="<?php echo esc_url( admin_url( add_query_arg( array(
					'page' => 'vt-blogging-welcome',
					'tab'  => 'supported_plugins',
				), 'themes.php' ) ) ); ?>">
					<?php esc_html_e( 'Supported Plugins', 'vt-blogging' ); ?>
				</a>
				<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'free_vs_pro' ) {
					echo 'nav-tab-active';
				} ?>" href="<?php echo esc_url( admin_url( add_query_arg( array(
					'page' => 'vt-blogging-welcome',
					'tab'  => 'free_vs_pro',
				), 'themes.php' ) ) ); ?>">
					<?php esc_html_e( 'Free Vs Pro', 'vt-blogging' ); ?>
				</a>
				<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'changelog' ) {
					echo 'nav-tab-active';
				} ?>" href="<?php echo esc_url( admin_url( add_query_arg( array(
					'page' => 'vt-blogging-welcome',
					'tab'  => 'changelog',
				), 'themes.php' ) ) ); ?>">
					<?php esc_html_e( 'Changelog', 'vt-blogging' ); ?>
				</a>
			</h2>
			<?php
		}

		/**
		 * Welcome screen page.
		 */
		public function welcome_screen() {
			$current_tab = empty( $_GET['tab'] ) ? 'about' : sanitize_title( $_GET['tab'] );

			// Look for a {$current_tab}_screen method.
			if ( is_callable( array( $this, $current_tab . '_screen' ) ) ) {
				return $this->{$current_tab . '_screen'}();
			}

			// Fallback to about screen.
			return $this->about_screen();
		}

		/**
		 * Output the about screen.
		 */
		public function about_screen() {
			$theme = wp_get_theme( get_template() );
			?>
			<div class="wrap about-wrap">

				<?php $this->intro(); ?>

				<div class="changelog point-releases">
					<div class="under-the-hood two-col">
						<div class="col">
							<h3><?php esc_html_e( 'Theme Customizer', 'vt-blogging' ); ?></h3>
							<p><?php esc_html_e( 'All Theme Options are available via Customize screen.', 'vt-blogging' ) ?></p>
							<p>
								<a href="<?php echo admin_url( 'customize.php' ); ?>" class="button button-secondary"><?php esc_html_e( 'Customize', 'vt-blogging' ); ?></a>
							</p>
						</div>

						<div class="col">
							<h3><?php esc_html_e( 'Documentation', 'vt-blogging' ); ?></h3>
							<p><?php esc_html_e( 'Please view our documentation page to setup the theme.', 'vt-blogging' ) ?></p>
							<p>
								<a href="<?php echo esc_url( 'https://volthemes.com/docs/vt-blogging-documentation/?utm_source=vt-blogging-about&utm_medium=documentation' ); ?>" class="button button-secondary" target="_blank"><?php esc_html_e( 'Documentation', 'vt-blogging' ); ?></a>
							</p>
						</div>

						<div class="col">
							<h3><?php esc_html_e( 'Got theme support question?', 'vt-blogging' ); ?></h3>
							<p><?php esc_html_e( 'Please put it in our dedicated support forum.', 'vt-blogging' ) ?></p>
							<p>
								<a href="<?php echo esc_url( 'https://wordpress.org/support/theme/vt-blogging' ); ?>" class="button button-secondary" target="_blank"><?php esc_html_e( 'Support Forum', 'vt-blogging' ); ?></a>
							</p>
						</div>

						<div class="col">
							<h3><?php esc_html_e( 'Need more features?', 'vt-blogging' ); ?></h3>
							<p><?php esc_html_e( 'Upgrade to PRO version for more exciting features.', 'vt-blogging' ) ?></p>
							<p>
								<a href="<?php echo esc_url( 'https://volthemes.com/theme/vt-blogging-pro/?utm_source=vt-blogging-about' ); ?>" class="button button-secondary" target="_blank"><?php esc_html_e( 'View Pro', 'vt-blogging' ); ?></a>
							</p>
						</div>

						<div class="col">
							<h3><?php esc_html_e( 'Got sales related question?', 'vt-blogging' ); ?></h3>
							<p><?php esc_html_e( 'Please send it via our sales contact page.', 'vt-blogging' ) ?></p>
							<p>
								<a href="<?php echo esc_url( 'https://volthemes.com/contact/?utm_source=vt-blogging-about&utm_medium=contact-page-link&utm_campaign=contact-page' ); ?>" class="button button-secondary" target="_blank"><?php esc_html_e( 'Contact Page', 'vt-blogging' ); ?></a>
							</p>
						</div>

						<div class="col">
							<h3>
								<?php
								esc_html_e( 'Translate', 'vt-blogging' );
								echo ' ' . $theme->display( 'Name' );
								?>
							</h3>
							<p><?php esc_html_e( 'Click below to translate this theme into your own language.', 'vt-blogging' ) ?></p>
							<p>
								<a href="<?php echo esc_url( 'https://translate.wordpress.org/projects/wp-themes/vt-blogging' ); ?>" class="button button-secondary" target="_blank">
									<?php
									esc_html_e( 'Translate', 'vt-blogging' );
									echo ' ' . $theme->display( 'Name' );
									?>
								</a>
							</p>
						</div>
					</div>
				</div>

				<div class="return-to-dashboard vt-blogging">
					<?php if ( current_user_can( 'update_core' ) && isset( $_GET['updated'] ) ) : ?>
						<a href="<?php echo esc_url( self_admin_url( 'update-core.php' ) ); ?>">
							<?php is_multisite() ? esc_html_e( 'Return to Updates', 'vt-blogging' ) : esc_html_e( 'Return to Dashboard &rarr; Updates', 'vt-blogging' ); ?>
						</a> |
					<?php endif; ?>
					<a href="<?php echo esc_url( self_admin_url() ); ?>"><?php is_blog_admin() ? esc_html_e( 'Go to Dashboard &rarr; Home', 'vt-blogging' ) : esc_html_e( 'Go to Dashboard', 'vt-blogging' ); ?></a>
				</div>
			</div>
			<?php
		}

		/**
		 * Output the changelog screen.
		 */
		public function changelog_screen() {
			global $wp_filesystem;

			?>
			<div class="wrap about-wrap">

				<?php $this->intro(); ?>

				<p class="about-description"><?php esc_html_e( 'View changelog below:', 'vt-blogging' ); ?></p>

				<?php
				$changelog_file = apply_filters( 'vt_blogging_changelog_file', get_template_directory() . '/changelog.txt' );

				// Check if the changelog file exists and is readable.
				if ( $changelog_file && is_readable( $changelog_file ) ) {
					WP_Filesystem();
					$changelog      = $wp_filesystem->get_contents( $changelog_file );
					$changelog_list = $this->parse_changelog( $changelog );

					echo wp_kses_post( $changelog_list );
				}
				?>
			</div>
			<?php
		}

		/**
		 * Parse changelog from readme file.
		 *
		 * @param  string $content
		 *
		 * @return string
		 */
		private function parse_changelog( $content ) {
			$matches   = null;
			$regexp    = '~==\s*Changelog\s*==(.*)($)~Uis';
			$changelog = '';

			if ( preg_match( $regexp, $content, $matches ) ) {
				$changes = explode( '\r\n', trim( $matches[1] ) );

				$changelog .= '<pre class="changelog">';

				foreach ( $changes as $index => $line ) {
					$changelog .= wp_kses_post( preg_replace( '~(=\s*Version\s*(\d+(?:\.\d+)+)\s*=|$)~Uis', '<span class="title">${1}</span>', $line ) );
				}

				$changelog .= '</pre>';
			}

			return wp_kses_post( $changelog );
		}

		/**
		 * Output the supported plugins screen.
		 */
		public function supported_plugins_screen() {
			?>
			<div class="wrap about-wrap">

				<?php $this->intro(); ?>

				<p class="about-description"><?php esc_html_e( 'This theme recommends following plugins:', 'vt-blogging' ); ?></p>
				<ol>
					<li>
						<a href="<?php echo esc_url( 'https://wordpress.org/plugins/contact-form-7/' ); ?>" target="_blank"><?php esc_html_e( 'Contact Form 7', 'vt-blogging' ); ?></a>
					</li>
					<li>
						<a href="<?php echo esc_url( 'https://wordpress.org/plugins/wordpress-seo/' ); ?>" target="_blank"><?php esc_html_e( 'Yoast SEO', 'vt-blogging' ); ?></a>
					</li>
					<li>
						<a href="<?php echo esc_url( 'https://wordpress.org/plugins/woocommerce/' ); ?>" target="_blank"><?php esc_html_e( 'WooCommerce', 'vt-blogging' ); ?></a>
						<?php esc_html_e( 'Fully Compatible in Pro Version', 'vt-blogging' ); ?>
					</li>
				</ol>

			</div>
			<?php
		}

		/**
		 * Output the free vs pro screen.
		 */
		public function free_vs_pro_screen() {
			?>
			<div class="wrap about-wrap">

				<?php $this->intro(); ?>

				<p class="about-description"><?php esc_html_e( 'Upgrade to PRO version for more exciting features.', 'vt-blogging' ); ?></p>

				<table>
					<thead>
					<tr>
						<th class="table-feature-title"><h3><?php esc_html_e( 'Features', 'vt-blogging' ); ?></h3></th>
						<th><h3><?php esc_html_e( 'VT Blogging Lite', 'vt-blogging' ); ?></h3></th>
						<th><h3><?php esc_html_e( 'VT Blogging Pro', 'vt-blogging' ); ?></h3></th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td><h3><?php esc_html_e( 'SEO Friendly', 'vt-blogging' ); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Translation Ready', 'vt-blogging' ); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'RTL Support', 'vt-blogging' ); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></td>
						<td><span class="dashicons dashicons-yes"></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Custom Menu', 'vt-blogging' ); ?></h3></td>
						<td><?php esc_html_e( '1', 'vt-blogging' ); ?></td>
						<td><?php esc_html_e( '2', 'vt-blogging' ); ?></td>
					</tr>
						<td><h3><?php esc_html_e( 'Color Options', 'vt-blogging' ); ?></h3></td>
						<td><?php esc_html_e( '5', 'vt-blogging' ); ?></td>
						<td><?php esc_html_e( '35+ color options', 'vt-blogging' ); ?></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Layout option', 'vt-blogging' ); ?></h3></td>
						<td><?php esc_html_e( '2', 'vt-blogging' ); ?></td>
						<td><?php esc_html_e( '5', 'vt-blogging' ); ?></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Social Icons', 'vt-blogging' ); ?></h3></td>
						<td><?php esc_html_e( '5', 'vt-blogging' ); ?></td>
						<td><?php esc_html_e( '9+', 'vt-blogging' ); ?></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Additional Custom Widget', 'vt-blogging' ); ?></h3></td>
						<td><?php esc_html_e( '2', 'vt-blogging' ); ?></td>
						<td><?php esc_html_e( '10', 'vt-blogging' ); ?></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Google Fonts Option', 'vt-blogging' ); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><?php esc_html_e( '600+', 'vt-blogging' ); ?></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Slider', 'vt-blogging' ); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Sticky Header', 'vt-blogging' ); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Sticky Sidebar', 'vt-blogging' ); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Font Settings', 'vt-blogging' ); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Animation', 'vt-blogging' ); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
					<tr>
						<td><h3><?php esc_html_e( 'Blog & Post Settings', 'vt-blogging' ); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Override Theme Text', 'vt-blogging' ); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Woocommerce Compatible', 'vt-blogging' ); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Instagram Feed', 'vt-blogging' ); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Footer Copyright Editor', 'vt-blogging' ); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Custom Scripts Editor', 'vt-blogging' ); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Content Demo', 'vt-blogging' ); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Support', 'vt-blogging' ); ?></h3></td>
						<td><?php esc_html_e( 'Forum', 'vt-blogging' ); ?></td>
						<td><?php esc_html_e( 'Support Forum + Emails/Support Ticket', 'vt-blogging' ); ?></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td class="btn-wrapper">
							<a href="<?php echo esc_url( apply_filters( 'vt_blogging_pro_theme_url', 'https://volthemes.com/theme/vt-blogging/?utm_source=vt-blogging-about&utm_medium=view-pro-link&utm_campaign=view-pro#free-vs-pro' ) ); ?>" class="button button-secondary docs" target="_blank"><?php esc_html_e( 'View Pro', 'vt-blogging' ); ?></a>
						</td>
					</tr>
					</tbody>
				</table>

			</div>
			<?php
		}
	}

endif;

return new VT_Blogging_admin();