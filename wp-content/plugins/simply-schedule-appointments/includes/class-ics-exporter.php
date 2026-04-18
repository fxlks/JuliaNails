<?php
/**
 * Simply Schedule Appointments Ics Exporter.
 *
 * @since   0.0.3
 * @package Simply_Schedule_Appointments
 */

/**
 * Simply Schedule Appointments Ics Exporter.
 *
 * @since 0.0.3
 */
class SSA_Ics_Exporter {
	/**
	 * ICS template.
	 *
	 * @var string
	 */
	public $template = 'customer';

	/**
	 * Appointments list to export
	 *
	 * @var array
	 */
	protected $appointments = array();

	/**
	 * File path
	 *
	 * @var string
	 */
	protected $file_path = '';

	/**
	 * UID prefix.
	 *
	 * @var string
	 */
	protected $uid_prefix = 'ssa_';

	/**
	 * End of line.
	 *
	 * @var string
	 */
	protected $eol = "\r\n";

	/**
	 * Get appointment .ics
	 *
	 * @param SSA_Appointment_Object $appointment Booking data.
	 * @param string                 $template the ics template to use.
	 *
	 * @return array an array containing the appointment data and headers.
	 */
	public function get_ics_for_appointment( $appointment, $template = 'customer' ) {
		$this->appointments = array( $appointment );

		return $this->get_ics( $this->appointments, $template );
	}

	/**
	 * Get .ics feed for appointments.
	 *
	 * @param array  $appointments an array of appointments objects.
	 * @param string $template the ics template to use.
	 *
	 * @return array|null an array containing the appointment data and headers if there are appointments. Null if no appointments.
	 */
	public function get_ics_feed( $appointments, $template = 'customer' ) {
		if ( empty( $appointments ) ) {
			return null;
		}
		if ( ! is_array( $appointments ) ) {
			$appointments = array( $appointments );
		}

		$this->appointments = $appointments;

		$sitename = get_bloginfo( 'name' );

		return $this->get_ics( $this->appointments, $template, $sitename );
	}

	/**
	 * Get .ics for appointments.
	 *
	 * @param  array  $appointments Array with SSA_Appointment_Object objects.
	 * @param  string $template the ics template to use.
	 * @param  string $filename .ics filename.
	 *
	 * @return string .ics path
	 */
	public function get_ics( $appointments, $template = 'customer', $filename = '' ) {
		$this->appointments = $appointments;
		$this->template     = $template;

		if ( '' === $filename ) {
			$filename = 'appointment-' . time() . '-' . wp_hash( wp_json_encode( $this->appointments['0']->id ) . $this->template );
		}

		// Create the .ics.
		$ics = $this->generate();

		return array(
			'data'    => $ics,
			'headers' => array(
				'Content-Disposition' => 'attachment; filename="' . $filename . '.ics"',
				'Content-Type'        => 'text/calendar; charset=utf-8',
			),
		);
	}

	/**
	 * Format the date
	 *
	 * @param int                    $timestamp Timestamp to format.
	 * @param SSA_Appointment_Object $appointment   Booking object.
	 *
	 * @return string Formatted date for ICS.
	 */
	protected function format_date( $timestamp, $appointment = null ) {
		$pattern = 'Ymd\THis';

		if ( $appointment ) {
			$pattern = ( $appointment->is_all_day() ) ? 'Ymd' : $pattern;
		}

		$formatted_date  = gmdate( $pattern, $timestamp );
		$formatted_date .= 'Z'; // Zulu (UTC).

		return $formatted_date;
	}

	/**
	 * Sanitize strings for .ics
	 *
	 * @param  string $string String to sanitize.
	 *
	 * @return string
	 */
	protected function sanitize_string( $string ) {
		$clear_map = array(
			'</p>'   => "</p>\r\n",
			'<br />' => "\r\n",
			'<br>'   => "\r\n",
			'<br/>'  => "\r\n",
		);

		$string = str_replace( array_keys( $clear_map ), array_values( $clear_map ), $string );
		$string = wp_strip_all_tags( $string );
		$string = preg_replace( '/([\,;])/', '\\\$1', $string );
		$string = str_replace( "\n\n\n", "\n\n", $string );
		$string = str_replace( "\n", '\n', $string );
		$string = sanitize_text_field( $string );
		$string = html_entity_decode( $string, ENT_QUOTES | ENT_XML1, 'UTF-8' );

		return $string;
	}

