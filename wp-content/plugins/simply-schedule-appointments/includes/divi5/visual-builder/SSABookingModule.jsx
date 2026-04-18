/**
 * SSA Booking Module - Divi 5 Visual Builder Component
 *
 * @package Simply_Schedule_Appointments
 * @since 5.9.0
 */

const React = window.React;
const { Component } = React;
const { ModuleContainer } = window?.divi?.module || {};

/**
 * Internal class component for managing state
 */
class SSABookingRenderer extends Component {
  constructor(props) {
    super(props);
    this.state = {
      bookingHTML: null,
      isLoading: true,
    };
  }

  componentDidMount() {
    this.fetchBookingForm();
  }

  componentDidUpdate(prevProps) {
    const prevType = prevProps?.appointmentType || '';
    const currentType = this.props?.appointmentType || '';
    
    if (prevType !== currentType) {
      this.fetchBookingForm();
    }
  }

  async fetchBookingForm() {
    this.setState({ isLoading: true });
    
    const appointmentType = this.props.appointmentType || '';
    const shortcodeAttr = appointmentType ? `type="${appointmentType}"` : '';
    const shortcode = `[ssa_booking ${shortcodeAttr}]`;
    
    try {
      const response = await fetch(`${window.wpApiSettings?.root || '/wp-json/'}ssa/v1/render-shortcode`, {
        method: 'POST',
        headers: {
          'X-WP-Nonce': window.wpApiSettings?.nonce || '',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ shortcode }),
      });
      
      const data = await response.json();
      this.setState({ 
        bookingHTML: data.html || data,
        isLoading: false 
      });
    } catch (err) {
      this.setState({ 
        bookingHTML: '<div style="padding: 20px; color: red;">Error loading booking form</div>',
        isLoading: false 
      });
    }
  }

  render() {
    const { bookingHTML, isLoading } = this.state;
    
    if (isLoading) {
      return React.createElement('div', {
        style: { padding: '40px', textAlign: 'center', color: '#666' }
      }, 'Loading booking form...');
    }
    
    return React.createElement('div', {
      dangerouslySetInnerHTML: { __html: bookingHTML }
    });
  }
}

/**
 * SSA Booking Module Edit Component - Arrow function wrapper
 */
const SSABookingModuleEdit = (props) => {
  const { attrs, elements, id, name } = props;
  const appointmentType = attrs?.module?.innerContent?.appointmentType?.desktop?.value || '';

  return React.createElement(ModuleContainer, {
    attrs: attrs,
    elements: elements,
    id: id,
    name: name,
  },
    elements?.styleComponents({ attrName: 'module' }),
    React.createElement(SSABookingRenderer, { appointmentType })
  );
};

export default SSABookingModuleEdit;
