<?php
/**
 * Popular Posts with Thumbnail widget.
 *
 * @package    VT Blogging
 * @author     VolThemes
 * @copyright  Copyright (c) 2018, VolThemes
 * @license    http://www.gnu.org/licenses/gpl-2.0.html
 * @since      1.0.0
 */
class vt_blogging_Popular_Widget extends WP_Widget {

	/**
	 * Sets up the widgets.
	 *
	 * @since 1.0.0
	 */
	function __construct() {

		/* Widget settings. */
		$widget_ops = array(
			'classname' => 'widget-vt-blogging-popular widget_posts_thumbnail',
			'description' => esc_html__('A widget that displays popular posts by comments with thumbnails.', 'vt-blogging')
		);

		// Create the widget.
		parent::__construct(
			'vt-blogging-popular',                     // $this->id_base
			__( '[VT] Popular Posts', 'vt-blogging' ), // $this->name
			$widget_ops                                // $this->widget_options
		);

		// Flush the transient.
		add_action( 'save_post'   , array( $this, 'flush_widget_transient' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_transient' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_transient' ) );

	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 *
	 * @since 1.0.0
	 */
	function widget( $args, $instance ) {
		extract( $args );

		// Output the theme's $before_widget wrapper.
		echo wp_kses_post($before_widget);

		// If the title not empty, display it.
		if ( $instance['title'] ) {
			echo wp_kses_post($before_title) . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . wp_kses_post($after_title);
		}

		// Display the popular posts.
		if ( false === ( $popular = get_transient( 'vt_blogging_popular_widget_' . $this->id ) ) ) {

			// Posts query arguments.
			$args = array(
				'post_type'      => 'post',
				'posts_per_page' => $instance['limit'],
				'orderby'        => 'comment_count'
			);

			// The post query
			$popular = new WP_Query( $args );

			// Store the transient.
			set_transient( 'vt_blogging_popular_widget_' . $this->id, $popular );

		}

		global $post;
		if ( $popular->have_posts() ) {
			echo '<ul>';

				while ( $popular->have_posts() ) : $popular->the_post();

					echo '<li class="clear">';

						echo '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . '<div class="thumbnail-wrap">';
							if ( has_post_thumbnail() ) {
							the_post_thumbnail('vt_blogging_widget_thumb');
							}
							else {
								echo '<img src="' . esc_url( get_template_directory_uri() ) . '/assets/img/no-thumbnail.png" alt="' . esc_attr__('No Picture', 'vt-blogging') . '" />';
							}
						echo '</div>' . '</a>';
						
						echo '<div class="entry-wrap"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . esc_html( get_the_title() ) . '</a>'; 

						if ( $instance['show_views'] ) :
							echo '<div class="entry-meta">' . get_the_date() . '</div>';
							
						endif;
						
					echo '</div></li>';

				endwhile;

			echo '</ul>';
		}

		// Reset the query.
		wp_reset_postdata();

		// Close the theme's widget wrapper.
		echo wp_kses_post($after_widget);

	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 *
	 * @since 1.0.0
	 */
	function update( $new_instance, $old_instance ) {

		$instance = $new_instance;
		$instance['title']     = wp_strip_all_tags ( $new_instance['title'] );
		$instance['limit']     = (int) $new_instance['limit'];
		$instance['show_views'] = isset( $new_instance['show_views'] ) ? (bool) $new_instance['show_views'] : false;

		// Delete our transient.
		$this->flush_widget_transient();

		return $instance;
	}

	/**
	 * Flush the transient.
	 *
	 * @since  1.0.0
	 */
	function flush_widget_transient() {
		delete_transient( 'vt_blogging_popular_widget_' . $this->id );
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 *
	 * @since 1.0.0
	 */
	function form( $instance ) {

		// Default value.
		$defaults = array(
			'title'     => esc_html__( 'Popular Posts', 'vt-blogging' ),
			'limit'     => 5,
			'show_views' => true
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
	?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>">
				<?php esc_html_e( 'Title', 'vt-blogging' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'limit' )); ?>">
				<?php esc_html_e( 'Number of posts to show', 'vt-blogging' ); ?>
			</label>
			<input class="small-text" id="<?php echo esc_attr($this->get_field_id( 'limit' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'limit' )); ?>" type="number" step="1" min="0" value="<?php echo (int)( $instance['limit'] ); ?>" />
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_views'] ); ?> id="<?php echo esc_attr($this->get_field_id( 'show_views' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'show_views' )); ?>" />
			<label for="<?php echo esc_attr($this->get_field_id( 'show_views' )); ?>">
				<?php esc_html_e( 'Display post date?', 'vt-blogging' ); ?>
			</label>
		</p>

	<?php

	}

} // class vt_blogging_Popular_Widget
?>