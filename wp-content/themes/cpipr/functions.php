<?php
/**
 * Constants
 */
// This site is an INN Member
if ( ! defined( 'INN_MEMBER' ) ) {
        define( 'INN_MEMBER', true );
}
// This site is hosted by INN
if ( ! defined( 'INN_HOSTED' ) ) {
        define( 'INN_HOSTED', true );
}

/**
 * LARGO_DEBUG defines whether or not to use minified assets
 *
 * Largo by default uses minified CSS and JavaScript files.
 * set LARGO_DEBUG to TRUE to use unminified JavaScript files
 * and unminified CSS files with sourcemaps to our LESS files.
 *
 * @since 0.5
 */

//use the Largo metabox API
require_once( get_template_directory() . '/largo-apis.php' );

/**
 * Includes
 */
$includes = array(
	'/inc/tax-landing-customizations.php',
);
// Perform load
foreach ( $includes as $include ) {
	require_once( get_stylesheet_directory() . $include );
}

//child theme text domain
add_action( 'after_setup_theme', 'cpipr_theme_setup' );
function cpipr_theme_setup() {
    load_child_theme_textdomain( 'cpipr', get_stylesheet_directory() . '/lang' );
}

//load typekit
add_action( 'wp_head', 'cpipr_typekit' );
function cpipr_typekit() { ?>
	<script src="https://use.typekit.net/dyj6ctr.js"></script>
	<script>try{Typekit.load({ async: true });}catch(e){}</script>
<?php
}

function cpipr_styles() {
	$suffix = (LARGO_DEBUG)? '' : '.min';

	wp_dequeue_style( 'largo-child-styles' );
	wp_enqueue_style( 'cpipr-styles', get_stylesheet_directory_uri().'/css/style' . $suffix . '.css' );
	wp_dequeue_script( 'largo-navigation' );
}
add_action( 'wp_enqueue_scripts', 'cpipr_styles', 20 );


//allow admins to add users without requiring email confirmation
function auto_activate_users($user, $user_email, $key, $meta){
  wpmu_activate_signup($key);
  return false;
}
add_filter( 'wpmu_signup_user_notification', 'auto_activate_users', 10, 4);
add_filter( 'wpmu_signup_user_notification', '__return_false' );


// spanish date format is different, we should fix this in Largo at some point
// https://github.com/INN/largo/issues/1480
function largo_time( $echo = true, $post = null ) {
	$post = get_post( $post );
	$the_time = get_the_time( 'U', $post );
	$time_difference = current_time( 'timestamp' ) - $the_time;

	if ( $time_difference < 86400 ) {
		$output = sprintf(
			// translators: %s is a number of hours as related by human_time_diff().
			__( '<span class="time-ago">%s ago</span>', 'largo' ),
			human_time_diff( $the_time, current_time( 'timestamp' ) )
		);
	} else {
		$output = get_the_date( 'j \d\e F Y', $post->ID );
	}

	if ( $echo ) {
		echo $output;
	}
	return $output;
}

//add a meta box for a subtitle on posts
largo_add_meta_box(
	'subtitle',
	'Subtitle',
	'subtitle_meta_box_display',
	'post',
	'normal',
	'core'
);
function subtitle_meta_box_display() {
	global $post;
	$values = get_post_custom( $post->ID );
	wp_nonce_field( 'largo_meta_box_nonce', 'meta_box_nonce' );
	?>
	<label for="subtitle"><?php _e( 'Subtitle', 'cpipr' ); ?></label>
	<textarea name="subtitle" id="subtitle" class="widefat" rows="2" cols="20"><?php if ( isset ( $values['subtitle'] ) ) echo $values['subtitle'][0]; ?></textarea>
	<?php
}
largo_register_meta_input( 'subtitle', 'sanitize_text_field' );

//add a meta box for english version URL
largo_add_meta_box(
	'en_version_url',
	'English Version URL',
	'en_version_url_meta_box_display',
	'post',
	'side',
	'default'
);
function en_version_url_meta_box_display() {
	global $post;
	$values = get_post_custom( $post->ID );
	wp_nonce_field( 'largo_meta_box_nonce', 'meta_box_nonce' );
	?>
	<label for="en_version_url"><?php _e( 'English Version URL (include http://)', 'cpipr' ); ?></label>
	<input name="en_version_url" id="en_version_url" style="width:90%;" value="<?php if ( isset ( $values['en_version_url'] ) ) echo $values['en_version_url'][0]; ?>" style="width:90%;" />
	<?php
}
largo_register_meta_input( 'en_version_url', 'sanitize_text_field' );

// Loading localization from child theme (we should consider commiting the fix on the largo theme)
load_theme_textdomain('largo', get_stylesheet_directory() . '/lang');

function widget_autores_init() {
	register_sidebar( array(
		'name'          => __( 'Biografia autores', 'twentyseventeen' ),
		'id'            => 'sidebar-4',
		'description'   => __( 'Add widgets here.', 'twentyseventeen' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'widget_autores_init' );

/**
 * Counter Hamburger menu
 */
function cpipr_counter_hamburger_menu() {
	echo "
				<script>
				jQuery(document).ready(function($) {
					counterHamburguer = false;

					$('#menu-btn').click(function () {
							if(counterHamburguer == false){
									$('#menu-header').slideDown('slow');
									counterHamburguer = true;
							}else{
									$('#menu-header').slideUp('slow');
									counterHamburguer = false;
							}
					});
				});
				</script>";
}
add_action( 'wp_footer', 'cpipr_counter_hamburger_menu' );

