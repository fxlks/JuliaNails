
function useScrollTo(elementContainer, elem, fromTop, duration, nested = {
  inCollapse: false,
}) {
  let start = elementContainer.scrollTop,
    containerTop = elementContainer.offsetTop,
    to = elem.offsetTop,
    change = to - (start + containerTop + fromTop),
    currentTime = 0,
    increment = 20;

  if (nested.inCollapse) {
    change = (to + elem.offsetParent.offsetTop + elem.offsetParent.offsetParent.offsetTop + elem.offsetParent.offsetParent.offsetParent.offsetTop) - (start + containerTop + fromTop)
  }

  let animateScroll = function(){
    currentTime += increment;
    elementContainer.scrollTop = Math.easeInOutQuad(currentTime, start, change, duration);
    if(currentTime < duration) {
      setTimeout(animateScroll, increment);
    }
  };
  animateScroll();
}

//t = current time
//b = start value
//c = change in value
//d = duration
Math.easeInOutQuad = function (t, b, c, d) {
  t /= d/2;
  if (t < 1) return c/2*t*t + b;
  t--;
  return -c/2 * (t*(t-2) - 1) + b;
};

export { useScrollTo }