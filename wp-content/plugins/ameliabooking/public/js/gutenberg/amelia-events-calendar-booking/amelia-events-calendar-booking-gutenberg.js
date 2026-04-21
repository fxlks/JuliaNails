(function (wp) {
  var wpAmeliaLabels = 'wpAmeliaLabels' in window ? window.wpAmeliaLabels : {data: {}}

  var el = wp.element.createElement
  var components = wp.components
  var blockControls = wp.blockEditor.BlockControls
  var inspectorControls = wp.blockEditor.InspectorControls
  var useBlockProps = wp.blockEditor.useBlockProps
  var data = wpAmeliaLabels.data

  var entityNames = ['events', 'tags', 'locations']
  var entities = {}

  entityNames.forEach((entityName) => {
    entities[entityName] = []
    if (data[entityName].length !== 0) {
      for (let i = 0; i < data[entityName].length; i++) {
        entities[entityName].push({
          value: data[entityName][i].id,
          text: data[entityName][i].name + (entityName !== 'tags'
            ? ' (id: ' + data[entityName][i].id + ')' + (data[entityName][i].formattedPeriodStart ? (' - ' + data[entityName][i].formattedPeriodStart) : '')
            : '')
        })
      }
    }
  })

  // Registering the Block for events shortcode
  wp.blocks.registerBlockType('amelia/events-calendar-booking-gutenberg-block', {
    apiVersion: 3,
    title: wpAmeliaLabels.events_calendar_booking_gutenberg_block.title,
    description: wpAmeliaLabels.events_calendar_booking_gutenberg_block.description,
    icon: window.ameliaBlockIcon,
    category: 'amelia-blocks',
    keywords: [
      'amelia',
      'events'
    ],
    supports: {
      customClassName: false,
      html: false
    },
    attributes: {
      short_code: {
        type: 'string',
        default: '[ameliaeventscalendarbooking]'
      },
      trigger: {
        type: 'string',
        default: ''
      },
      trigger_type: {
        type: 'string',
        default: 'id'
      },
      in_dialog: {
        type: 'boolean',
        default: false
      },
      event: {
        type: 'array',
        default: []
      },
      recurring: {
        type: 'boolean',
        default: false
      },
      tag: {
        type: 'array',
        default: []
      },
      location: {
        type: 'array',
        default: []
      },
      eventOptions: {
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

      var options = {
        events: [{value: '', label: wpAmeliaLabels.show_all_events}],
        tags: [{value: '', label: wpAmeliaLabels.show_all_tags}],
        locations: [{value: '', label: wpAmeliaLabels.show_all_locations}],
        eventOptions: [
          {value: 'events', label: wpAmeliaLabels.show_event},
          {value: 'tags', label: wpAmeliaLabels.show_tag}
        ],
        trigger_type: [
          {value: 'id', label: wpAmeliaLabels.trigger_type_id},
          {value: 'class', label: wpAmeliaLabels.trigger_type_class}
        ]
      }

      function getOptions (data) {
        var options = []
        data = Object.keys(data).map(function (key) {
          return data[key]
        })

        data.sort(function (a, b) {
          if (parseInt(a.value) < parseInt(b.value)) {
            return -1
          }

          if (parseInt(a.value) > parseInt(b.value)) {
            return 1
          }

          return 0
        })

        data.forEach(function (element) {
          options.push({value: element.value, label: element.text})
        })

        return options
      }

      Object.keys(entities).forEach(entity => {
        getOptions(entities[entity])
          .forEach(function (element) {
            options[entity].push(element)
          })
      })

      function getShortCode (props, attributes) {
        let shortCodeString = ''
        let shortCode = ''

        if (entities.events.length !== 0) {
          if (attributes.event !== '' && attributes.event.length && attributes.event[0] !== '') {
            shortCodeString += ' event=' + attributes.event + ''

            if (attributes.recurring) {
              shortCodeString += ' recurring=1'
            }
          }

          if (entities.tags.length !== 0) {
            if (attributes.tag && attributes.tag.length && attributes.tag[0] !== '') {
              shortCodeString += ' tag="'
              attributes.tag.forEach((tag, index) => {
                if (tag) {
                  shortCodeString += (index === 0 ? '' : ',') + '{' + tag + '}'
                }
              })
              shortCodeString += '"'
            }
          }

          if (entities.locations.length !== 0) {
            if (attributes.location && attributes.location.length && attributes.location[0] !== '') {
              shortCodeString += ' location=' + attributes.location + ''
            }
          }

          shortCode += '[ameliaeventscalendarbooking' + shortCodeString

          if (attributes.trigger) {
            shortCode += ' trigger=' + attributes.trigger + ''
          }

          if (attributes.trigger && attributes.trigger_type) {
            shortCode += ' trigger_type=' + attributes.trigger_type + ''
          }

          if (attributes.trigger && attributes.in_dialog) {
            shortCode += ' in_dialog=1'
          }

          shortCode += ']'
        } else {
          shortCode = 'Notice: Please create event first.'
        }

        props.setAttributes({short_code: shortCode})

        return shortCode
      }

      var blockProps = useBlockProps()

      if (entities.events.length !== 0) {
        inspectorElements.push(el(components.PanelRow,
          {},
          el('label', {htmlFor: 'amelia-js-parametars'}, wpAmeliaLabels.filter),
          el(components.FormToggle, {
            id: 'amelia-js-parametars',
            checked: attributes.parametars,
            onChange: function () {
              return props.setAttributes({parametars: !props.attributes.parametars})
            }
          })
        ))

        inspectorElements.push(el('div', {style: {marginBottom: '1em'}}, ''))

        if (attributes.parametars) {
          inspectorElements.push(el('div', {className: 'amelia-gutenberg-multi-select-note'}, wpAmeliaLabels.multiselect_note))

          if (entities.tags.length) {
            inspectorElements.push(el(components.SelectControl, {
              id: 'amelia-js-select-tag',
              className: 'amelia-gutenberg-multi-select',
              label: wpAmeliaLabels.select_tag,
              value: attributes.tag,
              options: options.tags,
              multiple: true,
              onChange: function (selectControl) {
                return props.setAttributes({tag: selectControl})
              }
            }))
          }

          if (entities.events.length) {
            inspectorElements.push(el(components.SelectControl, {
              id: 'amelia-js-select-event',
              className: 'amelia-gutenberg-multi-select',
              label: wpAmeliaLabels.select_event,
              value: attributes.event,
              options: options.events,
              multiple: true,
              onChange: function (selectControl) {
                return props.setAttributes({event: selectControl})
              }
            }))

            inspectorElements.push(el('div', {style: {marginBottom: '1em'}}, ''))

            inspectorElements.push(el(components.PanelRow,
              {},
              el('label', {htmlFor: 'amelia-js-today'}, wpAmeliaLabels.recurring_event),
              el(components.FormToggle, {
                id: 'amelia-js-recurring',
                checked: attributes.recurring,
                onChange: function () {
                  return props.setAttributes({recurring: !props.attributes.recurring})
                }
              })
            ))
          }

          if (entities.locations.length) {
            inspectorElements.push(el(components.SelectControl, {
              id: 'amelia-js-select-location',
              className: 'amelia-gutenberg-multi-select',
              label: wpAmeliaLabels.select_location,
              value: attributes.location,
              options: options.locations,
              multiple: true,
              onChange: function (selectControl) {
                return props.setAttributes({location: selectControl})
              }
            }))
          }

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

          inspectorElements.push(el(components.SelectControl, {
            id: 'amelia-js-trigger_type',
            label: wpAmeliaLabels.trigger_type,
            value: attributes.trigger_type,
            options: options.trigger_type,
            help: wpAmeliaLabels.trigger_type_tooltip,
            onChange: function (selectControl) {
              return props.setAttributes({trigger_type: selectControl})
            }
          }))

          inspectorElements.push(el(components.PanelRow,
            {},
            el('label', {htmlFor: 'amelia-js-in-dialog'}, wpAmeliaLabels.in_dialog),
            el(components.FormToggle, {
              id: 'amelia-js-in-dialog',
              checked: attributes.in_dialog,
              onChange: function () {
                return props.setAttributes({in_dialog: !props.attributes.in_dialog})
              }
            })
          ))
        } else {
          attributes.event = ''
          attributes.tag = ''
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
              el('div', {className: 'amelia-gutenberg-placeholder__title'}, 'Amelia - Events Calendar Booking')
            ),
            el('div', {className: 'amelia-gutenberg-placeholder__shortcode'},
              getShortCode(props, props.attributes)
            )
          )
        )
      } else {
        inspectorElements.push(el('p', {style: {marginBottom: '1em'}}, 'Please create event first. You can find instructions in our documentation on link below.'))
        inspectorElements.push(el('a', {href: 'https://wpamelia.com/documentation/service-quick-start/', target: '_blank', style: {marginBottom: '1em'}}, 'Start working with Amelia WordPress Appointment Booking plugin'))

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
              el('div', {className: 'amelia-gutenberg-placeholder__title'}, 'Amelia - Events Calendar Booking')
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
          short_code: {type: 'string', default: '[ameliaeventscalendarbooking]'},
          trigger: {type: 'string', default: ''},
          trigger_type: {type: 'string', default: 'id'},
          in_dialog: {type: 'boolean', default: false},
          event: {type: 'array', default: []},
          recurring: {type: 'boolean', default: false},
          tag: {type: 'array', default: []},
          location: {type: 'array', default: []},
          eventOptions: {type: 'string', default: ''},
          parametars: {type: 'boolean', default: false}
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
