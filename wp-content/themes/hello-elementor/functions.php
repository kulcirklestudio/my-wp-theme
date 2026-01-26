<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

define('HELLO_ELEMENTOR_VERSION', '3.4.4');
define('EHP_THEME_SLUG', 'hello-elementor');

define('HELLO_THEME_PATH', get_template_directory());
define('HELLO_THEME_URL', get_template_directory_uri());
define('HELLO_THEME_ASSETS_PATH', HELLO_THEME_PATH . '/assets/');
define('HELLO_THEME_ASSETS_URL', HELLO_THEME_URL . '/assets/');
define('HELLO_THEME_SCRIPTS_PATH', HELLO_THEME_ASSETS_PATH . 'js/');
define('HELLO_THEME_SCRIPTS_URL', HELLO_THEME_ASSETS_URL . 'js/');
define('HELLO_THEME_STYLE_PATH', HELLO_THEME_ASSETS_PATH . 'css/');
define('HELLO_THEME_STYLE_URL', HELLO_THEME_ASSETS_URL . 'css/');
define('HELLO_THEME_IMAGES_PATH', HELLO_THEME_ASSETS_PATH . 'images/');
define('HELLO_THEME_IMAGES_URL', HELLO_THEME_ASSETS_URL . 'images/');

if (!isset($content_width)) {
	$content_width = 800; // Pixels.
}

if (!function_exists('hello_elementor_setup')) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup()
	{
		if (is_admin()) {
			hello_maybe_update_theme_version_in_db();
		}

		if (apply_filters('hello_elementor_register_menus', true)) {
			register_nav_menus(['menu-1' => esc_html__('Header', 'hello-elementor')]);
			register_nav_menus(['menu-2' => esc_html__('Footer', 'hello-elementor')]);
		}

		if (apply_filters('hello_elementor_post_type_support', true)) {
			add_post_type_support('page', 'excerpt');
		}

		if (apply_filters('hello_elementor_add_theme_support', true)) {
			add_theme_support('post-thumbnails');
			add_theme_support('automatic-feed-links');
			add_theme_support('title-tag');
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
					'script',
					'style',
					'navigation-widgets',
				]
			);
			add_theme_support(
				'custom-logo',
				[
					'height' => 100,
					'width' => 350,
					'flex-height' => true,
					'flex-width' => true,
				]
			);
			add_theme_support('align-wide');
			add_theme_support('responsive-embeds');

			/*
			 * Editor Styles
			 */
			add_theme_support('editor-styles');
			add_editor_style('editor-styles.css');

			/*
			 * WooCommerce.
			 */
			if (apply_filters('hello_elementor_add_woocommerce_support', true)) {
				// WooCommerce in general.
				add_theme_support('woocommerce');
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support('wc-product-gallery-zoom');
				// lightbox.
				add_theme_support('wc-product-gallery-lightbox');
				// swipe.
				add_theme_support('wc-product-gallery-slider');
			}
		}
	}
}
add_action('after_setup_theme', 'hello_elementor_setup');

