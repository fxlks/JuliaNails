<?php

namespace AmeliaBooking\Infrastructure\WP\Translations;

use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Infrastructure\WP\SettingsService\SettingsStorage;

/**
 * @phpcs:disable
 */
class LiteFrontendStrings
{
    private static ?SettingsService $settings = null;

    /**
     * @return array|mixed
     */
    public static function getLabelsFromSettings()
    {
        if (!self::$settings) {
            self::$settings = new SettingsService(new SettingsStorage());
        }

        if (self::$settings->getSetting('labels', 'enabled') === true) {
            $labels = self::$settings->getCategorySettings('labels');
            unset($labels['enabled']);

            return $labels;
        }

        return [];
    }

    /**
     * Return all strings for frontend
     */
    public static function getAllStrings(): array
    {
        return array_merge(
            self::getCommonStrings(),
            self::getBookingStrings(),
            self::getBookableStrings(),
            self::getCatalogStrings(),
            self::getSearchStrings(),
            self::getLabelsFromSettings(),
            self::getEventStrings(),
            self::getCabinetStrings()
        );
    }

    /**
     * Returns the array for the bookable strings
     */
    public static function getBookableStrings(): array
    {
        return [];
    }

    /**
     * Returns the array of the common frontend strings
     */
    public static function getCommonStrings(): array
    {
        return [
            'add_to_calendar'              => __('Add to Calendar', 'ameliabooking'),
            'amount'                       => __('Amount', 'ameliabooking'),
            'all_services'                 => __('All Services', 'ameliabooking'),
            'all_locations'                => __('All Locations', 'ameliabooking'),
            'no_services_employees'        => __('It seems like there are no employees or services created, or no  employees are assigned to the service, at this moment.'),
            'add_services_employees'       => __('If you are the admin of this page, see how to'),
            'add_services_url'             => __('Add services'),
            'add_employees_url'            => __('employees.'),
            'back'                         => __('Back', 'ameliabooking'),
            'base_price_colon'             => __('Base Price:', 'ameliabooking'),
            'booking_completed_approved'   => __('Thank you! Your booking is completed.', 'ameliabooking'),
            'bookings_limit_reached'       => __('Maximum bookings reached', 'ameliabooking'),
            'cancel'                       => __('Cancel', 'ameliabooking'),
            'canceled'                     => __('Canceled', 'ameliabooking'),
            'capacity_colon'               => __('Capacity:', 'ameliabooking'),
            'closed'                       => __('Closed', 'ameliabooking'),
            'content_mode_tooltip'         => __('Don\'t use Text mode option if you already have HTML code in the description, since once this option is enabled the existing HTML tags could be lost.', 'ameliabooking'),
            'enable_google_meet'           => __('Enable Google Meet', 'ameliabooking'),
            'enable_microsoft_teams'       => __('Enable Microsoft Teams', 'ameliabooking'),
            'full'                         => __('Full', 'ameliabooking'),
            'upcoming'                     => __('Upcoming', 'ameliabooking'),
            'confirm'                      => __('Confirm', 'ameliabooking'),
            'congratulations'              => __('Congratulations', 'ameliabooking'),
            'customer_already_booked_app'  => __('You have already booked this appointment', 'ameliabooking'),
            'customer_already_booked_ev'   => __('You have already booked this event', 'ameliabooking'),
            'date_colon'                   => __('Date:', 'ameliabooking'),
            'duration_colon'               => __('Duration:', 'ameliabooking'),
            'email_colon'                  => __('Email:', 'ameliabooking'),
            'email_exist_error'            => __('Email already exists with different name. Please check your name.', 'ameliabooking'),
            'email_required'               => __('Email field is required', 'ameliabooking'),
            'employee_limit_reached'       => __('Employee daily appointment limit has been reached. Please choose another date or employee.', 'ameliabooking'),
            'enter_email_warning'          => __('Please enter email', 'ameliabooking'),
            'enter_first_name_warning'     => __('Please enter first name', 'ameliabooking'),
            'enter_last_name_warning'      => __('Please enter last name', 'ameliabooking'),
            'enter_phone_warning'          => __('Please enter phone number', 'ameliabooking'),
            'enter_valid_email_warning'    => __('Please enter a valid email address', 'ameliabooking'),
            'enter_valid_phone_warning'    => __('Please enter a valid phone number.', 'ameliabooking'), // Used in Redesign | Updated
            'event_info'                   => __('Event Info', 'ameliabooking'),
            'finish_appointment'           => __('Finish', 'ameliabooking'),
            'first_name_colon'             => __('First Name:', 'ameliabooking'),
            'h'                            => __('h', 'ameliabooking'),
            'last_name_colon'              => __('Last Name:', 'ameliabooking'),
            'licence_start_description'    => __('Available from Starter license', 'ameliabooking'),
            'licence_basic_description'    => __('Available from Standard license', 'ameliabooking'),
            'licence_pro_description'      => __('Available from Pro license', 'ameliabooking'),
            'licence_dev_description'      => __('Available in Elite licence', 'ameliabooking'),
            'licence_button_text'          => __('Upgrade', 'ameliabooking'),
            'min'                          => __('min', 'ameliabooking'),
            'no_results_found'             => __('No results found...', 'ameliabooking'),
            'on_site'                      => __('On-site', 'ameliabooking'),
            'payment_btn_on_site'          => __('On-Site', 'ameliabooking'),
            'oops'                         => __('Oops...'),
            'payment_btn_square'           => __('Square', 'ameliabooking'),
            'open'                         => __('Open', 'ameliabooking'),
            'phone_colon'                  => __('Phone:', 'ameliabooking'),
            'phone_exist_error'            => __('Phone already exists with different name. Please check your name.', 'ameliabooking'),
            'price_colon'                  => __('Price:', 'ameliabooking'),
            'service'                      => __('service', 'ameliabooking'),
            'select_calendar'              => __('Select Calendar', 'ameliabooking'),
            'services_lower'               => __('services', 'ameliabooking'),
            'square'                       => __('Square', 'ameliabooking'),
            'time_colon'                   => __('Local Time:', 'ameliabooking'),
            'time_slot_unavailable'        => __('Time slot is unavailable', 'ameliabooking'),
            'total_cost_colon'             => __('Total Cost:', 'ameliabooking'),
            'total_number_of_persons'      => __('Total Number of People:', 'ameliabooking'),
            'view'                         => __('View', 'ameliabooking'),
            'select'                       => __('Select', 'ameliabooking'),
            'free'                         => __('Free', 'ameliabooking'),
        ];
    }

