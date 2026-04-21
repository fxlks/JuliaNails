function useResponsiveClass (width) {
  if (width === undefined || width === 0) return ''
  // rw - responsive width
  if (width <= 320) return 'am-rw-768 am-rw-740 am-rw-720 am-rw-700 am-rw-680 am-rw-660 am-rw-650 am-rw-640 am-rw-620 am-rw-600 am-rw-580 am-rw-560 am-rw-540 am-rw-520 am-rw-500 am-rw-480 am-rw-460 am-rw-440 am-rw-420 am-rw-400 am-rw-380 am-rw-370 am-rw-360 am-rw-340 am-rw-320'
  if (width <= 340) return 'am-rw-768 am-rw-740 am-rw-720 am-rw-700 am-rw-680 am-rw-660 am-rw-650 am-rw-640 am-rw-620 am-rw-600 am-rw-580 am-rw-560 am-rw-540 am-rw-520 am-rw-500 am-rw-480 am-rw-460 am-rw-440 am-rw-420 am-rw-400 am-rw-380 am-rw-370 am-rw-360 am-rw-340'
  if (width <= 360) return 'am-rw-768 am-rw-740 am-rw-720 am-rw-700 am-rw-680 am-rw-660 am-rw-650 am-rw-640 am-rw-620 am-rw-600 am-rw-580 am-rw-560 am-rw-540 am-rw-520 am-rw-500 am-rw-480 am-rw-460 am-rw-440 am-rw-420 am-rw-400 am-rw-380 am-rw-370 am-rw-360'
  if (width <= 370) return 'am-rw-768 am-rw-740 am-rw-720 am-rw-700 am-rw-680 am-rw-660 am-rw-650 am-rw-640 am-rw-620 am-rw-600 am-rw-580 am-rw-560 am-rw-540 am-rw-520 am-rw-500 am-rw-480 am-rw-460 am-rw-440 am-rw-420 am-rw-400 am-rw-380 am-rw-370'
  if (width <= 380) return 'am-rw-768 am-rw-740 am-rw-720 am-rw-700 am-rw-680 am-rw-660 am-rw-650 am-rw-640 am-rw-620 am-rw-600 am-rw-580 am-rw-560 am-rw-540 am-rw-520 am-rw-500 am-rw-480 am-rw-460 am-rw-440 am-rw-420 am-rw-400 am-rw-380'
  if (width <= 400) return 'am-rw-768 am-rw-740 am-rw-720 am-rw-700 am-rw-680 am-rw-660 am-rw-650 am-rw-640 am-rw-620 am-rw-600 am-rw-580 am-rw-560 am-rw-540 am-rw-520 am-rw-500 am-rw-480 am-rw-460 am-rw-440 am-rw-420 am-rw-400'
  if (width <= 420) return 'am-rw-768 am-rw-740 am-rw-720 am-rw-700 am-rw-680 am-rw-660 am-rw-650 am-rw-640 am-rw-620 am-rw-600 am-rw-580 am-rw-560 am-rw-540 am-rw-520 am-rw-500 am-rw-480 am-rw-460 am-rw-440 am-rw-420'
  if (width <= 440) return 'am-rw-768 am-rw-740 am-rw-720 am-rw-700 am-rw-680 am-rw-660 am-rw-650 am-rw-640 am-rw-620 am-rw-600 am-rw-580 am-rw-560 am-rw-540 am-rw-520 am-rw-500 am-rw-480 am-rw-460 am-rw-440'
  if (width <= 460) return 'am-rw-768 am-rw-740 am-rw-720 am-rw-700 am-rw-680 am-rw-660 am-rw-650 am-rw-640 am-rw-620 am-rw-600 am-rw-580 am-rw-560 am-rw-540 am-rw-520 am-rw-500 am-rw-480 am-rw-460'
  if (width <= 480) return 'am-rw-768 am-rw-740 am-rw-720 am-rw-700 am-rw-680 am-rw-660 am-rw-650 am-rw-640 am-rw-620 am-rw-600 am-rw-580 am-rw-560 am-rw-540 am-rw-520 am-rw-500 am-rw-480'
  if (width <= 500) return 'am-rw-768 am-rw-740 am-rw-720 am-rw-700 am-rw-680 am-rw-660 am-rw-650 am-rw-640 am-rw-620 am-rw-600 am-rw-580 am-rw-560 am-rw-540 am-rw-520 am-rw-500'
  if (width <= 520) return 'am-rw-768 am-rw-740 am-rw-720 am-rw-700 am-rw-680 am-rw-660 am-rw-650 am-rw-640 am-rw-620 am-rw-600 am-rw-580 am-rw-560 am-rw-540 am-rw-520'
  if (width <= 540) return 'am-rw-768 am-rw-740 am-rw-720 am-rw-700 am-rw-680 am-rw-660 am-rw-650 am-rw-640 am-rw-620 am-rw-600 am-rw-580 am-rw-560 am-rw-540'
  if (width <= 560) return 'am-rw-768 am-rw-740 am-rw-720 am-rw-700 am-rw-680 am-rw-660 am-rw-650 am-rw-640 am-rw-620 am-rw-600 am-rw-580 am-rw-560'
  if (width <= 580) return 'am-rw-768 am-rw-740 am-rw-720 am-rw-700 am-rw-680 am-rw-660 am-rw-650 am-rw-640 am-rw-620 am-rw-600 am-rw-580'
  if (width <= 600) return 'am-rw-768 am-rw-740 am-rw-720 am-rw-700 am-rw-680 am-rw-660 am-rw-650 am-rw-640 am-rw-620 am-rw-600'
  if (width <= 620) return 'am-rw-768 am-rw-740 am-rw-720 am-rw-700 am-rw-680 am-rw-660 am-rw-650 am-rw-640 am-rw-620'
  if (width <= 640) return 'am-rw-768 am-rw-740 am-rw-720 am-rw-700 am-rw-680 am-rw-660 am-rw-650 am-rw-640'
  if (width <= 650) return 'am-rw-768 am-rw-740 am-rw-720 am-rw-700 am-rw-680 am-rw-660 am-rw-650'
  if (width <= 660) return 'am-rw-768 am-rw-740 am-rw-720 am-rw-700 am-rw-680 am-rw-660'
  if (width <= 680) return 'am-rw-768 am-rw-740 am-rw-720 am-rw-700 am-rw-680'
  if (width <= 700) return 'am-rw-768 am-rw-740 am-rw-720 am-rw-700'
  if (width <= 720) return 'am-rw-768 am-rw-740 am-rw-720'
  if (width <= 740) return 'am-rw-768 am-rw-740'
  if (width <= 768) return 'am-rw-768'
  return ''
}

export {
  useResponsiveClass
}