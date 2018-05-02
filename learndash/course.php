<?php
/**
 * Displays a course
 *
 * Available Variables:
 * $course_id       : (int) ID of the course
 * $course      : (object) Post object of the course
 * $course_settings : (array) Settings specific to current course
 *
 * $courses_options : Options/Settings as configured on Course Options page
 * $lessons_options : Options/Settings as configured on Lessons Options page
 * $quizzes_options : Options/Settings as configured on Quiz Options page
 *
 * $user_id         : Current User ID
 * $logged_in       : User is logged in
 * $current_user    : (object) Currently logged in user object
 *
 * $course_status   : Course Status
 * $has_access  : User has access to course or is enrolled.
 * $materials       : Course Materials
 * $has_course_content      : Course has course content
 * $lessons         : Lessons Array
 * $quizzes         : Quizzes Array
 * $lesson_progression_enabled  : (true/false)
 * $has_topics      : (true/false)
 * $lesson_topics   : (array) lessons topics
 *
 * @since 2.1.0
 *
 * @package LearnDash\Course
 */

?>

<?php
/**
 * Display course status
 */
?>
<?php if ( $logged_in ) : ?>
	<span id="learndash_course_status col-sm-12">
		<b>
		<?php
			// translators: Course Status Label.
			printf( esc_html_x( '%s Status:', 'Course Status Label', 'learndash' ), esc_attr( LearnDash_Custom_Label::get_label( 'course' ) ) );
			?>
			</b> 
			<?php
			echo esc_attr( $course_status );
		?>
		<br />
	</span>
	<br />

	<?php
		/**
		 * Filter to add custom content after the Course Status section of the Course template output.
		 *
		 * @since 2.3
		 * See https://bitbucket.org/snippets/learndash/7oe9K for example use of this filter.
		 */
		echo apply_filters( 'ld_after_course_status_template_container', '', learndash_course_status_idx( $course_status ), $course_id, $user_id );
	?>

	<?php if ( ! empty( $course_certficate_link ) ) : ?>
		<div id="learndash_course_certificate" class="learndash_course_certificate">
			<a href='<?php echo esc_attr( $course_certficate_link ); ?>' class="btn-blue" target="_blank"><?php echo apply_filters( 'ld_certificate_link_label', esc_html__( 'PRINT YOUR CERTIFICATE', 'learndash' ), $user_id, $post->ID ); ?></a>
		</div>
		<br />
	<?php endif; ?>
<?php endif; ?>

<div class="learndash_content"><?php echo $content; ?></div>

<?php if ( ! $has_access ) : 
	if(!is_user_logged_in()){
		?>
	<div class="col-sm-12 reg-login-msg">
		<p class="light-font">If you are not a registered user, please take the time to <a class="bold-font" href="">Register</a> now. Registering will allow you to take this course or any of our other <span class="bold-font">Free</span> courses. Already a registered user? <a class="bold-font" rel="nofollow" href="#login-modal" data-toggle="modal">Log In</a> to take this course. </p>
	</div>
<?php } else {
	echo learndash_payment_buttons( $post );
} ?>
<?php endif; ?>

