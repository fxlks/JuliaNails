import {useAmount} from "../common/pricing";

function usePackageBookingPrice (data) {
  let amountData = useAmount(
    null,
    data.coupon,
    data.tax ? data.tax[0] : null,
    data.price - data.price / 100 * data.discount,
    false,
  )

  return amountData.price - amountData.discount + amountData.tax
}

export {
  usePackageBookingPrice,
}