    /**
     * Returns the array of the frontend strings for the search shortcode
     */
    public static function getSearchStrings(): array
    {
        return [];
    }

    /**
     * Returns the array of the frontend strings for the booking shortcode
     */
    public static function getBookingStrings(): array
    {
        return [
            'continue'                     => __('Continue', 'ameliabooking'),
            'email_address_colon'          => __('Email Address', 'ameliabooking'),
            'get_in_touch'                 => __('Get in Touch', 'ameliabooking'),
            'collapse_menu'                => __('Collapse menu', 'ameliabooking'),
            'payment_onsite_sentence'      => __('The payment will be done on-site.', 'ameliabooking'),
            'payment_or_pay_with_card'     => __('Or pay with card', 'ameliabooking'),
            'phone_number_colon'           => __('Phone Number', 'ameliabooking'),
            'pick_date_and_time_colon'     => __('Pick date & time:', 'ameliabooking'),
            'please_select'                => __('Please select', 'ameliabooking'),
            'summary'                      => __('Summary', 'ameliabooking'),
            'total_amount_colon'           => __('Total Amount:', 'ameliabooking'),
            'your_name_colon'              => __('Your Name', 'ameliabooking'),

            'service_selection'            => __('Service Selection', 'ameliabooking'),
            'employee_selection'           => __('Employee Selection', 'ameliabooking'),
            'location_selection'           => __('Location Selection', 'ameliabooking'),
            'service_colon'                => __('Service', 'ameliabooking'),
            'please_select_service'        => __('Please select service', 'ameliabooking'),
            'dropdown_category_heading'    => __('Category', 'ameliabooking'),
            'dropdown_items_heading'       => __('Service', 'ameliabooking'),
            'date_time'                    => __('Date & Time', 'ameliabooking'),
            'info_step'                    => __('Your Information', 'ameliabooking'),
            'enter_first_name'             => __('Enter first name', 'ameliabooking'),
            'enter_last_name'              => __('Enter last name', 'ameliabooking'),
            'enter_email'                  => __('Enter email', 'ameliabooking'),
            'enter_phone'                  => __('Enter phone', 'ameliabooking'),
            'payment_step'                 => __('Payments', 'ameliabooking'),
            'summary_services'             => __('Services', 'ameliabooking'),
            'summary_person'               => __('person', 'ameliabooking'),
            'summary_persons'              => __('people', 'ameliabooking'),
            'summary_event'                => __('Event', 'ameliabooking'),
            'appointment_id'               => __('Appointment ID', 'ameliabooking'),
            'event_id'                     => __('Event ID', 'ameliabooking'),
            'congrats_payment'             => __('Payment', 'ameliabooking'),
            'congrats_date'                => __('Date', 'ameliabooking'),
            'congrats_time'                => __('Local Time', 'ameliabooking'),
            'congrats_service'             => __('Service', 'ameliabooking'),
            'congrats_employee'            => __('Employee', 'ameliabooking'),
            'show_more'                    => __('Show more', 'ameliabooking'),
            'show_less'                    => __('Show less', 'ameliabooking'),
            'learn_more'                   => __('Learn More', 'ameliabooking'),
            'view_in_package'              => __('View in Package', 'ameliabooking'),
            'service_information'          => __('Service information', 'ameliabooking'),
        ];
    }

