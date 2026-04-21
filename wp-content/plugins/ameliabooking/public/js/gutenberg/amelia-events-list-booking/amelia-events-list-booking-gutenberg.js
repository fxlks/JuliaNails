(function (wp) {
  var wpAmeliaLabels = 'wpAmeliaLabels' in window ? window.wpAmeliaLabels : {data: {}}

  var el = wp.element.createElement
  var components = wp.components
  var blockControls = wp.blockEditor.BlockControls
  var inspectorControls = wp.blockEditor.InspectorControls
  var useBlockProps = wp.blockEditor.useBlockProps
  var useEffect = wp.element.useEffect
  var useRef = wp.element.useRef
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
  wp.blocks.registerBlockType('amelia/events-list-booking-gutenberg-block', {
    apiVersion: 3,
    title: wpAmeliaLabels.events_list_booking_gutenberg_block.title,
    description: wpAmeliaLabels.events_list_booking_gutenberg_block.description,
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
        default: '[ameliaeventslistbooking]'
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
      },
      event_to_show: {
        type: 'string',
        default: 'all'
      },
      start_date: {
        type: 'string',
        default: ''
      },
      end_date: {
        type: 'string',
        default: ''
      }
    },
    edit: function (props) {
      var inspectorElements = []
      var attributes = props.attributes
      var startDatePickerRef = useRef(null)
      var endDatePickerRef = useRef(null)

      useEffect(function () {
        var hasCustomRangeControls = attributes.parametars &&
          attributes.event_to_show === 'custom' &&
          (!attributes.event || !attributes.event.length || attributes.event[0] === '')

        if (!hasCustomRangeControls) {
          return undefined
        }

        function applyWeekdayLabelStyles (container) {
          if (!container) {
            return
          }

          var datePickerRoot = container.querySelector('.components-datetime__date')

          if (!datePickerRoot) {
            return
          }

          var calendar = Array.from(datePickerRoot.children)
            .find(function (child) {
              return child.querySelector('button.components-datetime__date__day')
            })

          if (!calendar) {
            return
          }

          Array.from(calendar.children)
            .filter(function (child) {
              return child.tagName === 'DIV'
            })
            .forEach(function (child) {
              child.style.maxWidth = '26px'
              child.style.overflow = 'hidden'
              child.style.textOverflow = 'ellipsis'
              child.style.whiteSpace = 'nowrap'
            })
        }

        function observeDatePicker (container) {
          if (!container) {
            return null
          }

          applyWeekdayLabelStyles(container)

          var observer = new MutationObserver(function () {
            applyWeekdayLabelStyles(container)
          })

          observer.observe(container, {
            childList: true,
            subtree: true
          })

          return observer
        }

        function setupDatePickerObservers () {
          var dateControls = [startDatePickerRef.current, endDatePickerRef.current].filter(Boolean)

          var observers = []
          dateControls.forEach(function (control) {
            var observer = observeDatePicker(control)
            if (observer) {
              observers.push(observer)
            }
          })

          return observers
        }

        // First try immediately
        var observers = setupDatePickerObservers()

        // If not found, watch for DOM changes to detect when elements are added
        if (!observers || observers.length === 0) {

          var domObserver = new MutationObserver(function (mutations) {
            var dateControls = document.querySelectorAll('.amelia-date-control')
            if (dateControls.length > 0) {
              domObserver.disconnect()

              observers = setupDatePickerObservers()
            }
          })

          // Watch the entire document for changes
          domObserver.observe(document.body, {
            childList: true,
            subtree: true
          })

          return function () {
            domObserver.disconnect()
            if (observers) {
              observers.forEach(function (observer) {
                observer.disconnect()
              })
            }
          }
        }

        return function () {
          if (observers) {
            observers.forEach(function (observer) {
              observer.disconnect()
            })
          }
        }
      }, [attributes.event_to_show, attributes.parametars, attributes.event])

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
        ],
        event_to_show: [
          {value: 'all', label: wpAmeliaLabels.all_events},
          {value: 'future', label: wpAmeliaLabels.future_events},
          {value: 'past', label: wpAmeliaLabels.past_events},
          {value: 'custom', label: wpAmeliaLabels.custom_range}
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

          // Add event_to_show range logic (only if no specific event is selected)
          if ((!attributes.event || !attributes.event.length || attributes.event[0] === '') &&
              attributes.event_to_show && attributes.event_to_show !== 'all') {
            if (attributes.event_to_show === 'custom') {
              // Get today's date in Y-m-d format
              const today = new Date()
              const todayFormatted = today.toISOString().split('T')[0]

              const startDate = attributes.start_date || todayFormatted
              const endDate = attributes.end_date || todayFormatted

              shortCodeString += ' range="' + startDate + ' - ' + endDate + '"'
            } else {
              shortCodeString += ' range="' + attributes.event_to_show + '"'
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

          shortCode += '[ameliaeventslistbooking' + shortCodeString

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

          if (!attributes.event.length || attributes.event[0] === '') {
            // Add Event Time Scope dropdown
            inspectorElements.push(el(components.SelectControl, {
              id: 'amelia-js-select-event-to-show',
              label: wpAmeliaLabels.event_time_scope || 'Event Time Scope',
              value: attributes.event_to_show,
              options: options.event_to_show,
              onChange: function (selectControl) {
                return props.setAttributes({event_to_show: selectControl})
              }
            }))

            // Add custom date range fields when 'custom' is selected
            if (attributes.event_to_show === 'custom') {
              inspectorElements.push(el(components.BaseControl,
                {
                  id: 'amelia-js-start-date',
                  className: 'amelia-date-control',
                  label: wpAmeliaLabels.red_start_date || 'Start Date'
                },
                el('div', {ref: startDatePickerRef},
                  el(components.DatePicker, {
                    currentDate: attributes.start_date,
                    onChange: function (date) {
                      // Format date as Y-m-d
                      const formattedDate = date ? date.split('T')[0] : ''
                      return props.setAttributes({
                        start_date: formattedDate,
                        end_date: !formattedDate
                          ? attributes.end_date
                          : (!attributes.end_date || attributes.end_date < formattedDate
                            ? formattedDate
                            : attributes.end_date)
                      })
                    },
                    is12Hour: false
                  })
                )
              ))

              inspectorElements.push(el(components.BaseControl,
                {
                  id: 'amelia-js-end-date',
                  className: 'amelia-date-control',
                  label: wpAmeliaLabels.red_end_date || 'End Date'
                },
                el('div', {ref: endDatePickerRef},
                  el(components.DatePicker, {
                    currentDate: attributes.end_date,
                    onChange: function (date) {
                      // Format date as Y-m-d
                      const formattedDate = date ? date.split('T')[0] : ''
                      return props.setAttributes({
                        start_date: !attributes.start_date && formattedDate
                          ? formattedDate
                          : attributes.start_date,
                        end_date: attributes.start_date && formattedDate && formattedDate < attributes.start_date
                          ? attributes.start_date
                          : formattedDate
                      })
                    },
                    is12Hour: false
                  })
                )
              ))
            }
          }

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
              el('div', {className: 'amelia-gutenberg-placeholder__title'}, 'Amelia - Events List Booking')
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
              el('div', {className: 'amelia-gutenberg-placeholder__title'}, 'Amelia - Events List Booking')
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
          short_code: {type: 'string', default: '[ameliaeventslistbooking]'},
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
