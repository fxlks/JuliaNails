<?php
// $atts are defined in class-shortcodes.php
// don't try to load this file directly, instead call ssa()->shortcodes->ssa_upcoming_appointments()

$settings              = ssa()->settings->get();
$upcoming_appointments = ssa()->appointment_model->query( $atts );
$all_appointment_types    = ssa()->appointment_type_model->get_all_appointment_types();
$mapped_appointment_types = array();
foreach( $all_appointment_types as $appointment_type ){
	if(empty($mapped_appointment_types[$appointment_type['id']])){
		$mapped_appointment_types[$appointment_type['id']] = $appointment_type;
	}
}


$date_format           = SSA_Utils::localize_default_date_strings( 'F j, Y g:i a' ) . ' (T)';
?>

<div class="ssa-upcoming-appointments">
	<ul class="ssa-upcoming-appointments">
		<?php if ( ! is_user_logged_in() ) : ?>
			<?php echo wp_kses_post( $atts['logged_out_message'] ); ?>
		<?php elseif ( empty( $upcoming_appointments ) ) : ?>
			<?php echo wp_kses_post( $atts['no_results_message'] ); ?>
		<?php else : ?>
			<?php
			foreach ( $upcoming_appointments as $upcoming_appointment ) :
				$members = ssa()->staff_appointment_model->get_staff_details_for_appointment_id( $upcoming_appointment['id'] );
				$upcoming_appointment_type = $mapped_appointment_types[$upcoming_appointment['appointment_type_id']];

				$members_names = array();
				if ( !empty( $members ) ){
					foreach (  $members as $member){
						$members_names[] = $member['name'];
					}
				}
				$label= $upcoming_appointment_type['label_color'];
				?>
				<li class="ssa-upcoming-appointment">
					<i aria-hidden="true" class="md-icon md-primary md-size-2x md-theme-<?php echo $label;?> material-icons"> person </i>
					<div class="md-list-text-container">
						<h4>
							<?php
							// output name of the client who booked the meeting.
							$user_name = $upcoming_appointment['customer_information']['Name'];
							echo ( ' ' . ucfirst( $user_name ) . ' ' );
							?>
						</h4>
						<!-- Appointment type -->
						<p>
							<?php
							if ( filter_var( $atts['appointment_type_displayed'], FILTER_VALIDATE_BOOLEAN ) ) {
								echo ' ' . $upcoming_appointment_type['title'];
							}
							?>
						<!-- Appointment team members -->
							<?php
							if ( filter_var( $atts['team_members_displayed'], FILTER_VALIDATE_BOOLEAN ) && ! empty( $members_names ) ) {
								echo ' with ' . join( ', ', $members_names );
							}
							?>
						</p>
						<!-- Appointment Time -->
						<p>
							<?php
							$upcoming_appointment_datetime = ssa_datetime( $upcoming_appointment['start_date'] );
							if ( ! empty( $upcoming_appointment['customer_timezone'] ) ) {
								$customer_timezone_string = $upcoming_appointment['customer_timezone'];
							} else {
								$customer_timezone_string = 'UTC';
							}

							// display the localized time according to business timezone.
							$date_timezone    = SSA_Utils::safe_timezone( $settings['global']['timezone_string'] );
							$localized_string = $upcoming_appointment_datetime->setTimezone( $date_timezone )->format( $date_format );
							$localized_string = SSA_Utils::translate_formatted_date( $localized_string );

							echo $localized_string;
							?>

						</p>
						<!-- Appointment Links -->
						<p class="ssa-upcoming-appointment-links">
							<?php
							// link meeting details to the admin ssa panel.
							if ( ! empty( $upcoming_appointment['web_meeting_url'] ) && filter_var( $atts['web_meeting_url'], FILTER_VALIDATE_BOOLEAN ) ) {
								echo ' <a target="_blank" href="' . $upcoming_appointment['web_meeting_url'] . '"> ' . wp_kses_post( $atts['web_meeting_link_label'] ) . ' </a>';
							}

							if ( ! empty( $atts['details_link_displayed'] ) ) {
								echo '<a target="_blank" href=' . ssa()->wp_admin->url( '/ssa/appointment/' ) . $upcoming_appointment['id'] . '>' . wp_kses_post( $atts['details_link_label'] ) . '</a>';
							}
							?>

						</p>
					</div>
				</li>
				<?php endforeach; ?>
				<?php 
					if ( ! empty( $upcoming_appointments ) ) {
							echo '<a href=' . ssa()->wp_admin->url( '/ssa/appointments' ) . '><li class="ssa-upcoming-appointment"><div class="md-list-text-container">'.wp_kses_post( $atts['all_appointments_link_label'] ). '</div></li></a>'; 
					}
				?>
		<?php endif; ?>
	</ul>
</div>