	/**
	 * Fold a content line per RFC 5545 (max 75 octets, fold with CRLF + space)
	 *
	 * @param string $line The full line including property name (e.g., "DESCRIPTION:text...")
	 * @return string The folded line(s)
	 */
	protected function fold_line( $line ) {
		$max_length = 75;
		
		// If line is already short enough, return as-is
		if ( strlen( $line ) <= $max_length ) {
			return $line;
		}
		
		$result = '';
		$remaining = $line;
		$first_line = true;
		
		while ( strlen( $remaining ) > 0 ) {
			if ( $first_line ) {
				// First line: take up to 75 chars
				$chunk_length = $max_length;
				$first_line = false;
			} else {
				// Continuation lines: account for the leading space (74 chars of content + 1 space = 75)
				$chunk_length = $max_length - 1;
			}
			
			// Get chunk respecting UTF-8 boundaries
			$chunk = mb_strcut( $remaining, 0, $chunk_length, 'UTF-8' );
			$remaining = substr( $remaining, strlen( $chunk ) );
			
			if ( $result !== '' ) {
				// Add CRLF + space before continuation
				$result .= "\r\n ";
			}
			$result .= $chunk;
		}
		
		return $result;
	}

	/**
	 * Generate the .ics content
	 *
	 * @return string
	 */
	protected function generate() {
		$settings           = ssa()->settings->get();
		$sitename           = $settings['global']['company_name'];

		// Set the ics data.
		$ics = 'BEGIN:VCALENDAR' . $this->eol;
		$ics .= 'VERSION:2.0' . $this->eol;
		$ics .= 'PRODID:-//SSA//Simply Schedule Appointments ' . Simply_Schedule_Appointments::VERSION . '//EN' . $this->eol;
		$ics .= 'CALSCALE:GREGORIAN' . $this->eol;
		$ics .= 'X-ORIGINAL-URL:' . $this->sanitize_string( home_url( '/' ) ) . $this->eol;
		$ics .= 'X-WR-CALDESC:' . $this->sanitize_string( sprintf( __( 'Appointments from %s', 'simply-schedule-appointments' ), $sitename ) ) . $this->eol;
		$ics .= 'TRANSP:' . 'OPAQUE' . $this->eol;

		// Create recipient and URL once — avoids N×3 object creations inside the
		// per-appointment get_calendar_event_title/description/location calls.
		if ( 'staff' === $this->template ) {
			$recipient = SSA_Recipient_Staff::create();
			$url       = ssa()->wp_admin->url( 'ssa/appointments' );
		} else {
			$recipient = SSA_Recipient_Customer::create();
			$url       = '';
		}

		// Enable batch-mode caching on the template engine for the duration of this loop.
		// Each appointment needs title, description, and location — all three trigger the
		// same heavy filter chain (DB queries + meta lookups) on get_template_vars().
		// Batch mode caches the result after the first call per appointment so the 2nd
		// and 3rd calls are instant. Disabled immediately after the loop; zero side effects
		// on any other part of the application.
		ssa()->templates->enable_batch_mode();

		foreach ( $this->appointments as $appointment ) {
			if ( in_array( $this->template, array( 'customer', 'staff' ) ) ) {
				$summary     = $appointment->get_calendar_event_title( $recipient );
				$description = $appointment->get_calendar_event_description( $recipient );
				$location    = $appointment->get_calendar_event_location( $recipient );
				$date_prefix = ( $appointment->is_all_day() ) ? ';VALUE=DATE:' : ':';
			}

			$ics     .= 'BEGIN:VEVENT' . $this->eol;
			$ics     .= 'UID:' . $this->uid_prefix . $appointment->id . $this->template . $this->eol;
			$ics     .= 'DTSTAMP:' . $this->format_date( time() ) . $this->eol;
			if ( ! empty( $location ) ) {
				$ics .= $this->fold_line( 'LOCATION:' . $this->sanitize_string( $location ) ) . $this->eol;
			} else {
				$ics .= 'LOCATION:' . $this->eol;
			}
			$ics .= $this->fold_line( 'DESCRIPTION:' . $this->sanitize_string( $description ) ) . $this->eol;
			$ics .= $this->fold_line( 'URL;VALUE=URI:' . $this->sanitize_string( $url ) ) . $this->eol;
			$ics .= $this->fold_line( 'SUMMARY:' . $this->sanitize_string( $summary ) ) . $this->eol;
			$ics .= 'DTSTART' . $date_prefix . $this->format_date( $appointment->start_date_timestamp, $appointment ) . $this->eol;
			$ics .= 'DTEND' . $date_prefix . $this->format_date( $appointment->end_date_timestamp, $appointment ) . $this->eol;
			$ics .= 'END:VEVENT' . $this->eol;
		}

		ssa()->templates->disable_batch_mode();

		$ics .= 'END:VCALENDAR';

		return $ics;
	}

}