<?php if ( ( isset( $materials ) ) && ( ! empty( $materials ) ) ) : ?>
	<div id="learndash_course_materials" class="learndash_course_materials">
		<h4>
		<?php
			// translators: Course Materials Label.
			printf( esc_html_x( '%s Materials', 'Course Materials Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) );
		?>
		</h4>
		<p><?php echo $materials; ?></p>
	</div>
<?php endif; ?>

<?php if ( $has_course_content ) : ?>
	<?php
		$show_course_content = true;
	if ( ! $has_access ) :
		if ( 'on' === $course_meta['sfwd-courses_course_disable_content_table'] ) :
			$show_course_content = false;
			endif;
		endif;

	if ( $show_course_content ) :
		?>
	<div id="learndash_course_content" class="learndash_course_content">
		<?php
		/**
		 * Display lesson list
		 */
		?>
		<?php if ( ! empty( $lessons ) ) : ?>

			<?php if ( $has_topics ) : ?>
				<div class="expand_collapse">
					<a href="#" onClick='jQuery("#learndash_post_<?php echo $course_id; ?> .learndash_topic_dots").slideDown(); return false;'><?php esc_html_e( 'Expand All', 'learndash' ); ?></a> | <a href="#" onClick='jQuery("#learndash_post_<?php echo esc_attr( $course_id ); ?> .learndash_topic_dots").slideUp(); return false;'><?php esc_html_e( 'Collapse All', 'learndash' ); ?></a>
				</div>
				<?php if ( apply_filters( 'learndash_course_steps_expand_all', false, $course_id, 'course_lessons_listing_main' ) ) { ?>
					<script>
						jQuery(document).ready(function(){
							jQuery("#learndash_post_<?php echo $course_id; ?> .learndash_topic_dots").slideDown();
						});
					</script>	
				<?php } ?>
			<?php endif; ?>

			<div id="learndash_lessons" class="learndash_lessons">
				<div id="lesson_heading">
						<div class="col-sm-6"><?php echo LearnDash_Custom_Label::get_label( 'lessons' ); ?></div>
					<div class="colo-sm-6 text-align-right status"><?php esc_html_e( 'Status', 'learndash' ); ?></div>

				<div id="lessons_list" class="lessons_list">
				
					<?php foreach ( $lessons as $lesson ) : ?>
						<div class="col-sm-12 post-<?php echo esc_attr( $lesson['post']->ID ); ?> <?php echo esc_attr( $lesson['sample'] ); ?>">

							<div class="col-sm-1">
								<?php echo $lesson['sno']; ?>
							</div>

							<span class="col-sm-11 course-lesson-link">
								<a class='col-sm-11 <?php echo esc_attr( $lesson['status'] ); ?>' href='<?php echo esc_attr( learndash_get_step_permalink( $lesson['post']->ID, $course_id ) ); ?>'><?php echo $lesson['post']->post_title; ?></a>
								<span class="<?php echo esc_attr( $lesson['status'] ); ?> col-sm-1 glyphicon" aria-hidden="true"></span>		
								<?php
								/**
								 * Not available message for drip feeding lessons
								 */
								?>
								<?php if ( ! empty( $lesson['lesson_access_from'] ) ) : ?>
									<?php
										SFWD_LMS::get_template(
											'learndash_course_lesson_not_available',
											array(
												'user_id' => $user_id,
												'course_id' => learndash_get_course_id( $lesson['post']->ID ),
												'lesson_id' => $lesson['post']->ID,
												'lesson_access_from_int' => $lesson['lesson_access_from'],
												'lesson_access_from_date' => learndash_adjust_date_time_display( $lesson['lesson_access_from'] ),
												'context' => 'course',
											), true
										);
									?>
								<?php endif; ?>


								<?php
								/**
								 * Lesson Topics
								 */
								?>
								<?php $topics = @$lesson_topics[ $lesson['post']->ID ]; ?>

								<?php if ( ! empty( $topics ) ) : ?>
									<div id='learndash_topic_dots-<?php echo esc_attr( $lesson['post']->ID ); ?>' class="learndash_topic_dots type-list">
										<ul>
											<?php $odd_class = ''; ?>
											<?php foreach ( $topics as $key => $topic ) : ?>
												<?php $odd_class       = empty( $odd_class ) ? 'nth-of-type-odd' : ''; ?>
												<?php $completed_class = empty( $topic->completed ) ? 'topic-notcompleted' : 'topic-completed'; ?>												
												<li class='<?php echo esc_attr( $odd_class ); ?>'>
													<span class="topic_item col-sm-12">
														<a class='topic-lesson-link <?php echo esc_attr( $completed_class ); ?>' href='<?php echo esc_attr( learndash_get_step_permalink( $topic->ID, $course_id ) ); ?>' title='<?php echo esc_attr( $topic->post_title ); ?>'>
															<span class="col-sm-11"><?php echo $topic->post_title; ?></span>
														</a>
													</span>
													<span class="<?php echo esc_attr( $completed_class ); ?> col-sm-1 glyphicon glyphicon-unchecked" aria-hidden="true"></span>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endif; ?>

							</span>
						
			</div>
					<?php endforeach; ?>

				</div>
			</div>
			<?php
				global $course_lessons_results;
				if ( isset( $course_lessons_results['pager'] ) ) {
					echo SFWD_LMS::get_template( 
						'learndash_pager.php', 
						array(
						'pager_results' => $course_lessons_results['pager'], 
						'pager_context' => 'course_lessons'
						) 
					);
				}
			?>
		<?php endif; ?>
		
		<?php
			if ( ( isset( $course_lessons_results['pager'] ) ) && ( !empty( $course_lessons_results['pager'] ) ) ) {
				if ( $course_lessons_results['pager']['paged'] == $course_lessons_results['pager']['total_pages'] ) {
					$show_course_quizzes = true;
				} else {
					$show_course_quizzes = false;
				}
			} else {
				$show_course_quizzes = true;
			}
		?>
		<?php
		/**
		 * Display quiz list
		 */
		?>
		<?php 
			if ( $show_course_quizzes == true ) {
				if ( ! empty( $quizzes ) ) { ?>
					<div id="learndash_quizzes" class="learndash_quizzes">
						<div id="quiz_heading" class="col-sm-12">
								<div class="col-sm-6"><?php echo LearnDash_Custom_Label::get_label( 'quizzes' ); ?></div>
								<div class="col-sm-6 text-align-right status"><?php esc_html_e( 'Status', 'learndash' ); ?></span>
						</div>
						<div id="quiz_list" class="quiz_list">

							<?php foreach ( $quizzes as $quiz ) : ?>
								<div id='post-<?php echo esc_attr( $quiz['post']->ID ); ?>' class='col-sm-12 <?php echo esc_attr( $quiz['sample'] ); ?>'>
									<div class="list-count col-sm-1"><?php echo $quiz['sno']; ?></div>
									<span class="col-sm-11 course-quiz-link">
										<a class="col-sm-11 <?php echo esc_attr( $quiz['status'] ); ?>" href='<?php echo esc_attr( learndash_get_step_permalink( $quiz['post']->ID, $course_id ) ); ?>'><?php echo $quiz['post']->post_title; ?></a>
										<span class="<?php echo esc_attr( $quiz['status'] ); ?> col-sm-1 glyphicon" aria-hidden="true"></span>
									</span>
								</div>						
							<?php endforeach; ?>

						</div>
					</div>
				<?php }
			} 
		?>
	</div>
		<?php endif; ?>
<?php endif; ?>
