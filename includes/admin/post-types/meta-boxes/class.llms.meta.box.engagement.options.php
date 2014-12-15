<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Meta Box Engagement Options
* 
* Syllabus metabox contains misc options and syllabus
*/
class LLMS_Meta_Box_Engagement_Options {

	/**
	 * Set up video input
	 *
	 * @return string
	 * @param string $post
	 */
	
	public function __construct() {

	}

	/**
	 * Static output class.
	 *
	 * Displays MetaBox
	 * Calls static class metabox_options
	 * Loops through meta-options array and displays appropriate fields based on type.
	 * 
	 * @param  object $post [WP post object]
	 * 
	 * @return void
	 */
	public static function output( $post ) {
		global $post;
		wp_nonce_field( 'lifterlms_save_data', 'lifterlms_meta_nonce' );


		$engagement = get_post_meta( $post->ID, '_llms_engagement', true );
		$trigger_type = get_post_meta( $post->ID, '_llms_trigger_type', true );
		$engagement_type = get_post_meta( $post->ID, '_llms_engagement_type', true );
		$engagement_delay = get_post_meta( $post->ID, '_llms_engagement_delay', true );
		$engagement_trigger = get_post_meta( $post->ID, '_llms_engagement_trigger', true );

		?>

		<?php 

		$engagement_types = apply_filters('lifterlms_engagement_types', array(
			'email' => 'Send Email',
			'achievement' => 'Give Achievement',
			'certificate' => 'Give Certificate',
			)
		);

		?>

		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="_llms_engagement_type"><?php _e('Engagement Type', 'lifterlms' ); ?></label></th>
					<td>					
						<select id="_llms_engagement_type" name="_llms_engagement_type">
							<option value="" selected disabled>Please select an engagement type...</option>
								<?php foreach ( $engagement_types as $key => $value  ) : 
									if ( $key == $engagement_type ) {
								?>
									<option value="<?php echo $key; ?>" selected="selected"><?php echo $value; ?></option>

								<?php } else { ?>
									<option value="<?php echo $key; ?>"><?php echo $value; ?></option>

								<?php } ?>
								<?php endforeach; ?>
					 	</select>
						<br><span class="description"><?php _e( 'Select the type of engagement you want to create.', 'lifterlms' ); ?></span>	
					</td>
				</tr>

				<tr class="engagement-posts">
					<?php
					if($engagement_type) {
						$args = array(
								'post_type' 	=> 'llms_' . $engagement_type,
								'nopaging' 		=> true,
								'post_status'   => 'publish',
							 );

						$postlist = get_posts( $args );

						$engagement_types = $postlist;

						$engagement_types = apply_filters('lifterlms_engagement_event_options', $engagement_types, $engagement_type);

					?>
					<th><label for="engagement-select"><?php _e( 'Engagement Title', 'lifterlms' ); ?></label></th>
					<td>					
						<select id="engagement-select" class="chosen-select chosen select section-select" name="_llms_engagement">
							<option value="" selected disabled>Please select an engagement type...</option>
							<?php foreach ( $engagement_types as $key => $value  ) : 
						
								if ( $value->ID == $engagement ) {
							?>
								<option value="<?php echo $value->ID; ?>" selected="selected"><?php echo $value->post_title; ?></option>

							<?php } else { ?>
								<option value="<?php echo $value->ID; ?>"><?php echo $value->post_title; ?></option>

							<?php } ?>
							<?php endforeach; ?>
						</select>	
					</td>
					<?php } ?>
				</tr>

				<tr>
					<th>
						<?php
						$label = '';
						$label .= '<label for="_llms_engagement_delay">' . __( 'Engagement Delay (in days)', 'lifterlms' ) . '</label>';
						echo $label;
						?>
					</th>
					<td>
						<?php
						$html = '';
						$html .= '<input type="text" class="code" name="_llms_engagement_delay" id="_llms_engagement_delay" value="' . $engagement_delay . '"/>';
						$html .= '<br><span class="description">' .  __( 'If no value or 0 is entered the engagemnt will trigger immediately.', 'lifterlms' ) . '</span>';
						echo $html;
						?>
					</td>
				</tr>

				<?php
				$triggers = apply_filters( 'lifterlms_engagement_triggers', array(
					'lesson_completed' => 'lesson completed',
					'section_completed' => 'section completed',
					'course_completed' => 'course completed',
					'user_registration' => 'New User Registration',
					'days_since_login' => 'Days since user last logged in'
					) 
				);
				?>

				<tr>
					<th><label for="_llms_trigger_type"><?php _e( 'Event Trigger', 'lifterlms' ); ?></label></th>
					<td>	
						<select id="_llms_trigger_type" name="_llms_trigger_type">
							<option value="" selected disabled><?php _e( 'Please select a post...'); ?></option>
							<?php foreach ( $triggers as $key => $value  ) : 
								if ( $key == $trigger_type ) {
							?>
								<option value="<?php echo $key; ?>" selected="selected"><?php echo $value; ?></option>

							<?php } else { ?>
								<option value="<?php echo $key; ?>"><?php echo $value; ?></option>

							<?php } ?>
							<?php endforeach; ?>
					 	</select>
					 	<br><span class="description"><?php _e( 'Select the event to trigger the engagement on.' ); ?></span>
					</td>
				</tr>

				<tr class="engagement-option">

				<?php
				if($trigger_type) {
					$post_type = '';

					switch ($trigger_type)  {
						case 'lesson_completed' :
							$post_type = 'lesson';
							break;
						case 'section_completed' :
							$post_type = 'section';
							break;
						case 'course_completed' :
							$post_type = 'course';
							break;
						case 'course_purchased' :
							$post_type = 'course';
							break;
					}

					if ( ! empty($post_type) ) {


					$args = array(
							'post_type' 	=> $post_type,
							'nopaging' 		=> true,
							'post_status'   => 'publish',

						 );

						$postslist = get_posts( $args );


					$args2 = array(

						'post_type'   => $post_type,
						'post_status'   => 'publish',
						'meta_query'  => array(

						    array(

						      'value' => $post->ID,
						      'key' => '_llms_engagement_trigger'
						    )
						)
					);

					$my_query = new WP_Query( $args2 );

					if( $my_query->have_posts() ) {
						while( $my_query->have_posts() ) {
							$my_query->the_post();
								$engagement_trigger_id = $post->ID;
						}
					}
					wp_reset_postdata();
					?>

					<th><label for="trigger-select">Event</label></th>
					<td>
						<select id="trigger-select" class="chosen-select chosen select section-select" name="_llms_engagement_trigger">
							<option value="" selected disabled><?php _e( 'Please select an engagement type...', 'lifterlms' ); ?></option>
							<?php foreach ( $postslist as $key => $value  ) : 

								if ( $value->ID == $engagement_trigger_id) {
							?>
								<option value="<?php echo $value->ID; ?>" selected="selected"><?php echo $value->post_title; ?></option>

							<?php } else { ?>
								<option value="<?php echo $value->ID; ?>"><?php echo $value->post_title; ?></option>

							<?php } ?>
							<?php endforeach; ?>
				 		</select>
					</td>

					<?php } } ?>
				</tr>

			</tbody>
		</table>

	<?php 
	}

