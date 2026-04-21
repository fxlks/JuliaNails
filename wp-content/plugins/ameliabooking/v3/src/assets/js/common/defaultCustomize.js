import { reactive } from "vue";

const globalLabels = reactive(window.wpAmeliaLabels)

// * sbsNew - step by step new
// * cbf    - catalog booking form
// * elf    - events list form
// * ecf    - events calendar form
// * capc   - cabinet panel customer
// * cape   - cabinet panel employee

let defaultCustomizeSettings = {
  fonts: {
    fontFamily: 'Amelia Roboto, sans-serif',
    fontUrl: '',
    customFontFamily: '',
    customFontSelected: false,
  },
  sbsNew: {
    colors: {
      colorPrimary: '#1246D6',
      colorSuccess: '#019719',
      colorError: '#B4190F',
      colorWarning: '#CCA20C',
      colorInpBgr: '#FFFFFF',
      colorInpBorder: '#D1D5D7',
      colorInpText: '#1A2C37',
      colorInpPlaceHolder: '#808A90',
      colorDropBgr: '#FFFFFF',
      colorDropBorder: '#FFFFFF',
      colorDropText: '#0E1920',
      colorSbBgr: '#17295A',
      colorSbText: '#FFFFFF',
      colorMainBgr: '#FFFFFF',
      colorMainHeadingText: '#33434C',
      colorMainText: '#1A2C37',
      colorCalCell: '#265CF2',
      colorCalCellText: '#1246D6',
      colorCalCellLow: '#265CF2',
      colorCalCellLowText: '#1246D6',
      colorCalCellHigh: '#265CF2',
      colorCalCellHighText: '#1246D6',
      colorCalCellWaiting: 'rgba(26, 44, 55, 0.04)',
      colorCalCellWaitingText: '#1A2C37',
      colorCalCellSelected: '#1246D6',
      colorCalCellSelectedText: '#FFFFFF',
      colorCalCellDisabled: 'rgba(87, 88, 139, 0.05)',
      colorCalCellDisabledText: '#8C959A',
      colorBtnPrim: '#265CF2',
      colorBtnPrimText: '#FFFFFF',
      colorBtnSec: '#1A2C37',
      colorBtnSecText: '#FFFFFF',
    },
    order: [
      {
        id: 'ServiceStep',
      },
      {
        id: 'EmployeeStep',
      },
      {
        id: 'LocationStep',
      },
    ],
    initStep: {
      options: {
        service: {
          name: globalLabels.service_input_field,
          filterable: true,
        },
        tax: {
          name: globalLabels.tax_visibility,
          visibility: true,
        },
        location: {
          name: globalLabels.location_input_field,
          required: false,
          visibility: true,
          filterable: true,
        },
        employee: {
          name: globalLabels.employee_input_field,
          required: false,
          visibility: true,
          filterable: true,
        },
        primaryFooterButton: {
          name: globalLabels.continue_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      order: [
        {
          name: globalLabels.service,
          id: 'service',
        },
        {
          name: globalLabels.employee,
          id: 'employee',
        },
        {
          name: globalLabels.location,
          id: 'location',
        },
      ],
      translations: null,
    },
    serviceStep: {
      options: {
        search: {
          name: globalLabels.search_input_field,
          visibility: true,
        },
        category: {
          name: globalLabels.category_input_field,
          visibility: true,
          filterable: true,
        },
        price: {
          name: globalLabels.service_price,
          visibility: true,
        },
        tax: {
          name: globalLabels.tax_visibility,
          visibility: true,
        },
        serviceCategory: {
          name: globalLabels.service_category,
          visibility: true,
        },
        serviceDuration: {
          name: globalLabels.service_duration,
          visibility: true,
        },
        serviceCapacity: {
          name: globalLabels.service_capacity,
          visibility: true,
        },
        serviceLocation: {
          name: globalLabels.service_location,
          visibility: true,
        },
        moreBtn: {
          name: globalLabels.learn_more_button,
          visibility: true,
        },
        packagesBtn: {
          name: globalLabels.view_in_package_buttons,
          visibility: true,
        },
        primaryFooterButton: {
          name: globalLabels.continue_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    employeeStep: {
      options: {
        employee: {
          name: globalLabels.employee_selection,
          required: false,
          visibility: true,
        },
        price: {
          name: globalLabels.employee_price,
          visibility: true,
        },
        moreBtn: {
          name: globalLabels.learn_more_button,
          visibility: true,
        },
        primaryFooterButton: {
          name: globalLabels.continue_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    locationStep: {
      options: {
        location: {
          name: globalLabels.location_selection,
          required: false,
          visibility: true,
        },
        address: {
          name: globalLabels.location_address,
          visibility: true,
        },
        moreBtn: {
          name: globalLabels.learn_more_button,
          visibility: true,
        },
        primaryFooterButton: {
          name: globalLabels.continue_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    bringingAnyone: {
      options: {
        heading: {
          name: globalLabels.heading,
          visibility: true,
        },
        info: {
          name: globalLabels.info,
          visibility: true,
        },
        bringingPrice: {
          name: globalLabels.bringing_price,
          visibility: true
        },
        primaryButton: {
          name: globalLabels.primary_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        secondaryButton: {
          name: globalLabels.secondary_button_type,
          buttonType: 'text',
          typeOptions: ['filled', 'plain', 'text'],
        },
        primaryFooterButton: {
          name: globalLabels.continue_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    packageStep: {
      options: {
        heading: {
          name: globalLabels.popup_heading,
          visibility: true,
        },
        tax: {
          name: globalLabels.tax_visibility,
          visibility: true,
        },
        primaryButton: {
          name: globalLabels.primary_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        primaryFooterButton: {
          name: globalLabels.continue_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    packageInfoStep: {
      options: {
        primaryFooterButton: {
          name: globalLabels.continue_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        tax: {
          name: globalLabels.tax_visibility,
          visibility: true,
        },
      },
      translations: null,
    },
    packageAppointmentsStep: {
      options: {
        employee: {
          name: globalLabels.employee_input_field,
          required: false,
          visibility: true,
        },
        location: {
          name: globalLabels.location_input_field,
          required: false,
          visibility: true,
        },
        endTimeVisibility: {
          name: globalLabels.calendar_slot_end_time,
          visibility: true,
        },
        timeZoneVisibility: {
          name: globalLabels.calendar_time_zone,
          visibility: true,
        },
        busyTimeSlotsVisibility: {
          name: globalLabels.show_busy_slots,
          visibility: false,
        },
        primaryFooterButton: {
          name: globalLabels.continue_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    packageAppointmentsListStep: {
      options: {
        primaryFooterButton: {
          name: globalLabels.continue_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        tax: {
          name: globalLabels.tax_visibility,
          visibility: true,
        },
      },
      translations: null,
    },
    extrasStep: {
      options: {
        heading: {
          name: globalLabels.extras_heading,
          visibility: true,
        },
        description: {
          name: globalLabels.extras_description,
          visibility: true,
        },
        tax: {
          name: globalLabels.tax_visibility,
          visibility: true,
        },
        duration: {
          name: globalLabels.extras_duration,
          visibility: true,
        },
        primaryFooterButton: {
          name: globalLabels.continue_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    dateTimeStep: {
      options: {
        endTimeVisibility: {
          name: globalLabels.calendar_slot_end_time,
          visibility: true,
        },
        timeZoneVisibility: {
          name: globalLabels.calendar_time_zone,
          visibility: true,
        },
        estimatedPricingVisibility: {
          name: globalLabels.show_estimated_pricing,
          visibility: false
        },
        slotPricingVisibility: {
          name: globalLabels.show_slot_pricing,
          visibility: false
        },
        indicatorPricingVisibility: {
          name: globalLabels.show_indicator_pricing,
          visibility: false
        },
        peopleWaitingVisibility: {
          name: globalLabels.show_people_waiting,
          visibility: true
        },
        busyTimeSlotsVisibility: {
          name: globalLabels.show_busy_slots,
          visibility: false,
        },
        primaryFooterButton: {
          name: globalLabels.continue_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    cartStep: {
      options: {
        addToCart: {
          name: globalLabels.add_to_cart_button_type,
          buttonType: 'text',
          typeOptions: ['filled', 'plain', 'text'],
        },
        backToCart: {
          name: globalLabels.back_to_cart_button_type,
          buttonType: 'text',
          typeOptions: ['filled', 'plain', 'text'],
        },
        tax: {
          name: globalLabels.tax_visibility,
          visibility: true,
        },
        primaryFooterButton: {
          name: globalLabels.continue_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    recurringPopup: {
      options: {
        content: {
          name: globalLabels.popup_content,
          visibility: true,
        },
        primaryButton: {
          name: globalLabels.primary_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        secondaryButton: {
          name: globalLabels.secondary_button_type,
          buttonType: 'text',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    recurringStep: {
      options: {
        heading: {
          name: globalLabels.heading_content,
          visibility: true,
        },
        primaryFooterButton: {
          name: globalLabels.continue_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    recurringSummary: {
      options: {
        endTimeVisibility: {
          name: globalLabels.calendar_slot_end_time,
          visibility: true,
        },
        timeZoneVisibility: {
          name: globalLabels.calendar_time_zone,
          visibility: true,
        },
        estimatedPricingVisibility: {
          name: globalLabels.show_estimated_pricing,
          visibility: false
        },
        slotPricingVisibility: {
          name: globalLabels.show_slot_pricing,
          visibility: false
        },
        indicatorPricingVisibility: {
          name: globalLabels.show_indicator_pricing,
          visibility: false
        },
        busyTimeSlotsVisibility: {
          name: globalLabels.show_busy_slots,
          visibility: false,
        },
        primaryFooterButton: {
          name: globalLabels.continue_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    infoStep: {
      order: [
        {
          name: globalLabels.first_name,
          id: 'firstName',
        },
        {
          name: globalLabels.last_name,
          id: 'lastName',
        },
        {
          name: globalLabels.email,
          id: 'email',
        },
        {
          name: globalLabels.phone,
          id: 'phone',
        },
      ],
      options: {
        lastName: {
          name: globalLabels.last_name_input_field,
          required: true,
          visibility: true,
        },
        email: {
          name: globalLabels.email_input_field,
          required: false,
          visibility: true,
        },
        phone: {
          name: globalLabels.phone_input_field,
          required: false,
          visibility: true,
        },
        primaryFooterButton: {
          name: globalLabels.continue_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    paymentStep: {
      options: {
        coupon: {
          name: globalLabels.coupon_input_field,
          required: false,
        },
        primaryFooterButton: {
          name: globalLabels.continue_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    congratulations: {
      options: {
        primaryFooterButton: {
          name: globalLabels.finish_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        secondaryFooterButton: {
          name: globalLabels.panel_button_type,
          buttonType: 'text',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    sidebar: {
      options: {
        self: {
          name: globalLabels.sidebar_visibility,
          visibility: true,
        },
        supportHeading: {
          name: globalLabels.support_heading,
          visibility: true,
        },
        companyPhone: {
          name: globalLabels.company_phone,
          visibility: true,
        },
        companyEmail: {
          name: globalLabels.company_email,
          visibility: true,
        },
      },
      translations: null,
    },
  },
  cbf: {
    colors: {
      colorPrimary: '#1246D6',
      colorSuccess: '#019719',
      colorError: '#B4190F',
      colorWarning: '#CCA20C',
      colorInpBgr: '#FFFFFF',
      colorInpBorder: '#D1D5D7',
      colorInpText: '#1A2C37',
      colorInpPlaceHolder: '#808A90',
      colorDropBgr: '#FFFFFF',
      colorDropBorder: '#FFFFFF',
      colorDropText: '#0E1920',
      colorSbBgr: '#FFFFFF',
      colorSbText: '#1A2C37',
      colorMainBgr: '#FFFFFF',
      colorMainHeadingText: '#33434C',
      colorMainText: '#1A2C37',
      colorCalCell: '#265CF2',
      colorCalCellText: '#1246D6',
      colorCalCellSelected: '#1246D6',
      colorCalCellSelectedText: '#FFFFFF',
      colorCalCellDisabled: 'rgba(87, 88, 139, 0.05)',
      colorCalCellDisabledText: '#8C959A',
      colorCardBgr: '#FFFFFF',
      colorCardBorder: '#D1D5D7',
      colorCardText: '#1A2C37',
      colorBtnPrim: '#265CF2',
      colorBtnPrimText: '#FFFFFF',
      colorBtnSec: '#1A2C37',
      colorBtnSecText: '#FFFFFF',
    },
    categoriesList: {
      options: {
        cardSideColor: {
          name: globalLabels.cl_side_color,
          visibility: true,
        },
        cardColor: {
          name: globalLabels.cl_color,
          visibility: false,
        },
        cardButton: {
          name: globalLabels.card_button_type,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
        services: {
          name: globalLabels.card_services_number,
          visibility: true,
        },
        packages: {
          name: globalLabels.card_packages_number,
          visibility: true,
        },
      },
      translations: null,
    },
    categoryItemsList: {
      options: {
        backBtn: {
          name: globalLabels.back_btn_type,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
        filterMenuBtn: {
          name: globalLabels.cil_filter_menu_btn,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
        searchInput: {
          name: globalLabels.cil_filter_input,
          visibility: true,
        },
        filterEmployee: {
          name: globalLabels.cil_filter_employee,
          visibility: true,
        },
        filterLocation: {
          name: globalLabels.cil_filter_location,
          visibility: true,
        },
        filterButtons: {
          name: globalLabels.cil_filter_buttons,
          visibility: true,
        },
        sidebar: {
          name: globalLabels.cil_sidebar,
          visibility: true,
        },
        cardColor: {
          name: globalLabels.cli_item_color,
          visibility: false,
        },
        packageBadge: {
          name: globalLabels.package_badge,
          visibility: true,
        },
        packagePrice: {
          name: globalLabels.package_price,
          visibility: true,
        },
        tax: {
          name: globalLabels.tax_visibility,
          visibility: true,
        },
        packageCategory: {
          name: globalLabels.package_category,
          visibility: true,
        },
        packageDuration: {
          name: globalLabels.package_duration,
          visibility: true,
        },
        packageCapacity: {
          name: globalLabels.package_capacity,
          visibility: true,
        },
        packageLocation: {
          name: globalLabels.package_location,
          visibility: true,
        },
        packageServices: {
          name: globalLabels.package_services_list,
          visibility: true,
        },
        serviceBadge: {
          name: globalLabels.service_badge,
          visibility: true,
        },
        servicePrice: {
          name: globalLabels.service_price,
          visibility: true,
        },
        serviceCategory: {
          name: globalLabels.service_category,
          visibility: true,
        },
        serviceDuration: {
          name: globalLabels.service_duration,
          visibility: true,
        },
        serviceCapacity: {
          name: globalLabels.service_capacity,
          visibility: true,
        },
        serviceLocation: {
          name: globalLabels.service_location,
          visibility: true,
        },
        cardEmployeeBtn: {
          name: globalLabels.employee_btn_type,
          visibility: true,
          buttonType: 'text',
          typeOptions: ['filled', 'plain', 'text'],
        },
        dialogEmployeeBtn: {
          name: globalLabels.book_employee_btn_type,
          visibility: true,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        cardContinueBtn: {
          name: globalLabels.continue_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        pageScroll: {
          name: globalLabels.page_scroll_visibility,
          visibility: true,
        },
      },
      translations: null,
    },
    categoryService: {
      options: {
        backBtn: {
          name: globalLabels.back_btn_type,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
        bookingBtn: {
          name: globalLabels.booking_btn_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        serviceBadge: {
          name: globalLabels.service_badge,
          visibility: true,
        },
        servicePrice: {
          name: globalLabels.service_price,
          visibility: true,
        },
        tax: {
          name: globalLabels.tax_visibility,
          visibility: true,
        },
        serviceCategory: {
          name: globalLabels.service_category,
          visibility: true,
        },
        serviceDuration: {
          name: globalLabels.service_duration,
          visibility: true,
        },
        serviceCapacity: {
          name: globalLabels.service_capacity,
          visibility: true,
        },
        serviceLocation: {
          name: globalLabels.service_location,
          visibility: true,
        },
        serviceDescription: {
          name: globalLabels.service_about,
          visibility: true,
        },
        serviceEmployees: {
          name: globalLabels.service_employees,
          visibility: true,
        },
        serviceEmployeePrice: {
          name: globalLabels.employee_price,
          visibility: true,
        },
        servicePackages: {
          name: globalLabels.package_block,
          visibility: true,
        },
        packagePrice: {
          name: globalLabels.package_price,
          visibility: true,
        },
        packageCategory: {
          name: globalLabels.package_category,
          visibility: true,
        },
        packageDuration: {
          name: globalLabels.package_duration,
          visibility: true,
        },
        packageCapacity: {
          name: globalLabels.package_capacity,
          visibility: true,
        },
        packageLocation: {
          name: globalLabels.package_location,
          visibility: true,
        },
        packageServices: {
          name: globalLabels.package_services_list,
          visibility: true,
        },
        pageScroll: {
          name: globalLabels.page_scroll_visibility,
          visibility: true,
        },
      },
      translations: null,
    },
    categoryPackage: {
      options: {
        backBtn: {
          name: globalLabels.back_btn_type,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
        bookingBtn: {
          name: globalLabels.booking_btn_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        packageBadge: {
          name: globalLabels.package_badge,
          visibility: true,
        },
        packagePrice: {
          name: globalLabels.package_price,
          visibility: true,
        },
        tax: {
          name: globalLabels.tax_visibility,
          visibility: true,
        },
        packageCategory: {
          name: globalLabels.package_category,
          visibility: true,
        },
        packageDuration: {
          name: globalLabels.package_duration,
          visibility: true,
        },
        packageCapacity: {
          name: globalLabels.package_capacity,
          visibility: true,
        },
        packageLocation: {
          name: globalLabels.package_location,
          visibility: true,
        },
        packageDescription: {
          name: globalLabels.package_about,
          visibility: true,
        },
        packageEmployees: {
          name: globalLabels.package_employees,
          visibility: true,
        },
        packageServices: {
          name: globalLabels.package_services_list,
          visibility: true,
        },
        pageScroll: {
          name: globalLabels.page_scroll_visibility,
          visibility: true,
        },
      },
      translations: null,
    },
  },
  elf: {
    colors: {
      colorPrimary: '#1246D6',
      colorSuccess: '#019719',
      colorError: '#B4190F',
      colorWarning: '#CCA20C',
      colorInpBgr: '#FFFFFF',
      colorInpBorder: '#D1D5D7',
      colorInpText: '#1A2C37',
      colorInpPlaceHolder: '#808A90',
      colorDropBgr: '#FFFFFF',
      colorDropBorder: '#FFFFFF',
      colorDropText: '#0E1920',
      colorMainBgr: '#FFFFFF',
      colorMainHeadingText: '#33434C',
      colorMainText: '#1A2C37',
      colorCalCell: '#265CF2',
      colorCalCellText: '#1246D6',
      colorCalCellSelected: '#1246D6',
      colorCalCellSelectedText: '#FFFFFF',
      colorCalCellDisabled: 'rgba(87, 88, 139, 0.05)',
      colorCalCellDisabledText: '#8C959A',
      colorCardBgr: '#FFFFFF',
      colorCardBorder: '#D1D5D7',
      colorCardText: '#1A2C37',
      colorBtnPrim: '#265CF2',
      colorBtnPrimText: '#FFFFFF',
      colorBtnSec: '#1A2C37',
      colorBtnSecText: '#FFFFFF',
      colorBtnWaiting: '#CCA20C',
      colorBtnWaitingText: '#FFFFFF',
    },
    list: {
      options: {
        header: {
          name: globalLabels.step_title,
          visibility: true,
        },
        filters: {
          name: globalLabels.step_filters,
          visibility: true,
        },
        imgTab: {
          name: globalLabels.event_tab_img,
          visibility: true,
        },
        location: {
          name: globalLabels.event_location,
          visibility: true,
        },
        slots: {
          name: globalLabels.event_slots_capacity,
          visibility: true,
        },
        price: {
          name: globalLabels.event_price,
          visibility: true,
        },
        tax: {
          name: globalLabels.tax_visibility,
          visibility: true,
        },
        status: {
          name: globalLabels.event_status,
          visibility: true,
        },
        filterBtn: {
          name: globalLabels.filters_button_type,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
        bookingBtn: {
          name: globalLabels.read_more_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        infoBtn: {
          name: globalLabels.learn_more_button_type,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
        waitingBtn: {
          name: globalLabels.booking_waiting_btn,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    info: {
      options: {
        gallery: {
          name: globalLabels.event_gallery,
          visibility: true,
        },
        location: {
          name: globalLabels.event_location,
          visibility: true,
        },
        slots: {
          name: globalLabels.event_slots_capacity,
          visibility: true,
        },
        price: {
          name: globalLabels.event_price,
          visibility: true,
        },
        tax: {
          name: globalLabels.tax_visibility,
          visibility: true,
        },
        status: {
          name: globalLabels.event_status,
          visibility: true,
        },
        eventDescription: {
          name: globalLabels.event_description,
          visibility: true,
        },
        eventOrganizer: {
          name: globalLabels.event_organizer,
          visibility: true,
        },
        eventEmployees: {
          name: globalLabels.event_employees,
          visibility: true,
        },
        primBtn: {
          name: globalLabels.booking_event_btn,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        secBtn: {
          name: globalLabels.close_event_btn,
          visibility: true,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
        waitingBtn: {
          name: globalLabels.booking_waiting_btn,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    bringingAnyone: {
      options: {
        primBtn: {
          name: globalLabels.booking_event_btn,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        secBtn: {
          name: globalLabels.close_event_btn,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    tickets: {
      options: {
        primBtn: {
          name: globalLabels.booking_event_btn,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        secBtn: {
          name: globalLabels.close_event_btn,
          visibility: true,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    customerInfo: {
      order: [
        {
          name: globalLabels.first_name,
          id: 'firstName',
        },
        {
          name: globalLabels.last_name,
          id: 'lastName',
        },
        {
          name: globalLabels.email,
          id: 'email',
        },
        {
          name: globalLabels.phone,
          id: 'phone',
        },
      ],
      options: {
        lastName: {
          name: globalLabels.last_name_input_field,
          required: true,
          visibility: true,
        },
        email: {
          name: globalLabels.email_input_field,
          required: false,
          visibility: true,
        },
        phone: {
          name: globalLabels.phone_input_field,
          required: false,
          visibility: true,
        },
        primBtn: {
          name: globalLabels.booking_event_btn,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        secBtn: {
          name: globalLabels.close_event_btn,
          visibility: true,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    payment: {
      options: {
        coupon: {
          name: globalLabels.coupon_input_field,
          required: false,
        },
        primBtn: {
          name: globalLabels.booking_event_btn,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        secBtn: {
          name: globalLabels.close_event_btn,
          visibility: true,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    congrats: {
      options: {
        primBtn: {
          name: globalLabels.finish_event_btn,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        secBtn: {
          name: globalLabels.customer_panel_btn,
          visibility: true,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
  },
  capc: {
    colors: {
      colorPrimary: '#1246D6',
      colorSuccess: '#019719',
      colorError: '#B4190F',
      colorWarning: '#CCA20C',
      colorInpBgr: '#FFFFFF',
      colorInpBorder: '#D1D5D7',
      colorInpText: '#1A2C37',
      colorInpPlaceHolder: '#808A90',
      colorDropBgr: '#FFFFFF',
      colorDropBorder: '#FFFFFF',
      colorDropText: '#0E1920',
      colorSbBgr: '#17295A',
      colorSbText: '#FFFFFF',
      colorMainBgr: '#FFFFFF',
      colorMainHeadingText: '#33434C',
      colorMainText: '#1A2C37',
      colorCalCell: '#265CF2',
      colorCalCellText: '#1246D6',
      colorCalCellLow: '#265CF2',
      colorCalCellLowText: '#1246D6',
      colorCalCellHigh: '#265CF2',
      colorCalCellHighText: '#1246D6',
      colorCalCellSelected: '#1246D6',
      colorCalCellSelectedText: '#FFFFFF',
      colorCalCellDisabled: 'rgba(87, 88, 139, 0.05)',
      colorCalCellDisabledText: '#8C959A',
      colorBtnPrim: '#265CF2',
      colorBtnPrimText: '#FFFFFF',
      colorBtnSec: '#1A2C37',
      colorBtnSecText: '#FFFFFF',
      colorBtnDanger: '#B4190F',
      colorBtnDangerText: '#FFFFFF',
    },
    sidebar: {
      options: {
        toggle: {
          name: globalLabels.toggled_sidebar,
          visibility: false,
        },
      },
      translations: null,
    },
    profile: {
      order: [
        {
          name: globalLabels.first_name,
          id: 'firstName',
        },
        {
          name: globalLabels.last_name,
          id: 'lastName',
        },
        {
          name: globalLabels.email,
          id: 'email',
        },
        {
          name: globalLabels.phone,
          id: 'phone',
        },
        {
          name: globalLabels.birthday,
          id: 'birthday',
        },
      ],
      options: {
        lastName: {
          name: globalLabels.last_name_input_field,
          required: true,
          visibility: true,
        },
        email: {
          name: globalLabels.email_input_field,
          required: false,
          visibility: true,
        },
        phone: {
          name: globalLabels.phone_input_field,
          required: false,
          visibility: true,
        },
        birthday: {
          name: globalLabels.birthday_input_field,
          required: false,
          visibility: true,
        },
        deleteFooterButton: {
          name: globalLabels.delete_profile_button_type,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
        saveFooterButton: {
          name: globalLabels.save_changes_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        passFooterButton: {
          name: globalLabels.change_pass_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    deleteProfile: {
      options: {
        closeBtn: {
          name: globalLabels.close_button_type,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
        confirmBtn: {
          name: globalLabels.confirm_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    appointments: {
      options: {
        timeZone: {
          name: globalLabels.time_zone,
          visibility: true,
        },
        servicesFilter: {
          name: globalLabels.csb_cust_filter_service,
          visibility: true,
        },
        providersFilter: {
          name: globalLabels.csb_cust_filter_employee,
          visibility: true,
        },
        locationsFilter: {
          name: globalLabels.csb_cust_filter_location,
          visibility: true,
        },
        filterBtn: {
          name: globalLabels.filters_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        employee: {
          name: globalLabels.csb_cust_appointment_employee,
          visibility: true,
        },
        price: {
          name: globalLabels.appointment_price,
          visibility: true,
        },
      },
      translations: null,
    },
    rescheduleAppointment: {
      options: {
        timeZoneVisibility: {
          name: globalLabels.calendar_time_zone,
          visibility: true,
        },
        estimatedPricingVisibility: {
          name: globalLabels.show_estimated_pricing,
          visibility: false
        },
        slotPricingVisibility: {
          name: globalLabels.show_slot_pricing,
          visibility: false
        },
        indicatorPricingVisibility: {
          name: globalLabels.show_indicator_pricing,
          visibility: false
        },
        endTimeVisibility: {
          name: globalLabels.calendar_slot_end_time,
          visibility: true,
        },
        cancelBtn: {
          name: globalLabels.cancel_button_type,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
        continueBtn: {
          name: globalLabels.continue_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    cancelAppointment: {
      options: {
        cancelBtn: {
          name: globalLabels.cancel_button_type,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
        confirmBtn: {
          name: globalLabels.confirm_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    events: {
      options: {
        timeZone: {
          name: globalLabels.time_zone,
          visibility: true,
        },
        eventsFilter: {
          name: globalLabels.csb_cust_filter_event,
          visibility: true,
        },
        providersFilter: {
          name: globalLabels.csb_cust_filter_employee,
          visibility: true,
        },
        locationsFilter: {
          name: globalLabels.csb_cust_filter_location,
          visibility: true,
        },
        filterBtn: {
          name: globalLabels.filters_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        employee: {
          name: globalLabels.csb_cust_event_employee,
          visibility: true,
        },
        price: {
          name: globalLabels.event_price,
          visibility: true,
        },
      },
      translations: null,
    },
    cancelEvent: {
      options: {
        cancelBtn: {
          name: globalLabels.cancel_button_type,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
        confirmBtn: {
          name: globalLabels.confirm_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    packagesList: {
      options: {
        timeZone: {
          name: globalLabels.time_zone,
          visibility: true,
        },
        packagesFilter: {
          name: globalLabels.csb_cust_filter_packages,
          visibility: true,
        },
        servicesFilter: {
          name: globalLabels.csb_cust_filter_service,
          visibility: true,
        },
        providersFilter: {
          name: globalLabels.csb_cust_filter_employee,
          visibility: true,
        },
        locationsFilter: {
          name: globalLabels.csb_cust_filter_location,
          visibility: true,
        },
        filterBtn: {
          name: globalLabels.filters_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    packageAppointmentsList: {
      options: {
        backBtn: {
          name: globalLabels.go_back_btn_type,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
        cancelBtn: {
          name: globalLabels.cancel_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        payBtn: {
          name: globalLabels.pay_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        bookBtn: {
          name: globalLabels.book_now_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        employee: {
          name: globalLabels.csb_cust_appointment_employee,
          visibility: true,
        },
        price: {
          name: globalLabels.appointment_price,
          visibility: true,
        },
      },
      translations: null,
    },
    bookAppointment: {
      options: {
        employee: {
          name: globalLabels.employee_input_field,
          required: false,
          visibility: true,
          filterable: true,
        },
        location: {
          name: globalLabels.location_input_field,
          required: false,
          visibility: true,
          filterable: true,
        },
        timeZoneVisibility: {
          name: globalLabels.calendar_time_zone,
          visibility: true,
        },
        endTimeVisibility: {
          name: globalLabels.calendar_slot_end_time,
          visibility: true,
        },
        cancelBtn: {
          name: globalLabels.cancel_button_type,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
        continueBtn: {
          name: globalLabels.continue_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    cancelPackage: {
      options: {
        cancelBtn: {
          name: globalLabels.cancel_button_type,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
        confirmBtn: {
          name: globalLabels.confirm_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    signIn: {
      options: {
        signInBtn: {
          name: globalLabels.sign_in_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    accessLink: {
      options: {
        sendBtn: {
          name: globalLabels.send_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    accessLinkSuccess: {
      translations: null,
    },
    setPass: {
      options: {
        newPassBtn: {
          name: globalLabels.new_pass_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
  },
  cape: {
    colors: {
      colorPrimary: '#1246D6',
      colorSuccess: '#019719',
      colorError: '#B4190F',
      colorWarning: '#CCA20C',
      colorInpBgr: '#FFFFFF',
      colorInpBorder: '#D1D5D7',
      colorInpText: '#1A2C37',
      colorInpPlaceHolder: '#808A90',
      colorDropBgr: '#FFFFFF',
      colorDropBorder: '#FFFFFF',
      colorDropText: '#0E1920',
      colorSbBgr: '#17295A',
      colorSbText: '#FFFFFF',
      colorMainBgr: '#FFFFFF',
      colorMainHeadingText: '#33434C',
      colorMainText: '#1A2C37',
      colorBtnPrim: '#265CF2',
      colorBtnPrimText: '#FFFFFF',
      colorBtnSec: '#1A2C37',
      colorBtnSecText: '#FFFFFF',
      colorBtnDanger: '#B4190F',
      colorBtnDangerText: '#FFFFFF',
    },
    sidebar: {
      options: {
        toggle: {
          name: globalLabels.toggled_sidebar,
          visibility: false,
        },
      },
      translations: null,
    },
    appointments: {
      options: {
        timeZone: {
          name: globalLabels.time_zone,
          visibility: true,
        },
        servicesFilter: {
          name: globalLabels.csb_cust_filter_service,
          visibility: true,
        },
        customersFilter: {
          name: globalLabels.csb_cust_filter_customer,
          visibility: true,
        },
        locationsFilter: {
          name: globalLabels.csb_cust_filter_location,
          visibility: true,
        },
        filterBtn: {
          name: globalLabels.filters_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        newAppBtn: {
          name: globalLabels.new_app_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        customer: {
          name: globalLabels.csb_cust_appointment_customer,
          visibility: true,
        },
        customerPhone: {
          name: globalLabels.csb_cust_appointment_customer_phone,
          visibility: true,
        },
        customerEmail: {
          name: globalLabels.csb_cust_appointment_customer_email,
          visibility: true,
        },
        price: {
          name: globalLabels.appointment_price,
          visibility: true,
        },
      },
      translations: null,
    },
    events: {
      options: {
        timeZone: {
          name: globalLabels.time_zone,
          visibility: true,
        },
        eventsFilter: {
          name: globalLabels.csb_cust_filter_event,
          visibility: true,
        },
        customersFilter: {
          name: globalLabels.csb_cust_filter_customer,
          visibility: true,
        },
        locationsFilter: {
          name: globalLabels.csb_cust_filter_location,
          visibility: true,
        },
        filterBtn: {
          name: globalLabels.filters_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        scanQrCodeBtn: {
          name: globalLabels.qr_etickets_evt_button,
          visibility: true,
        },
        newEvtBtn: {
          name: globalLabels.new_evt_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        customerPhone: {
          name: globalLabels.csb_cust_event_customer_phone,
          visibility: true,
        },
        customerEmail: {
          name: globalLabels.csb_cust_event_customer_email,
          visibility: true,
        },
        price: {
          name: globalLabels.event_price,
          visibility: true,
        },
      },
      translations: null,
    },
    signIn: {
      options: {
        signInBtn: {
          name: globalLabels.sign_in_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    accessLink: {
      options: {
        sendBtn: {
          name: globalLabels.send_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    accessLinkSuccess: {
      translations: null,
    },
    setPass: {
      options: {
        newPassBtn: {
          name: globalLabels.new_pass_button_type,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
  },
  ecf: {
    colors: {
      colorPrimary: '#1246D6',
      colorSuccess: '#019719',
      colorError: '#B4190F',
      colorWarning: '#CCA20C',
      colorInpBgr: '#FFFFFF',
      colorInpBorder: '#D1D5D7',
      colorInpText: '#1A2C37',
      colorInpPlaceHolder: '#808A90',
      colorDropBgr: '#FFFFFF',
      colorDropBorder: '#FFFFFF',
      colorDropText: '#0E1920',
      colorSbBgr: '#FFFFFF',
      colorSbText: '#1A2C37',
      colorMainBgr: '#FFFFFF',
      colorMainHeadingText: '#33434C',
      colorMainText: '#1A2C37',
      colorCardBgr: '#FFFFFF',
      colorCardBorder: '#D1D5D7',
      colorCardText: '#1A2C37',
      colorBtnPrim: '#265CF2',
      colorBtnPrimText: '#FFFFFF',
      colorBtnSec: '#1A2C37',
      colorBtnSecText: '#FFFFFF',
      colorBtnWaiting: '#CCA20C',
      colorBtnWaitingText: '#FFFFFF',
    },
    calendar: {
      options: {
        upcomingBlock: {
          name: globalLabels.upcoming_events_block,
          visibility: true,
        },
        search: {
          name: globalLabels.step_search,
          visibility: true,
        },
        filters: {
          name: globalLabels.step_filters,
          visibility: true,
        },
        capacity: {
          name: globalLabels.event_slots_capacity,
          visibility: true,
        },
        price: {
          name: globalLabels.event_price,
          visibility: true,
        },
        tax: {
          name: globalLabels.tax_visibility,
          visibility: true,
        },
        location: {
          name: globalLabels.event_location,
          visibility: true,
        },
        status: {
          name: globalLabels.event_status,
          visibility: true,
        },
      },
      translations: null,
    },
    info: {
      options: {
        gallery: {
          name: globalLabels.event_gallery,
          visibility: true,
        },
        location: {
          name: globalLabels.event_location,
          visibility: true,
        },
        slots: {
          name: globalLabels.event_slots_capacity,
          visibility: true,
        },
        price: {
          name: globalLabels.event_price,
          visibility: true,
        },
        tax: {
          name: globalLabels.tax_visibility,
          visibility: true,
        },
        status: {
          name: globalLabels.event_status,
          visibility: true,
        },
        eventDescription: {
          name: globalLabels.event_description,
          visibility: true,
        },
        eventOrganizer: {
          name: globalLabels.event_organizer,
          visibility: true,
        },
        eventEmployees: {
          name: globalLabels.event_employees,
          visibility: true,
        },
        primBtn: {
          name: globalLabels.booking_event_btn,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        waitingBtn: {
          name: globalLabels.booking_waiting_btn,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        secBtn: {
          name: globalLabels.close_event_btn,
          visibility: true,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    bringingAnyone: {
      options: {
        primBtn: {
          name: globalLabels.booking_event_btn,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        secBtn: {
          name: globalLabels.close_event_btn,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    tickets: {
      options: {
        primBtn: {
          name: globalLabels.booking_event_btn,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        secBtn: {
          name: globalLabels.close_event_btn,
          visibility: true,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    customerInfo: {
      order: [
        {
          name: globalLabels.first_name,
          id: 'firstName',
        },
        {
          name: globalLabels.last_name,
          id: 'lastName',
        },
        {
          name: globalLabels.email,
          id: 'email',
        },
        {
          name: globalLabels.phone,
          id: 'phone',
        },
      ],
      options: {
        lastName: {
          name: globalLabels.last_name_input_field,
          required: true,
          visibility: true,
        },
        email: {
          name: globalLabels.email_input_field,
          required: false,
          visibility: true,
        },
        phone: {
          name: globalLabels.phone_input_field,
          required: false,
          visibility: true,
        },
        primBtn: {
          name: globalLabels.booking_event_btn,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        secBtn: {
          name: globalLabels.close_event_btn,
          visibility: true,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    payment: {
      options: {
        coupon: {
          name: globalLabels.coupon_input_field,
          required: false,
        },
        primBtn: {
          name: globalLabels.booking_event_btn,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        secBtn: {
          name: globalLabels.close_event_btn,
          visibility: true,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
    congrats: {
      options: {
        primBtn: {
          name: globalLabels.finish_event_btn,
          buttonType: 'filled',
          typeOptions: ['filled', 'plain', 'text'],
        },
        secBtn: {
          name: globalLabels.customer_panel_btn,
          visibility: true,
          buttonType: 'plain',
          typeOptions: ['filled', 'plain', 'text'],
        },
      },
      translations: null,
    },
  },
}

let defaultTranslations = {
  sbsNew: {
    initStep: {
      header: {
        name: globalLabels.step_title,
        labels: {
          service_selection: null
        }
      },
      input: {
        name: globalLabels.input_fields,
        labels: {
          service_colon: null,
          select_service: null,
          please_select_service: null,
          location: null,
          select_location: null,
          please_select_location: null,
          employee_colon: null,
          select_employee: null,
          please_select_employee: null,
        }
      },
      dropdown: {
        name: globalLabels.dropdowns,
        labels: {
          dropdown_category_heading: null,
          dropdown_items_heading: null,
          dropdown_empty: null,
          total_tax_colon: null,
          incl_tax:null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          continue: null
        }
      },
      employeeDescription: {
        name: globalLabels.employee_description,
        labels: {
          employee_information: null,
          select_this_employee: null
        }
      }
    },
    serviceStep: {
      header: {
        name: globalLabels.step_title,
        labels: {
          service_selection: null
        }
      },
      filter: {
        name: globalLabels.step_filters,
        labels: {
          filter_input: null,
          select_service_category: null,
          dropdown_empty: null,
          no_results_found: null,
        }
      },
      card: {
        name: globalLabels.card_field,
        labels: {
          free: null,
          total_tax_colon: null,
          incl_tax:null,
          multiple_locations: null,
          learn_more: null,
          view_in_package: null,
        }
      },
      popup: {
        name: globalLabels.popup_content,
        labels: {
          service_information: null,
          employees: null,
          locations: null,
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          continue: null
        }
      },
    },
    employeeStep: {
      header: {
        name: globalLabels.step_title,
        labels: {
          employee_selection: null
        }
      },
      card: {
        name: globalLabels.card_field,
        labels: {
          any_employee: null,
          learn_more: null,
        }
      },
      popup: {
        name: globalLabels.popup_content,
        labels: {
          employee_information: null,
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          continue: null
        }
      },
    },
    locationStep: {
      header: {
        name: globalLabels.step_title,
        labels: {
          location_selection: null
        }
      },
      card: {
        name: globalLabels.card_field,
        labels: {
          any_location: null,
          learn_more: null,
        }
      },
      popup: {
        name: globalLabels.popup_content,
        labels: {
          location_information: null,
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          continue: null
        }
      }
    },
    bringingAnyone: {
      header: {
        name: globalLabels.step_title,
        labels: {
          bringing_anyone: null
        }
      },
      heading: {
        name: globalLabels.sub_step_title,
        labels: {
          bringing_anyone_title: null
        },
      },
      content: {
        name: globalLabels.content,
        labels: {
          bringing_people: null,
          add_people: null,
          bringing_price: null,
          bringing_people_total: null,
          add_people_total: null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          continue: null,
          bringing_yes: null,
          bringing_no: null
        }
      }
    },
    packageStep: {
      header: {
        name: globalLabels.step_title,
        labels: {
          package_selection: null
        }
      },
      heading: {
        name: globalLabels.sub_step_title,
        labels: {
          package_heading: null
        },
      },
      content: {
        name: globalLabels.content,
        labels: {
          discount_save: null,
          separator_or: null,
          total_tax_colon: null,
          incl_tax:null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          continue_without_package: null,
          continue: null
        }
      }
    },
    packageInfoStep: {
      header: {
        name: globalLabels.step_title,
        labels: {
          package_info_step: null
        }
      },
      heading: {
        name: globalLabels.heading_content,
        labels: {
          package_info_discount: null,
          package_book_service: null,
          expires_at: null,
          expires_after: null,
          expires_day: null,
          expires_days: null,
          expires_week: null,
          expires_weeks: null,
          expires_month: null,
          expires_months: null,
          without_expiration: null,
          multiple_locations: null,
          show_more: null,
          show_less: null,
          total_tax_colon: null,
          incl_tax:null
        },
      },
      content: {
        name: globalLabels.main_content,
        labels: {
          package_info_includes: null,
          package_info_employees: null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          continue: null
        }
      }
    },
    packageAppointmentsStep: {
      header: {
        name: globalLabels.step_title,
        labels: {
          package_appointment_step: null
        }
      },
      heading: {
        name: globalLabels.step_heading,
        labels: {
          package_appointment_required: null,
          package_appointment_remaining: null
        }
      },
      inputs: {
        name: globalLabels.input_field,
        labels: {
          package_appointment_employee: null,
          package_select_employee: null,
          package_appointment_location: null,
          package_select_location: null
        }
      },
      employeeDescriptionPackage: {
        name: globalLabels.employee_description,
        labels: {
          employee_information_package: null,
          select_this_employee_package: null
        }
      },
      content: {
        name: globalLabels.cards_and_buttons,
        labels: {
          package_appointments: null,
          package_appointments_date: null,
          package_appointments_select: null,
          package_appointments_selected: null,
          package_appointments_add_more: null,
          package_appointments_slots_selected: null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          continue: null
        }
      }
    },
    packageAppointmentsListStep: {
      header: {
        name: globalLabels.step_title,
        labels: {
          package_booking_overview: null
        }
      },
      content: {
        name: globalLabels.page_content,
        labels: {
          package_overview_all_selected: null,
          package_overview_selected_later: null,
          package_overview_info: null,
          package_overview_date: null,
          package_overview_time: null,
          package_overview_employee: null,
          package_overview_location: null,
          total_tax_colon: null,
          incl_tax:null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          continue: null
        }
      }
    },
    extrasStep: {
      header: {
        name: globalLabels.step_title,
        labels: {
          extras: null
        }
      },
      heading: {
        name: globalLabels.heading_content,
        labels: {
          extras_available: null,
          min_req_extras_colon: null
        }
      },
      card: {
        name: globalLabels.card_field,
        labels: {
          extras_card_open: null,
          extras_card_close: null,
          duration_colon: null,
          total_tax_colon: null,
          incl_tax:null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          continue: null
        }
      }
    },
    dateTimeStep: {
      header: {
        name: globalLabels.step_title,
        labels: {
          date_time: null
        }
      },
      content: {
        name: globalLabels.step_content,
        labels: {
          date_time_slots_selected: null,
          total_tax_colon: null,
          incl_tax:null,
          waiting_list: null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          continue: null
        }
      }
    },
    cartStep: {
      header: {
        name: globalLabels.step_title,
        labels: {
          cart_step: null
        }
      },
      content: {
        name: globalLabels.step_content,
        labels: {
          cart_title: null
        }
      },
      card: {
        name: globalLabels.card_field,
        labels: {
          service_colon: null,
          summary_person: null,
          summary_persons: null,
          summary_services_subtotal: null,
          extras: null,
          summary_extras_subtotal: null,
          total_price: null,
          info: null,
          delete: null,
          edit: null,
          total_tax_colon: null,
          incl_tax:null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          continue: null,
          cart_add_button: null,
          cancel: null
        }
      }
    },
    recurringStep: {
      header: {
        name: globalLabels.step_title,
        labels: {
          recurring_step: null
        }
      },
      heading: {
        name: globalLabels.heading_content,
        labels: {
          recurrence: null,
          recurrence_choosing_time: null
        }
      },
      content: {
        name: globalLabels.page_content,
        labels: {
          repeat_every: null,
          recurrence_day: null,
          recurrence_week: null,
          recurrence_month: null,
          recurrence_days: null,
          recurrence_weeks: null,
          recurrence_months: null,
          recurrence_repeat_on: null,
          recurrence_specific_date: null,
          recurrence_first: null,
          recurrence_second: null,
          recurrence_third: null,
          recurrence_fourth: null,
          recurrence_last: null,
          recurrence_ends: null,
          recurrence_choose_ends: null,
          recurrence_on: null,
          recurrence_select_date: null,
          recurrence_after: null,
          occurrences: null,
          appointment_repeats: null,
          recurrence_every: null,
          repeats_from: null,
          repeats_on: null,
          repeats_at: null,
          number_of_recurrences: null,
          ends_on: null,
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          continue: null
        }
      }
    },
    recurringSummary: {
      header: {
        name: globalLabels.step_title,
        labels: {
          recurring_summary: null
        }
      },
      alert: {
        name: globalLabels.alert_content,
        labels: {
          recurring_unavailable_slots: null,
          recurring_alert_content: null
        }
      },
      content: {
        name: globalLabels.card_content,
        labels: {
          recurring_chose_date: null,
          recurring_delete: null,
          recurring_slots_selected: null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          continue: null
        }
      }
    },
    recurringPopup: {
      header: {
        name: globalLabels.popup_title,
        labels: {
          repeat_appointment: null,
        }
      },
      content: {
        name: globalLabels.popup_content,
        labels: {
          repeat_appointment_quest: null
        }
      },
      footer: {
        name: globalLabels.popup_buttons,
        labels: {
          yes: null,
          no: null
        }
      }
    },
    infoStep: {
      header: {
        name: globalLabels.step_title,
        labels: {
          info_step: null
        }
      },
      social: {
        name: globalLabels.social_login,
        labels: {
          auto_fill_your_details: null,
          or_enter_details_below: null,
        }
      },
      subscribe: {
        name: globalLabels.mailchimp,
        labels: {
          subscribe_to_mailing_list: null,
        }
      },
      input: {
        name: globalLabels.input_fields,
        labels: {
          first_name_colon: null,
          enter_first_name: null,
          enter_first_name_warning: null,
          last_name_colon: null,
          enter_last_name: null,
          enter_last_name_warning: null,
          email_colon: null,
          enter_email: null,
          enter_valid_email_warning: null,
          phone_colon: null,
          enter_phone: null,
          enter_phone_warning: null,
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          continue: null
        }
      }
    },
    paymentStep: {
      header: {
        name: globalLabels.step_title,
        labels: {
          payment_step: null
        }
      },
      summarySegment: {
        name: globalLabels.summary_segment,
        labels: {
          summary: null,
          coupon: null,
          add_coupon_btn: null,
          summary_services: null,
          summary_services_subtotal: null,
          summary_person: null,
          summary_persons: null,
          summary_recurrence: null,
          summary_recurrences: null,
          summary_extras: null,
          summary_extras_subtotal: null,
          summary_package: null,
          total_price: null,
          subtotal: null,
          discount_amount_colon: null,
          total_amount_colon: null,
          paying_now: null,
          paying_later: null,
          full_amount_consent: null,
          total_tax_colon: null,
          incl_tax:null
        }
      },
      paymentSegment: {
        name: globalLabels.payment_segment,
        labels: {
          payment_method: null,
          on_site: null,
          card_number_colon: null,
          expires_date_colon: null,
          payment_protected_policy: null,
          payment_onsite_sentence: null,
          payment_wc_mollie_sentence: null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          continue: null
        }
      }
    },
    congratulations: {
      heading: {
        name: globalLabels.heading_title,
        labels: {
          congratulations: null,
          appointment_id: null,
          your_position_on_waiting_list: null,
          appointment_waiting_list_notify_message: null
        }
      },
      content: {
        name: globalLabels.content,
        labels: {
          add_to_calendar: null,
          congrats_cart: null,
          congrats_date: null,
          congrats_time: null,
          congrats_service: null,
          congrats_package: null,
          congrats_employee: null,
          congrats_location: null,
          congrats_total_amount: null,
          congrats_payment: null,
          your_name_colon: null,
          email_address_colon: null,
          phone_number_colon: null,
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          finish_appointment: null,
          congrats_panel: null
        }
      }
    },
    sidebar: {
      content: {
        name: globalLabels.content,
        labels: {
          plus_more: null,
        }
      },
      footer: {
        name: globalLabels.sidebar_footer,
        labels: {
          get_in_touch: null,
          collapse_menu: null
        }
      }
    }
  },
  cbf: {
    categoriesList: {
      cardFooter: {
        name: globalLabels.cl_btn,
        labels: {
          view_all: null
        }
      }
    },
    categoryItemsList: {
      backBtn: {
        name: globalLabels.back_btn_heading,
        labels: {
          back_btn: null
        }
      },
      filterBlock: {
        name: globalLabels.cil_filter_block,
        labels: {
          filter_input: null,
          filter_employee: null,
          filter_location: null,
          filter_all: null,
          filter_packages: null,
          filter_services: null,
        }
      },
      sideMenu: {
        name: globalLabels.cil_side_menu_block,
        labels: {
          get_in_touch: null,
          collapse_menu: null
        }
      },
      page: {
        name: globalLabels.cil_main_content,
        labels: {
          available: null,
          heading_service: null,
          heading_services: null,
          package: null,
          packages: null,
          no_search_data: null,
          save: null,
          free: null,
          expires_at: null,
          expires_after: null,
          expires_day: null,
          expires_days: null,
          expires_week: null,
          expires_weeks: null,
          expires_month: null,
          expires_months: null,
          without_expiration: null,
          multiple_locations: null,
          in_package: null,
          view_employees: null,
          continue: null,
          total_tax_colon: null,
          incl_tax: null
        }
      },
      employeeDialog: {
        name: globalLabels.employee_dialog,
        labels: {
          employee_info: null,
          book_service: null,
          book_package: null
        }
      }
    },
    categoryService: {
      header: {
        name: globalLabels.cil_main_header,
        labels: {
          back_btn: null,
          heading_service: null,
          book_now: null,
          free: null,
          multiple_locations: null,
        }
      },
      page: {
        name: globalLabels.cil_main_content,
        labels: {
          view_all_photos: null,
          about_service: null,
          tab_employees: null,
          total_tax_colon: null,
          incl_tax: null
        }
      },
      packBlock: {
        name: globalLabels.package_block,
        labels: {
          service_available_in_package: null,
          save: null,
          more_packages: null,
          less_packages: null,
          expires_at: null,
          expires_after: null,
          expires_day: null,
          expires_days: null,
          expires_week: null,
          expires_weeks: null,
          expires_month: null,
          expires_months: null,
          without_expiration: null,
          in_package: null
        }
      }
    },
    categoryPackage: {
      header: {
        name: globalLabels.cil_main_header,
        labels: {
          back_btn: null,
          package: null,
          save: null,
          book_now: null,
          free: null,
          expires_at: null,
          expires_after: null,
          expires_day: null,
          expires_days: null,
          expires_week: null,
          expires_weeks: null,
          expires_month: null,
          expires_months: null,
          without_expiration: null,
          multiple_locations: null,
        }
      },
      page: {
        name: globalLabels.cil_main_content,
        labels: {
          view_all_photos: null,
          about_package: null,
          tab_employees: null,
          package_includes: null,
          package_book_service: null,
          total_tax_colon: null,
          incl_tax:null
        }
      },
    }
  },
  elf: {
    list: {
      header: {
        name: globalLabels.step_title,
        labels: {
          event_available: null,
          events_available: null
        }
      },
      filter: {
        name: globalLabels.step_filters,
        labels: {
          event_search: null,
          event_filters: null,
          event_type: null,
          event_location: null
        }
      },
      card: {
        name: globalLabels.event_card,
        labels: {
          event_begins: null,
          from: null,
          event_free: null,
          event_slot_left: null,
          event_slots_left: null,
          event_learn_more: null,
          event_read_more: null,
          open: null,
          full: null,
          upcoming: null,
          closed: null,
          canceled: null,
          total_tax_colon: null,
          incl_tax:null,
          person_waiting: null,
          people_waiting: null,
          join_waiting_list: null
        }
      },
      pagination: {
        name: globalLabels.step_pagination,
        labels: {
          event_page: null
        }
      }
    },
    info: {
      header: {
        name: globalLabels.step_header,
        labels: {
          event_begins: null,
          from: null,
          event_free: null,
          event_slot_left: null,
          event_slots_left: null,
          open: null,
          full: null,
          upcoming: null,
          closed: null,
          canceled: null,
          total_tax_colon: null,
          incl_tax:null,
          person_waiting: null,
          people_waiting: null,
        }
      },
      tabInfo: {
        name: globalLabels.step_info_tab,
        labels: {
          event_info: null,
          event_timetable: null,
          description: null,
          show_more: null,
          show_less: null,
          event_organizer: null,
          about: null,
        }
      },
      tabTickets: {
        name: globalLabels.step_tickets_tab,
        labels: {
          event_tickets: null,
          event_tickets_left: null,
          event_ticket_left: null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          event_book_event: null,
          event_close: null,
          join_waiting_list: null
        }
      }
    },
    bringingAnyone: {
      header: {
        name: globalLabels.step_title,
        labels: {
          event_bringing: null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          back_btn: null,
          continue: null,
        }
      }
    },
    tickets: {
      header: {
        name: globalLabels.step_title,
        labels: {
          event_select_tickets: null
        }
      },
      heading: {
        name: globalLabels.step_heading,
        labels: {
          event_tickets: null,
          event_tickets_context: null,
          event_ticket_types: null
        }
      },
      content: {
        name: globalLabels.step_content,
        labels: {
          event_ticket_left: null,
          event_tickets_left: null,
          person_waiting: null,
          people_waiting: null,
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          continue: null,
          event_close: null,
          join_waiting_list: null,
        }
      }
    },
    customerInfo: {
      header: {
        name: globalLabels.step_title,
        labels: {
          event_customer_info: null
        }
      },
      social: {
        name: globalLabels.social_login,
        labels: {
          auto_fill_your_details: null,
          or_enter_details_below: null
        }
      },
      subscribe: {
        name: globalLabels.mailchimp,
        labels: {
          subscribe_to_mailing_list: null,
        }
      },
      input: {
        name: globalLabels.input_fields,
        labels: {
          first_name_colon: null,
          enter_first_name: null,
          enter_first_name_warning: null,
          last_name_colon: null,
          enter_last_name: null,
          enter_last_name_warning: null,
          email_colon: null,
          enter_email: null,
          enter_valid_email_warning: null,
          phone_colon: null,
          enter_phone: null,
          enter_phone_warning: null,
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          continue: null,
          event_close: null
        }
      }
    },
    payment: {
      header: {
        name: globalLabels.step_title,
        labels: {
          event_payment: null
        }
      },
      summarySegment: {
        name: globalLabels.summary_segment,
        labels: {
          summary: null,
          summary_event: null,
          summary_person: null,
          summary_persons: null,
          subtotal: null,
          discount_amount_colon: null,
          total_amount_colon: null,
          paying_now: null,
          paying_later: null,
          full_amount_consent: null,
          total_tax_colon: null,
          incl_tax:null
        }
      },
      coupon: {
        name: globalLabels.coupon_segment,
        labels: {
          coupon: null,
          add_coupon_btn: null,
          coupons_used: null,
        }
      },
      paymentSegment: {
        name: globalLabels.payment_segment,
        labels: {
          payment_method: null,
          on_site: null,
          card_number_colon: null,
          expires_date_colon: null,
          payment_protected_policy: null,
          payment_onsite_sentence: null,
          payment_wc_mollie_sentence: null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          event_book_event: null,
          event_close: null
        }
      }
    },
    congrats: {
      heading: {
        name: globalLabels.step_title,
        labels: {
          event_congrats: null,
          your_position_on_waiting_list: null,
          waiting_list_notify_message: null
        }
      },
      content: {
        name: globalLabels.content,
        labels: {
          event_id: null,
          event_about_list: null,
          event_start: null,
          event_end: null,
          event_at: null,
          event_location: null,
          event_tickets: null,
          event_show_more: null,
          event_show_less: null,
          congrats_total_amount: null,
          congrats_payment: null,
          your_name_colon: null,
          email_address_colon: null,
          phone_number_colon: null,
          add_to_calendar: null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          finish_appointment: null,
          congrats_panel: null
        }
      }
    }
  },
  capc: {
    sidebar: {
      menu: {
        name: globalLabels.mobile_menu,
        labels: {
          menu_title: null
        }
      },
      footer: {
        name: globalLabels.sidebar_footer,
        labels: {
          toggle_sidebar: null,
          log_out: null
        }
      }
    },
    profile: {
      header: {
        name: globalLabels.step_title,
        labels: {
          my_profile: null
        }
      },
      tab: {
        name: globalLabels.tab_title,
        labels: {
          personal_info: null,
          password_tab: null,
        }
      },
      input: {
        name: globalLabels.input_fields,
        labels: {
          first_name_colon: null,
          enter_first_name: null,
          enter_first_name_warning: null,
          last_name_colon: null,
          enter_last_name: null,
          enter_last_name_warning: null,
          email_colon: null,
          enter_email: null,
          enter_valid_email_warning: null,
          phone_colon: null,
          enter_phone: null,
          enter_phone_warning: null,
          date_of_birth: null,
          enter_date_of_birth: null,
          enter_date_of_birth_warning: null,
          new_password_colon: null,
          new_password_required: null,
          new_password_length: null,
          new_password_colon_retype: null,
          passwords_not_match: null
        }
      },
      message: {
        name: globalLabels.step_message,
        labels: {
          profile_data_success: null,
          password_success: null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          delete_profile: null,
          save_changes: null,
          change_password: null
        }
      }
    },
    deleteProfile: {
      heading: {
        name: globalLabels.heading_title,
        labels: {
          delete_profile: null
        }
      },
      content: {
        name: globalLabels.content,
        labels: {
          delete_profile_description: null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          close: null,
          delete: null
        }
      }
    },
    appointments: {
      heading: {
        name: globalLabels.heading_title,
        labels: {
          appointments: null
        }
      },
      filter: {
        name: globalLabels.step_filters,
        labels: {
          filters: null,
          services_dropdown: null,
          providers_dropdown: null,
          locations_dropdown: null,
        }
      },
      status: {
        name: globalLabels.item_status,
        labels: {
          approved:null,
          pending: null,
          canceled: null,
          rejected: null
        }
      },
      card: {
        name: globalLabels.page_card,
        labels: {
          pay_now_btn: null,
          paid: null,
          reschedule: null,
          preview_invoice: null,
          download_invoice: null,
          cancel: null,
          provider_profile: null,
          google_meet_link: null,
          microsoft_teams_link: null,
          zoom_link: null,
          lesson_space_link: null,
          extras: null,
          custom_fields: null
        }
      },
      messages: {
        name: globalLabels.page_messages,
        labels: {
          appointment_rescheduled: null,
          appointment_canceled: null,
        }
      },
      emptyState: {
        name: globalLabels.cust_empty_state,
        labels: {
          no_app_found: null,
          have_no_app: null
        }
      }
    },
    rescheduleAppointment: {
      heading: {
        name: globalLabels.heading_title,
        labels: {
          no_selected_slot_requirements: null
        }
      },
      content: {
        name: globalLabels.content,
        labels: {
          date_time_slots_selected: null,
          time_slot_unavailable: null,
          booking_reschedule_exception: null
        }
      },
      message: {
        name: globalLabels.step_message,
        labels: {
          booking_cancel_exception: null,
          time_slot_unavailable: null,
          booking_reschedule_exception: null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          cancel: null,
          continue: null
        }
      },
    },
    cancelAppointment: {
      heading: {
        name: globalLabels.heading_title,
        labels: {
          cancel_appointment: null
        }
      },
      content: {
        name: globalLabels.content,
        labels: {
          confirm_cancel_appointment: null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          close: null,
          confirm: null
        }
      }
    },
    events: {
      heading: {
        name: globalLabels.heading_title,
        labels: {
          events: null
        }
      },
      filter: {
        name: globalLabels.step_filters,
        labels: {
          filters: null,
          events_dropdown: null,
          providers_dropdown: null,
          locations_dropdown: null,
        }
      },
      status: {
        name: globalLabels.item_status,
        labels: {
          approved:null,
          canceled: null,
          rejected: null
        }
      },
      card: {
        name: globalLabels.page_card,
        labels: {
          pay_now_btn: null,
          paid: null,
          cancel: null,
          preview_invoice: null,
          download_invoice: null,
          event_organizer: null,
          event_staff: null,
          provider_profile: null,
          event_timetable: null,
          google_meet_link: null,
          microsoft_teams_link: null,
          zoom_link: null,
          lesson_space_link: null,
          custom_fields: null,
          event_tickets: null,
          event_ticket: null,
          e_tickets: null,
        }
      },
      messages: {
        name: globalLabels.page_messages,
        labels: {
          event_canceled: null
        }
      },
      emptyState: {
        name: globalLabels.cust_empty_state,
        labels: {
          no_evt_found: null,
          have_no_evt: null
        }
      }
    },
    cancelEvent: {
      heading: {
        name: globalLabels.heading_title,
        labels: {
          cancel_event: null
        }
      },
      content: {
        name: globalLabels.content,
        labels: {
          confirm_cancel_event: null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          close: null,
          confirm: null
        }
      }
    },
    packagesList: {
      heading: {
        name: globalLabels.heading_title,
        labels: {
          packages: null
        }
      },
      filter: {
        name: globalLabels.step_filters,
        labels: {
          filters: null,
          packages_dropdown: null,
          services_dropdown: null,
          providers_dropdown: null,
          locations_dropdown: null,
        }
      },
      card: {
        name: globalLabels.page_card,
        labels: {
          package_book_expire: null,
          package_book_expiration: null,
          package_book_unlimited: null,
          preview_invoice: null,
          download_invoice: null,
          appointments_booked: null,
          appointment_booked: null,
          package_deal_expire_in: null,
          expires_days: null,
          appointments_deal_expire: null,
        }
      },
      emptyState: {
        name: globalLabels.cust_empty_state,
        labels: {
          no_pack_found: null,
          have_no_pack: null
        }
      }
    },
    packageAppointmentsList: {
      backBtn: {
        name: globalLabels.back_btn_heading,
        labels: {
          back_btn: null
        }
      },
      content: {
        name: globalLabels.content,
        labels: {
          package_book_expire: null,
          package_book_expiration: null,
          package_book_unlimited: null,
          appointment_booked: null,
          appointments_booked: null,
          package_cancel: null,
          pay_now_btn: null,
          paid: null,
          book_now: null
        }
      },
      status: {
        name: globalLabels.item_status,
        labels: {
          approved:null,
          pending: null,
          canceled: null,
          rejected: null
        }
      },
      card: {
        name: globalLabels.page_card,
        labels: {
          reschedule: null,
          preview_invoice: null,
          download_invoice: null,
          cancel: null,
          google_meet_link: null,
          microsoft_teams_link: null,
          zoom_link: null,
          lesson_space_link: null,
          extras: null,
          custom_fields: null
        }
      },
      messages: {
        name: globalLabels.page_messages,
        labels: {
          booking_added_success: null,
          package_purchase_canceled: null
        }
      }
    },
    bookAppointment: {
      heading: {
        name: globalLabels.heading_title,
        labels: {
          no_selected_slot_requirements: null
        }
      },
      input: {
        name: globalLabels.input_fields,
        labels: {
          package_appointment_employee: null,
          package_select_employee: null,
          please_select_employee: null,
          employee_information_package: null,
          select_this_employee_package: null,
          package_appointment_location: null,
          package_select_location: null,
          please_select_location: null
        }
      },
      content: {
        name: globalLabels.content,
        labels: {
          date_time_slots_selected: null,
          time_slot_unavailable: null,
          package_booking_unavailable: null
        }
      },
      message: {
        name: globalLabels.step_message,
        labels: {
          customer_already_booked_app: null,
          time_slot_unavailable: null,
          package_booking_unavailable: null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          cancel: null,
          continue: null
        }
      }
    },
    cancelPackage: {
      heading: {
        name: globalLabels.heading_title,
        labels: {
          cancel_package: null
        }
      },
      content: {
        name: globalLabels.content,
        labels: {
          confirm_cancel_package: null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          close: null,
          confirm: null
        }
      }
    },
    signIn: {
      alert: {
        name: globalLabels.alert_message,
        labels: {
          profile_deleted: null
        }
      },
      heading: {
        name: globalLabels.heading_title,
        labels: {
          welcome_back: null,
          enter_credentials: null
        }
      },
      social: {
        name: globalLabels.social_login,
        labels: {
          or_enter_details_below: null,
        }
      },
      input: {
        name: globalLabels.input_fields,
        labels: {
          email_or_username: null,
          enter_email_or_username_warning: null,
          password: null,
          enter_password_warning: null
        }
      },
      footer: {
        name: globalLabels.footer_content,
        labels: {
          sign_in: null,
          forgot_your_password: null,
          reset_password: null,
        }
      }
    },
    accessLink: {
      heading: {
        name: globalLabels.heading_title,
        labels: {
          access_link_send: null,
          access_link_send_description: null
        }
      },
      input: {
        name: globalLabels.input_fields,
        labels: {
          email: null,
          enter_email_warning: null
        }
      },
      footer: {
        name: globalLabels.footer_content,
        labels: {
          send: null,
          sign_in: null
        }
      }
    },
    accessLinkSuccess: {
      heading: {
        name: globalLabels.heading_title,
        labels: {
          access_link_send_check: null,
          access_link_send_click: null
        }
      },
      footer: {
        name: globalLabels.footer_content,
        labels: {
          access_link_send_inbox: null,
          access_link_send_retry: null
        }
      }
    },
    setPass: {
      heading: {
        name: globalLabels.heading_title,
        labels: {
          new_password_set: null,
          new_password_set_description: null
        }
      },
      input: {
        name: globalLabels.input_fields,
        labels: {
          new_password_colon: null,
          new_password_required: null,
          new_password_length: null,
          new_password_colon_retype: null
        }
      },
      footer: {
        name: globalLabels.footer_content,
        labels: {
          new_password_set_action: null
        }
      }
    }
  },
  cape: {
    signIn: {
      alert: {
        name: globalLabels.alert_message,
        labels: {
          profile_deleted: null
        }
      },
      heading: {
        name: globalLabels.heading_title,
        labels: {
          welcome_back: null,
          enter_credentials: null
        }
      },
      social: {
        name: globalLabels.social_login,
        labels: {
          or_enter_details_below: null,
        }
      },
      input: {
        name: globalLabels.input_fields,
        labels: {
          email_or_username: null,
          enter_email_or_username_warning: null,
          password: null,
          enter_password_warning: null
        }
      },
      footer: {
        name: globalLabels.footer_content,
        labels: {
          sign_in: null,
          forgot_your_password: null,
          reset_password: null,
        }
      }
    },
    accessLink: {
      heading: {
        name: globalLabels.heading_title,
        labels: {
          access_link_send: null,
          access_link_send_description: null
        }
      },
      input: {
        name: globalLabels.input_fields,
        labels: {
          email: null,
          enter_email_warning: null
        }
      },
      footer: {
        name: globalLabels.footer_content,
        labels: {
          send: null,
          sign_in: null
        }
      }
    },
    accessLinkSuccess: {
      heading: {
        name: globalLabels.heading_title,
        labels: {
          access_link_send_check: null,
          access_link_send_click: null
        }
      },
      footer: {
        name: globalLabels.footer_content,
        labels: {
          access_link_send_inbox: null,
          access_link_send_retry: null
        }
      }
    },
    setPass: {
      heading: {
        name: globalLabels.heading_title,
        labels: {
          new_password_set: null,
          new_password_set_description: null
        }
      },
      input: {
        name: globalLabels.input_fields,
        labels: {
          new_password_colon: null,
          new_password_required: null,
          new_password_length: null,
          new_password_colon_retype: null
        }
      },
      footer: {
        name: globalLabels.footer_content,
        labels: {
          new_password_set_action: null
        }
      }
    }
  },
  ecf: {
    calendar: {
      filters: {
        name: globalLabels.step_filters,
        labels: {
          event_search: null,
          event_filters: null,
          event_type: null,
          event_location: null,
          event_employee: null,
          event_status: null,
          open: null,
          full: null,
          upcoming: null,
          closed: null,
          canceled: null,
        }
      },
      heading: {
        name: globalLabels.step_heading,
        labels: {
          event_today: null
        }
      },
      page: {
        name: globalLabels.step_content,
        labels: {
          event_slot_left: null,
          event_slots_left: null,
          event_no_spots: null,
          join_waiting_list: null,
          waiting_list:  null,
          person_waiting: null,
          people_waiting: null,
          events: null,
          event_upcoming_events: null,
          upcoming: null,
          event_begins: null,
          event_at: null,
          event_free: null,
          from: null,
          total_tax_colon: null,
          incl_tax:null,
          evt_empty_message: null
        }
      }
    },
    info: {
      header: {
        name: globalLabels.step_header,
        labels: {
          event_begins: null,
          from: null,
          event_free: null,
          event_slot_left: null,
          event_slots_left: null,
          open: null,
          full: null,
          upcoming: null,
          closed: null,
          canceled: null,
          total_tax_colon: null,
          incl_tax:null,
          person_waiting: null,
          people_waiting: null,
        }
      },
      tabInfo: {
        name: globalLabels.step_info_tab,
        labels: {
          event_info: null,
          event_timetable: null,
          description: null,
          show_more: null,
          show_less: null,
          event_organizer: null,
          about: null,
        }
      },
      tabTickets: {
        name: globalLabels.step_tickets_tab,
        labels: {
          event_tickets: null,
          event_tickets_left: null,
          event_ticket_left: null,
          person_waiting: null,
          people_waiting: null,
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          event_book_event: null,
          event_close: null,
          join_waiting_list: null,
        }
      }
    },
    bringingAnyone: {
      header: {
        name: globalLabels.step_title,
        labels: {
          event_bringing: null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          back_btn: null,
          continue: null,
        }
      }
    },
    tickets: {
      header: {
        name: globalLabels.step_title,
        labels: {
          event_select_tickets: null
        }
      },
      heading: {
        name: globalLabels.step_heading,
        labels: {
          event_tickets: null,
          event_tickets_context: null,
          event_ticket_types: null
        }
      },
      content: {
        name: globalLabels.step_content,
        labels: {
          event_ticket_left: null,
          event_tickets_left: null,
          person_waiting: null,
          people_waiting: null,
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          continue: null,
          event_close: null,
          join_waiting_list: null,
        }
      }
    },
    customerInfo: {
      header: {
        name: globalLabels.step_title,
        labels: {
          event_customer_info: null
        }
      },
      social: {
        name: globalLabels.social_login,
        labels: {
          auto_fill_your_details: null,
          or_enter_details_below: null,
        }
      },
      subscribe: {
        name: globalLabels.mailchimp,
        labels: {
          subscribe_to_mailing_list: null,
        }
      },
      input: {
        name: globalLabels.input_fields,
        labels: {
          first_name_colon: null,
          enter_first_name: null,
          enter_first_name_warning: null,
          last_name_colon: null,
          enter_last_name: null,
          enter_last_name_warning: null,
          email_colon: null,
          enter_email: null,
          enter_valid_email_warning: null,
          phone_colon: null,
          enter_phone: null,
          enter_phone_warning: null,
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          continue: null,
          event_close: null
        }
      }
    },
    payment: {
      header: {
        name: globalLabels.step_title,
        labels: {
          event_payment: null
        }
      },
      summarySegment: {
        name: globalLabels.summary_segment,
        labels: {
          summary: null,
          summary_event: null,
          summary_person: null,
          summary_persons: null,
          subtotal: null,
          discount_amount_colon: null,
          total_amount_colon: null,
          paying_now: null,
          paying_later: null,
          full_amount_consent: null,
          total_tax_colon: null,
          incl_tax:null
        }
      },
      coupon: {
        name: globalLabels.coupon_segment,
        labels: {
          coupon: null,
          add_coupon_btn: null,
          coupons_used: null,
        }
      },
      paymentSegment: {
        name: globalLabels.payment_segment,
        labels: {
          payment_method: null,
          on_site: null,
          card_number_colon: null,
          expires_date_colon: null,
          payment_protected_policy: null,
          payment_onsite_sentence: null,
          payment_wc_mollie_sentence: null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          event_book_event: null,
          event_close: null
        }
      }
    },
    congrats: {
      heading: {
        name: globalLabels.step_title,
        labels: {
          event_congrats: null,
          your_position_on_waiting_list: null,
          waiting_list_notify_message: null
        }
      },
      content: {
        name: globalLabels.content,
        labels: {
          event_id: null,
          event_about_list: null,
          event_start: null,
          event_end: null,
          event_at: null,
          event_location: null,
          event_tickets: null,
          event_show_more: null,
          event_show_less: null,
          congrats_total_amount: null,
          congrats_payment: null,
          your_name_colon: null,
          email_address_colon: null,
          phone_number_colon: null,
          add_to_calendar: null
        }
      },
      footer: {
        name: globalLabels.footer_buttons,
        labels: {
          finish_appointment: null,
          congrats_panel: null
        }
      }
    }
  }
}

export { defaultCustomizeSettings, defaultTranslations }
