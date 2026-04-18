/**
 * Conversion Outline for SSA Booking Module
 * 
 * This file defines how Divi 4 module attributes are converted to Divi 5 structure.
 * It maps the old shortcode attributes to the new module.json structure.
 * 
 * @package Simply_Schedule_Appointments
 * @since 6.9.21
 */

export const conversionOutline = {
  // Map Divi 4 advanced fields to Divi 5 structure
  advanced: {
    admin_label:     'module.meta.adminLabel',
    animation:       'module.decoration.animation',
    background:      'module.decoration.background',
    disabled_on:     'module.decoration.disabledOn',
    module:          'module.advanced.htmlAttributes',
    overflow:        'module.decoration.overflow',
    position_fields: 'module.decoration.position',
    scroll:          'module.decoration.scroll',
    sticky:          'module.decoration.sticky',
    text:            'module.advanced.text',
    transform:       'module.decoration.transform',
    transition:      'module.decoration.transition',
    z_index:         'module.decoration.zIndex',
    margin_padding:  'module.decoration.spacing',
    max_width:       'module.decoration.sizing',
    height:          'module.decoration.sizing',
    link_options:    'module.advanced.link',
    text_shadow: {
      default: 'module.advanced.text.textShadow',
    },
    box_shadow: {
      default: 'module.decoration.boxShadow',
    },
    borders: {
      default: 'module.decoration.border',
    },
    filters: {
      default: 'module.decoration.filters',
    },
  },

  // Map custom CSS to Divi 5 structure
  css: {
    after:        'css.*.after',
    before:       'css.*.before',
    main_element: 'css.*.mainElement',
  },

  // Map SSA-specific module attributes
  module: {
    // Map Divi 4 'appointment_type' field to Divi 5 structure
    appointment_type: 'module.innerContent.appointmentType',
    
    // Map color fields (these were custom fields in Divi 4)
    accent_color:     'module.advanced.customColors.accentColor',
    background_color: 'module.advanced.customColors.backgroundColor',
    
    // Map font family field
    font_family:      'module.advanced.typography.fontFamily',
    
    // Map padding fields
    padding:          'module.decoration.spacing.padding',
    padding_css_unit: 'module.decoration.spacing.paddingUnit',
  },
};