	/**
	 * Static save method
	 *
	 * cleans variables and saves using update_post_meta
	 * 
	 * @param  int 		$post_id [id of post object]
	 * @param  object 	$post [WP post object]
	 * 
	 * @return void
	 */
	public static function save( $post_id, $post ) {

		global $wpdb;

		//all fields must have a value to save them. 
		if (isset($_POST['_llms_engagement'])
			&& isset($_POST['_llms_trigger_type'])
			&& isset($_POST['_llms_engagement_type'])
			&& isset($_POST['_llms_engagement_delay'])
			&& isset($_POST['_llms_engagement_trigger'])
		) {

			//update engagement select
			$engagement = ( llms_clean( $_POST['_llms_engagement']  ) );
			update_post_meta( $post_id, '_llms_engagement', ( $engagement === '' ) ? '' : $engagement );

			//update trigger select
			$trigger_type = ( llms_clean( $_POST['_llms_trigger_type']  ) );
			update_post_meta( $post_id, '_llms_trigger_type', ( $trigger_type === '' ) ? '' : $trigger_type );

			//update type select
			$engagement_type = ( llms_clean( $_POST['_llms_engagement_type']  ) );
			update_post_meta( $post_id, '_llms_engagement_type', ( $engagement_type === '' ) ? '' : $engagement_type );

			//update delay textbox
			$engagement_delay = ( llms_clean( $_POST['_llms_engagement_delay']  ) );
			update_post_meta( $post_id, '_llms_engagement_delay', ( $engagement_delay === '' ) ? '0' : $engagement_delay );

			//update trigger select
			$engagement_trigger = ( llms_clean( $_POST['_llms_engagement_trigger']  ) );
			update_post_meta( $engagement_trigger, '_llms_engagement_trigger', ( $post->ID === '' ) ? '' : $post->ID );
		}

	}

}