function hello_maybe_update_theme_version_in_db()
{
	$theme_version_option_name = 'hello_theme_version';
	// The theme version saved in the database.
	$hello_theme_db_version = get_option($theme_version_option_name);

	// If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
	if (!$hello_theme_db_version || version_compare($hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<')) {
		update_option($theme_version_option_name, HELLO_ELEMENTOR_VERSION);
	}
}

if (!function_exists('hello_elementor_display_header_footer')) {
	/**
	 * Check whether to display header footer.
	 *
	 * @return bool
	 */
	function hello_elementor_display_header_footer()
	{
		$hello_elementor_header_footer = true;

		return apply_filters('hello_elementor_header_footer', $hello_elementor_header_footer);
	}
}

if (!function_exists('hello_elementor_scripts_styles')) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles()
	{
		if (apply_filters('hello_elementor_enqueue_style', true)) {
			wp_enqueue_style(
				'hello-elementor',
				HELLO_THEME_STYLE_URL . 'reset.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if (apply_filters('hello_elementor_enqueue_theme_style', true)) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				HELLO_THEME_STYLE_URL . 'theme.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if (hello_elementor_display_header_footer()) {
			wp_enqueue_style(
				'hello-elementor-header-footer',
				HELLO_THEME_STYLE_URL . 'header-footer.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}
	}
}
add_action('wp_enqueue_scripts', 'hello_elementor_scripts_styles');

if (!function_exists('hello_elementor_register_elementor_locations')) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations($elementor_theme_manager)
	{
		if (apply_filters('hello_elementor_register_elementor_locations', true)) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action('elementor/theme/register_locations', 'hello_elementor_register_elementor_locations');

if (!function_exists('hello_elementor_content_width')) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function hello_elementor_content_width()
	{
		$GLOBALS['content_width'] = apply_filters('hello_elementor_content_width', 800);
	}
}
add_action('after_setup_theme', 'hello_elementor_content_width', 0);

if (!function_exists('hello_elementor_add_description_meta_tag')) {
	/**
	 * Add description meta tag with excerpt text.
	 *
	 * @return void
	 */
	function hello_elementor_add_description_meta_tag()
	{
		if (!apply_filters('hello_elementor_description_meta_tag', true)) {
			return;
		}

		if (!is_singular()) {
			return;
		}

		$post = get_queried_object();
		if (empty($post->post_excerpt)) {
			return;
		}

		echo '<meta name="description" content="' . esc_attr(wp_strip_all_tags($post->post_excerpt)) . '">' . "\n";
	}
}
add_action('wp_head', 'hello_elementor_add_description_meta_tag');

// Settings page
require get_template_directory() . '/includes/settings-functions.php';

// Header & footer styling option, inside Elementor
require get_template_directory() . '/includes/elementor-functions.php';

if (!function_exists('hello_elementor_customizer')) {
	// Customizer controls
	function hello_elementor_customizer()
	{
		if (!is_customize_preview()) {
			return;
		}

		if (!hello_elementor_display_header_footer()) {
			return;
		}

		require get_template_directory() . '/includes/customizer-functions.php';
	}
}
add_action('init', 'hello_elementor_customizer');

if (!function_exists('hello_elementor_check_hide_title')) {
	/**
	 * Check whether to display the page title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title($val)
	{
		if (defined('ELEMENTOR_VERSION')) {
			$current_doc = Elementor\Plugin::instance()->documents->get(get_the_ID());
			if ($current_doc && 'yes' === $current_doc->get_settings('hide_title')) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter('hello_elementor_page_title', 'hello_elementor_check_hide_title');

/**
 * BC:
 * In v2.7.0 the theme removed the `hello_elementor_body_open()` from `header.php` replacing it with `wp_body_open()`.
 * The following code prevents fatal errors in child themes that still use this function.
 */
if (!function_exists('hello_elementor_body_open')) {
	function hello_elementor_body_open()
	{
		wp_body_open();
	}
}

require HELLO_THEME_PATH . '/theme.php';

HelloTheme\Theme::instance();


// Enqueue JavaScript and localize script
add_action('wp_enqueue_scripts', 'enqueue_custom_cart_script');
function enqueue_custom_cart_script()
{
	wp_enqueue_script('custom-cart-script', get_stylesheet_directory_uri() . '/assets/js/custom-cart.js', array('jquery'), '1.0', true);
}


/**************CountDown Widget Recurring Counter****************/
use Elementor\Controls_Manager;

add_action('elementor/element/countdown/section_countdown/before_section_end', function ($element, $args) {
	$element->update_control(
		'countdown_type',
		[
			'options' => [
				'due_date' => esc_html__('Due Date', 'elementor-pro'),
				'evergreen' => esc_html__('Evergreen Timer', 'elementor-pro'),
				'recurring' => esc_html__('Recurring Timer (Daily)', 'elementor-pro'),
			],
		]
	);

	$element->add_control(
		'recurring_start_time',
		[
			'label' => esc_html__('Start Time (Daily)', 'elementor-pro'),
			'type' => Controls_Manager::DATE_TIME,
			'default' => gmdate('Y-m-d 00:00:00'),
			'description' => esc_html__('Daily countdown start time (e.g. 00:00 = midnight).', 'elementor-pro'),
			'condition' => ['countdown_type' => 'recurring'],
		]
	);

	$element->add_control(
		'recurring_end_time',
		[
			'label' => esc_html__('End Time (Daily)', 'elementor-pro'),
			'type' => Controls_Manager::DATE_TIME,
			'default' => gmdate('Y-m-d 15:30:00'),
			'description' => esc_html__('Daily countdown end time (e.g. 15:30 = 3:30 PM).', 'elementor-pro'),
			'condition' => ['countdown_type' => 'recurring'],
		]
	);
}, 10, 2);

add_filter('elementor/widget/render_content', function ($content, $widget) {
	if ('countdown' !== $widget->get_name()) {
		return $content;
	}
	$settings = $widget->get_settings_for_display();
	if (isset($settings['countdown_type']) && $settings['countdown_type'] === 'recurring') {
		$timezone = new \DateTimeZone(wp_timezone_string());
		$now = new \DateTime('now', $timezone);

		$start_time = !empty($settings['recurring_start_time']) ?
			new \DateTime($settings['recurring_start_time'], $timezone) :
			new \DateTime('today 00:00:00', $timezone);

		$end_time = !empty($settings['recurring_end_time'])
			? new \DateTime(
				$settings['recurring_end_time'],
				$timezone
			) : new \DateTime('today 15:30:00', $timezone);

		$original_end_hour = (int) $end_time->format('H');
		$original_end_min = (int) $end_time->format('i');

		$start_time->setDate($now->format('Y'), $now->format('m'), $now->format('d'));
		$end_time->setDate($now->format('Y'), $now->format('m'), $now->format('d'));

		if ($now > $end_time) {
			$start_time->modify('+1 day');
			$end_time->modify('+1 day');
		}

		$widget_id = $widget->get_id();
		if (!isset($GLOBALS['recurring_countdowns'])) {
			$GLOBALS['recurring_countdowns'] = [];
		}
		$GLOBALS['recurring_countdowns'][$widget_id] = [
			'end_hour' => $original_end_hour,
			'end_min' => $original_end_min,
		];

		$due_date = $end_time->getTimestamp();
		$content = preg_replace('/data-date="(\d+)"/', 'data-date="' . $due_date . '" data-widget-id="' . $widget_id . '" data-end-hour="' . $original_end_hour . '" data-end-min="' . $original_end_min . '"', $content);
		$content = preg_replace('/data-type="recurring"/', 'data-type="recurring"', $content);
	}
	return $content;
}, 10, 2);


add_action('wp_footer', function () {

	if (empty($GLOBALS['recurring_countdowns'])) {
		return;
	}

	$timezone = new \DateTimeZone(wp_timezone_string());
	$tz_name = $timezone->getName();
	?>

	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const deliveryEndsEls = document.querySelectorAll('.delivery-ends');

			if (!deliveryEndsEls.length) return;

			const wpTimezone = "<?php echo esc_js($tz_name); ?>";
			function checkDeliveryVisibility() {

				const now = new Date();
				const nowInWP = new Date(now.toLocaleString("en-US", { timeZone: wpTimezone }));
				const hrs = nowInWP.getHours();
				const mins = nowInWP.getMinutes();
				const day = nowInWP.toLocaleString("en-US", { weekday: "long", timeZone: wpTimezone });

				if (day === "Saturday" || day === "Sunday") {
					deliveryEndsEls.forEach(el => el.style.setProperty('display', 'none', 'important'));
					return;
				} else {
					deliveryEndsEls.forEach(el => el.style.removeProperty('display'));
				}

				deliveryEndsEls.forEach(el => {
					const countdownWidget = el.querySelector('[data-end-hour]');
					if (!countdownWidget) {
						return;
					}
					const endHour = parseInt(countdownWidget.getAttribute('data-end-hour'));
					const endMin = parseInt(countdownWidget.getAttribute('data-end-min'));
					const afterCutoff = (hrs > endHour) || (hrs === endHour && mins >= endMin);
					console.log(afterCutoff);
					if (afterCutoff) {
						el.style.setProperty('display', 'none', 'important');
						console.log(afterCutoff);
					} else {
						el.style.removeProperty('display');
						console.log(afterCutoff);
					}
				});
			}
			checkDeliveryVisibility();
			setInterval(checkDeliveryVisibility, 10000);
		});
	</script>
<?php });

// Register Custom Post Type: Client Reviews
add_action('init', function () {

	$labels = [
		'name' => 'Client Reviews',
		'singular_name' => 'Client Review',
		'menu_name' => 'Client Reviews',
		'name_admin_bar' => 'Client Review',
		'add_new' => 'Add New',
		'add_new_item' => 'Add New Review',
		'new_item' => 'New Review',
		'edit_item' => 'Edit Review',
		'view_item' => 'View Review',
		'all_items' => 'All Reviews',
		'search_items' => 'Search Reviews',
		'not_found' => 'No reviews found.',
	];

	$args = [
		'labels' => $labels,
		'public' => false,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_admin_bar' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => false,
		'show_in_rest' => true,
		'menu_icon' => 'dashicons-testimonial',
		'supports' => ['title', 'editor'],
		'rewrite' => false,
		'has_archive' => false,
	];

	register_post_type('client_reviews', $args);
});



function story_category_tabs()
{
	ob_start();
	$categories = get_categories();
	?>
	<div class="story-tabs">
		<ul class="story-tab-buttons">
			<li class="story-tab-btn active" data-tab="tab-recent">Recent</li>
			<?php foreach ($categories as $cat): ?>
				<li class="story-tab-btn" data-tab="tab-<?php echo $cat->term_id; ?>">
					<?php echo esc_html($cat->name); ?>
				</li>
			<?php endforeach; ?>
		</ul>

		<div class="story-tab-contents">
			<div class="story-tab-content active" id="tab-recent">
				<?php
				$recent_posts = get_posts([
					'post_type' => 'post',
					'numberposts' => -1,
					'date_query' => [
						'relation' => 'OR',
						[
							'year' => date('Y'),
							'month' => date('n'),
						],
						[
							'year' => date('Y', strtotime('-1 month')),
							'month' => date('n', strtotime('-1 month')),
						],
					],
				]); foreach ($recent_posts as $post):
					setup_postdata($post);
					$cat = get_the_category($post->ID);
					$post_url = get_permalink($post->ID);
					$post_image = get_the_post_thumbnail_url($post->ID);
					?>
					<div class="story-card story-grid">
						<a class="story-card-image" href="<?php echo $post_url; ?>">
							<img src="<?php echo $post_image; ?>" alt="<?php echo get_the_title($post->ID); ?>">
						</a>
						<div class="story-card-content">
							<div class="story-card-category">
								<?php echo !empty($cat) ? esc_html($cat[0]->name) : ''; ?>
							</div>
							<a class="story-card-title" href="<?php echo $post_url; ?>">
								<?php echo get_the_title($post->ID); ?>
							</a>
							<div class="story-card-date">
								<?php echo get_the_date('F j, Y', $post->ID); ?>
							</div>
							<div class="story-card-arrow">
								<a href="<?php echo $post_url; ?>"><img
										src="https://hoyleton.org/wp-content/uploads/readmore_arrow_blog.svg"></a>
							</div>
						</div>
					</div>
				<?php endforeach;
				wp_reset_postdata(); ?>
			</div>

			<?php foreach ($categories as $cat): ?>
				<div class="story-tab-content" id="tab-<?php echo $cat->term_id; ?>">
					<?php
					$posts = get_posts([
						'category' => $cat->term_id,
						'posts_per_page' => -1
					]); foreach ($posts as $post):
						setup_postdata($post);
						$post_url = get_permalink($post->ID);
						$post_image = get_the_post_thumbnail_url($post->ID);
						?>

						<div class="story-card story-grid">
							<a class="story-card-image" href="<?php echo $post_url; ?>">
								<img src="<?php echo $post_image; ?>" alt="<?php echo get_the_title($post->ID); ?>">
							</a>
							<div class="story-card-content">
								<div class="story-card-category">
									<?php echo esc_html($cat->name); ?>
								</div>
								<a class="story-card-title" href="<?php echo $post_url; ?>">
									<?php echo get_the_title($post->ID); ?>
								</a>
								<div class="story-card-date">
									<?php echo get_the_date('F j, Y', $post->ID); ?>
								</div>
								<div class="story-card-arrow">
									<a href="<?php echo $post_url; ?>"><img
											src="https://hoyleton.org/wp-content/uploads/readmore_arrow_blog.svg"></a>
								</div>
							</div>
						</div>
					<?php endforeach;
					wp_reset_postdata(); ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode('story_category_tabs', 'story_category_tabs');

function register_projects_cpt()
{
	register_post_type('projects', [
		'labels' => [
			'name' => 'Projects',
			'singular_name' => 'Project',
		],
		'public' => true,
		'has_archive' => true,
		'menu_icon' => 'dashicons-portfolio',
		'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
		'rewrite' => ['slug' => 'projects'],
	]);

	// Category taxonomy for Projects
	register_taxonomy('project_category', 'projects', [
		'label' => 'Project Categories',
		'hierarchical' => true,
		'rewrite' => ['slug' => 'project-category'],
	]);
}
add_action('init', 'register_projects_cpt');

add_shortcode('slide_projects', 'slide_projects');
function slide_projects()
{
	?>

	<div class="swiper projects-slider">
		<div class="swiper-wrapper">
			<div class="swiper-slide">

				<?php
				$args = [
					'post_type' => 'projects',
					'posts_per_page' => -1,
				];

				$query = new WP_Query($args);

				while ($query->have_posts()):
					$query->the_post();
					$cats = get_the_terms(get_the_ID(), 'project_category');
					$cat_name = $cats ? $cats[0]->name : '';
					?>

					<div class="project-box">
						<div class="project-img">
							<?php the_post_thumbnail('large'); ?>
						</div>

						<div class="project-content">
							<p class="project-subtitle">
								<?php echo esc_html($cat_name); ?>
							</p>

							<h3 class="project-title">
								<a href="<?php the_permalink(); ?>">
									<?php the_title(); ?>
								</a>
							</h3>

							<p class="project-desc">
								<?php echo get_the_excerpt(); ?>
							</p>

							<a href="javascript:void(0)" class="project-icon">
								<i class="far fa-plus"></i>
							</a>
						</div>
					</div>



				<?php endwhile;
				wp_reset_postdata(); ?>

			</div>
		</div>
	</div>
	<?php
}

add_action('wp_footer', function () {
	?>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css" />

	<script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
	<style>
		.project-desc {
			display: none;
			margin-top: 10px;
		}

		.project-box.active .project-desc {
			display: block;
		}

		.project-icon {
			display: inline-flex;
			margin-top: 10px;
			cursor: pointer;
		}

		.project-box.active .project-icon i {
			transform: rotate(45deg);
		}
	</style>
	<script>
		jQuery(document).on('click', '.project-icon', function () {
			let box = jQuery(this).closest('.project-box');

			// Close others (accordion behavior)
			jQuery('.project-box').not(box).removeClass('active');

			box.toggleClass('active');
		});

		new Swiper('.projects-slider', {
			slidesPerView: 3,
			spaceBetween: 30,
			breakpoints: {
				768: { slidesPerView: 2 },
				480: { slidesPerView: 1 }
			}
		});


	</script>
	<?php
});