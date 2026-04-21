(function (wp) {

  var el = wp.element.createElement
  var components = wp.components
  var blockControls = wp.blockEditor.BlockControls
  var inspectorControls = wp.blockEditor.InspectorControls
  var useBlockProps = wp.blockEditor.useBlockProps
  var data = wpAmeliaLabels.data

  var categories = []
  var services = []
  var employees = []
  var locations = []
  var packages = []

  if (data.categories.length !== 0) {
    for (let i = 0; i < data.categories.length; i++) {
      categories.push({
        value: data.categories[i].id,
        text: data.categories[i].name + ' (id: ' + data.categories[i].id + ')'
      })
    }
  } else {
    categories = []
  }

  if (data.servicesList.length !== 0) {
    // Create array of services objects
    for (let i = 0; i < data.servicesList.length; i++) {
      if (data.servicesList[i].length !== 0) {
        services.push({
          value: data.servicesList[i].id,
          text: data.servicesList[i].name + ' (id: ' + data.servicesList[i].id + ')'
        })
      }
    }
  } else {
    services = []
  }

  if (data.employees.length !== 0) {
    // Create array of employees objects
    for (let i = 0; i < data.employees.length; i++) {
      employees.push({
        value: data.employees[i].id,
        text: data.employees[i].firstName + ' ' + data.employees[i].lastName + ' (id: ' + data.employees[i].id + ')'
      })
    }
  } else {
    employees = []
  }

  if (data.locations.length !== 0) {
    // Create array of locations objects
    for (let i = 0; i < data.locations.length; i++) {
      locations.push({
        value: data.locations[i].id,
        text: data.locations[i].name + ' (id: ' + data.locations[i].id + ')'
      })
    }
  } else {
    locations = []
  }

  if (data.packages.length !== 0) {
    // Create array of packages objects
    for (let i = 0; i < data.packages.length; i++) {
      packages.push({
        value: data.packages[i].id,
        text: data.packages[i].name + ' (id: ' + data.packages[i].id + ')'
      })
    }
  } else {
    packages = []
  }

  // Registering the Block for booking shotcode
  wp.blocks.registerBlockType('amelia/booking-gutenberg-block', {
    apiVersion: 3,
    title: wpAmeliaLabels.booking_gutenberg_block.title,
    description: el('div', {className: 'amelia-gutenberg-desc'}, wpAmeliaLabels.booking_gutenberg_block.description,
      el('div', {className: 'amelia-gutenberg-outdated'}, wpAmeliaLabels.outdated_booking_gutenberg_block)
    ),
    icon: window.ameliaBlockIcon,
    category: 'amelia-blocks',
    keywords: [
      'amelia',
      'booking'
    ],
    supports: {
      customClassName: false,
      html: false,
      inserter: false
    },
    attributes: {
      short_code: {
        type: 'string',
        default: '[ameliabooking]'
      },
      trigger: {
        type: 'string',
        default: ''
      },
      show: {
        type: 'string',
        default: ''
      },
      location: {
        type: 'string',
        default: ''
      },
      category: {
        type: 'string',
        default: ''
      },
      service: {
        type: 'string',
        default: ''
      },
      employee: {
        type: 'string',
        default: ''
      },
      parametars: {
        type: 'boolean',
        default: false
      }
    },
    edit: function (props) {
      var inspectorElements = []
      var attributes = props.attributes
      var options = []

      options['categories'] = [{value: '', label: wpAmeliaLabels.show_all_categories}]
      options['services'] = [{value: '', label: wpAmeliaLabels.show_all_services}]
      options['employees'] = [{value: '', label: wpAmeliaLabels.show_all_employees}]
      options['locations'] = [{value: '', label: wpAmeliaLabels.show_all_locations}]
      options['show'] = [{value: '', label: wpAmeliaLabels.show_all}, {value: 'services', label: wpAmeliaLabels.services}, {value: 'packages', label: wpAmeliaLabels.packages}]

      function getOptions(data) {
        var options = []

        data = Object.keys(data).map(function (key) {
          return data[key]
        })

        data.sort(function (a, b) {
          if (parseInt(a.pos) < parseInt(b.pos)) return -1
          if (parseInt(a.pos) > parseInt(b.pos)) return 1
          return 0
        })

        data.forEach(function (element) {
          options.push({value: element.value, label: element.text})
        })

        return options
      }

      getOptions(categories)
      .forEach(function (element) {
        options['categories'].push(element)
      })

      getOptions(services)
      .forEach(function (element) {
        options['services'].push(element)
      })

      getOptions(employees)
      .forEach(function (element) {
        options['employees'].push(element)
      })

      if (locations.length) {
        getOptions(locations)
        .forEach(function (element) {
          options['locations'].push(element)
        })
      }

      function getShortCode(props, attributes) {
        var shortCode = ''
        if (categories.length !== 0 && services.length !== 0 && employees.length !== 0) {
          if (attributes.parametars) {
            shortCode = '[ameliabooking'

            if (attributes.show) {
              shortCode += ' show=' + attributes.show + ''
            }

            if (attributes.trigger) {
              shortCode += ' trigger=' + attributes.trigger + ''
            }

            if (attributes.service) {
              shortCode += ' service=' + attributes.service + ''
            } else if (attributes.category) {
              shortCode += ' category=' + attributes.category + ''
            }

            if (attributes.employee) {
              shortCode += ' employee=' + attributes.employee + ''
            } else if (attributes.location) {
              shortCode += ' location=' + attributes.location + ''
            }
            shortCode += ']'
          } else {
            shortCode = '[ameliabooking]'
          }
        } else {
          shortCode = "Notice: Please create category, service and employee first."
        }

        props.setAttributes({short_code: shortCode})

        return shortCode
      }

      var blockProps = useBlockProps()

      if (categories.length !== 0 && services.length !== 0 && employees.length !== 0) {

        inspectorElements.push(el(components.PanelRow,
          {},
          el('label', {htmlFor: 'amelia-js-parametars'}, wpAmeliaLabels.filter),
          el(components.FormToggle, {
            id: 'amelia-js-parametars',
            checked: attributes.parametars,
            onChange: function () {
              return props.setAttributes({parametars: !props.attributes.parametars})
            },
          })
        ))

        inspectorElements.push(el('div', {style: {marginBottom: '1em'}}, ''))

        if (attributes.parametars) {

          inspectorElements.push(el(components.SelectControl, {
            id: 'amelia-js-select-category',
            label: wpAmeliaLabels.select_category,
            value: attributes.category,
            options: options.categories,
            onChange: function (selectControl) {
              return props.setAttributes({category: selectControl})
            }
          }))

          inspectorElements.push(el('div', {style: {marginBottom: '1em'}}, ''))

          inspectorElements.push(el(components.SelectControl, {
            id: 'amelia-js-select-service',
            label: wpAmeliaLabels.select_service,
            value: attributes.service,
            options: options.services,
            onChange: function (selectControl) {
              return props.setAttributes({service: selectControl})
            }
          }))

          inspectorElements.push(el('div', {style: {marginBottom: '1em'}}, ''))

          inspectorElements.push(el(components.SelectControl, {
            id: 'amelia-js-select-employee',
            label: wpAmeliaLabels.select_employee,
            value: attributes.employee,
            options: options.employees,
            onChange: function (selectControl) {
              return props.setAttributes({employee: selectControl})
            }
          }))

          inspectorElements.push(el('div', {style: {marginBottom: '1em'}}, ''))

          inspectorElements.push(el(components.SelectControl, {
            id: 'amelia-js-select-location',
            label: wpAmeliaLabels.select_location,
            value: attributes.location,
            options: options.locations,
            onChange: function (selectControl) {
              return props.setAttributes({location: selectControl})
            }
          }))

          inspectorElements.push(el('div', {style: {marginBottom: '1em'}}, ''))

          inspectorElements.push(el(components.TextControl, {
            id: 'amelia-js-trigger',
            label: wpAmeliaLabels.manually_loading,
            value: attributes.trigger,
            help: wpAmeliaLabels.manually_loading_description,
            onChange: function (TextControl) {
              return props.setAttributes({trigger: TextControl})
            }
          }))

          if (packages.length) {
            inspectorElements.push(el('div', {style: {marginBottom: '1em'}}, ''))

            inspectorElements.push(el(components.SelectControl, {
              id: 'amelia-js-select-type',
              label: wpAmeliaLabels.show_all,
              value: attributes.show,
              options: options.show,
              onChange: function (selectControl) {
                return props.setAttributes({show: selectControl})
              }
            }))
          }
        }

        return el('div', blockProps,
          el(blockControls, {key: 'controls'}),
          el(inspectorControls, {key: 'inspector'},
            el(components.PanelBody, {initialOpen: true},
              inspectorElements
            )
          ),
          el('div', {className: 'amelia-gutenberg-placeholder'},
            el('div', {className: 'amelia-gutenberg-placeholder__header'},
              el('div', {className: 'amelia-gutenberg-placeholder__icon'}, window.ameliaBlockIcon || ''),
              el('div', {className: 'amelia-gutenberg-placeholder__title'}, 'Amelia - Booking (Legacy)')
            ),
            el('div', {className: 'amelia-gutenberg-placeholder__shortcode'},
              getShortCode(props, props.attributes)
            )
          )
        )

      } else {
        inspectorElements.push(el('p', {style: {marginBottom: '1em'}}, 'Please create category, services and employee first. You can find instructions in our documentation on link below.'));
        inspectorElements.push(el('a', {href: 'https://wpamelia.com/documentation/service-quick-start/', target: '_blank', style: {marginBottom: '1em'}}, 'Start working with Amelia WordPress Appointment Booking plugin'));

        return el('div', blockProps,
          el(blockControls, {key: 'controls'}),
          el(inspectorControls, {key: 'inspector'},
            el(components.PanelBody, {initialOpen: true},
              inspectorElements
            )
          ),
          el('div', {className: 'amelia-gutenberg-placeholder'},
            el('div', {className: 'amelia-gutenberg-placeholder__header'},
              el('div', {className: 'amelia-gutenberg-placeholder__icon'}, window.ameliaBlockIcon || ''),
              el('div', {className: 'amelia-gutenberg-placeholder__title'}, 'Amelia - Booking (Legacy)')
            ),
            el('div', {className: 'amelia-gutenberg-placeholder__shortcode'},
              getShortCode(props, props.attributes)
            )
          )
        )
      }

    },

    save: function () {
      return null
    },
    deprecated: [
      {
        attributes: {
          short_code: { type: 'string', default: '[ameliabooking]' },
          trigger: { type: 'string', default: '' },
          show: { type: 'string', default: '' },
          location: { type: 'string', default: '' },
          category: { type: 'string', default: '' },
          service: { type: 'string', default: '' },
          employee: { type: 'string', default: '' },
          parametars: { type: 'boolean', default: false }
        },
        save: function (props) {
          return el('div', {}, props.attributes.short_code)
        }
      }
    ]
  })

})(
  window.wp
)
