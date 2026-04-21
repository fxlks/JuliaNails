const standardEvents = [
  'addpaymentinfo', 'addtocart', 'addtowishlist',
  'completeregistration', 'contact', 'customizeproduct',
  'donate',
  'findlocation',
  'initiatecheckout',
  'lead',
  'pageview', 'purchase',
  'schedule', 'search', 'starttrial', 'submitapplication', 'subscribe',
  'viewcontent'
]

function install () {
  if (typeof window.fbq === 'undefined') {
    !(function(f, b, e, v, n, t, s) {
      if (f.fbq) return;
      n = f.fbq = function() {
        n.callMethod
          ? n.callMethod.apply(n, arguments)
          : n.queue.push(arguments);
      };
      if (!f._fbq) f._fbq = n;
      n.push = n;
      n.loaded = !0;
      n.version = '2.0';
      n.queue = [];
      t = b.createElement(e);
      t.async = !0;
      t.src = v;
      s = b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t, s);
    })(
      window,
      document,
      'script',
      'https://connect.facebook.net/en_US/fbevents.js',
    );
  }
}

function init (appId) {
  window.fbq('init', appId, {})
}

function event(name, data) {
  window.fbq.apply(
    window,
    [
      !standardEvents.includes(name.toLowerCase()) ? 'trackCustom' : 'track',
      name,
      data
    ]
  )
}

export {
  install,
  init,
  event,
}