    /**
     * Returns the array of the frontend strings for the event shortcode
     */
    public static function getEventStrings(): array
    {
        return [
            'event_book_event'          => __('Book event', 'ameliabooking'),
            'event_book'                => __('Book this event', 'ameliabooking'),
            'event_capacity'            => __('Capacity:', 'ameliabooking'),
            'event_filters'             => __('Filters', 'ameliabooking'),
            'event_start'               => __('Event Starts', 'ameliabooking'),
            'event_end'                 => __('Event Ends', 'ameliabooking'),
            'event_at'                  => __('at', 'ameliabooking'),
            'event_close'               => __('Close', 'ameliabooking'),
            'event_congrats'            => __('Congratulations', 'ameliabooking'),
            'event_payment'             => __('Payment', 'ameliabooking'),
            'event_customer_info'       => __('Your Information', 'ameliabooking'),
            'event_about_list'          => __('About Event', 'ameliabooking'),
            'events_available'          => __('Events Available', 'ameliabooking'),
            'event_available'           => __('Event Available', 'ameliabooking'),
            'event_search'              => __('Search for Events', 'ameliabooking'),
            'event_slot_left'           => __('slot left', 'ameliabooking'),
            'event_slots_left'          => __('slots left', 'ameliabooking'),
            'event_learn_more'          => __('Learn more', 'ameliabooking'),
            'event_read_more'           => __('Read more', 'ameliabooking'),
            'event_timetable'           => __('Timetable', 'ameliabooking'),
            'event_bringing'            => __('How many attendees do you want to book event for?', 'ameliabooking'),
            'event_show_less'           => __('Show less', 'ameliabooking'),
            'event_show_more'           => __('Show more', 'ameliabooking'),
            'event_location'            => __('Event Location', 'ameliabooking'),
            'no_events'                 => __('No results found...', 'ameliabooking'),
            'date_picker_placeholder'   => __('Date Picker', 'ameliabooking'),
        ];
    }

    /**
     * Returns the array of the frontend strings for the catalog shortcode
     */
    public static function getCatalogStrings(): array
    {
        return [
            'categories'                         => __('Categories', 'ameliabooking'),
            'category_colon'                     => __('Category:', 'ameliabooking'),
            'description'                        => __('Description', 'ameliabooking'),
            'info'                               => __('Info', 'ameliabooking'),
            'view_more'                          => __('View More', 'ameliabooking'),
            'view_all'                           => __('View All', 'ameliabooking'),
            'filter_input'                       => __('Search', 'ameliabooking'),
            'book_now'                           => __('Book Now', 'ameliabooking'),
            'about_service'                      => __('About Service', 'ameliabooking'),
            'view_all_photos'                    => __('View all photos', 'ameliabooking'),
            'back_btn'                           => __('Go Back', 'ameliabooking'),
            'heading_service'                    => __('Service', 'ameliabooking'),
            'heading_services'                   => __('Services', 'ameliabooking'),
            'no_search_data'                     => __('No results', 'ameliabooking'),
            'filter_all'                         => __('All', 'ameliabooking'),
        ];
    }

    /**
     * Returns the array of the frontend strings for the event shortcode
     */
    public static function getCabinetStrings(): array
    {
        return [
            'available'                              => __('Available', 'ameliabooking'),
            'booking_cancel_exception'               => __('Booking can\'t be canceled', 'ameliabooking'),
            'generate_payment_links'                 => __('Generate payment links', 'ameliabooking'),
            'generate_payment_links_tooltip'         => __('Check this box to generate a payment link.<br> To include it in the notification, add the payment link placeholder.', 'ameliabooking'),
            'no_results'                             => __('There are no results...', 'ameliabooking'),
            'select_customer'                        => __('Select Customer', 'ameliabooking'),
            'select_service'                         => __('Select Service', 'ameliabooking'),
            'subtotal'                               => __('Subtotal', 'ameliabooking'),
        ];
    }
}
