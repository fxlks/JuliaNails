import httpClient from "../../../plugins/axios";
import {reactive} from "vue";
import {useCart} from "./cart";

const globalLabels = reactive(window.wpAmeliaLabels)

async function validateCoupon(store, callback) {
    let type = store.getters['bookableType/getType'] ? 'event' : store.getters['booking/getBookableType']
    let module = type === 'event' ? 'coupon' : 'booking'
    let customerModule = type === 'event' ? 'customerInfo' : 'booking'
    let coupon = store.getters[`${module}/getCoupon`]
    if (coupon.code) {
        try {
            const response = await httpClient.post(
                '/coupons/validate',
                {
                    code: coupon.code,
                    id: getCouponEntityIds(store),
                    type: type,
                    user: {
                        firstName: store.getters[`${customerModule}/getCustomerFirstName`],
                        lastName: store.getters[`${customerModule}/getCustomerLastName`],
                        email: store.getters[`${customerModule}/getCustomerEmail`],
                    },
                    count: coupon.bookingsCount ? coupon.bookingsCount : 1
                }
            )

            store.commit(`${module}/setCoupon`, {
                code: response.data.data.coupon.code,
                deduction: response.data.data.coupon.deduction,
                discount: response.data.data.coupon.discount,
                limit: response.data.data.limit,
                required: coupon.required,
                bookingsCount: coupon.bookingsCount,
                servicesIds: response.data.data.coupon.serviceList.map(i => i.id),
            })

            callback()

        } catch (e) {
            store.commit(`${module}/setCoupon`, {
                code: coupon.code,
                deduction: 0,
                discount: 0,
                limit: 0,
                required: coupon.required,
                bookingsCount: coupon.bookingsCount,
                servicesIds: [],
            })

            let message = e.response.data.message

            if ('couponUnknown' in e.response.data.data && e.response.data.data.couponUnknown === true) {
                message = globalLabels.coupon_unknown
            } else if ('couponInvalid' in e.response.data.data && e.response.data.data.couponInvalid === true) {
                message = globalLabels.coupon_invalid
            } else if ('couponMissing' in e.response.data.data && e.response.data.data.couponMissing === true) {
                message = globalLabels.coupon_missing
            } else if ('emailError' in e.response.data.data && e.response.data.data.emailError === true) {
                message = globalLabels.email_exist_error
            } else if ('phoneError' in e.response.data.data && e.response.data.data.phoneError === true) {
                message = globalLabels.phone_exist_error
            }

            store.commit(`${module}/setError`, message)

            callback()
        }
    } else {
        store.commit(`${module}/setCoupon`, {
            code: '',
            deduction: 0,
            discount: 0,
            limit: 0,
            required: coupon.required,
            bookingsCount: coupon.bookingsCount
        })

    }
}

function getCouponEntityIds (store) {
    let cart = useCart(store)
    let type = cart.length > 1 ? 'cart' : (store.getters['bookableType/getType'] ? 'event' : store.getters['booking/getBookableType'])
    let ids = []
    switch (type) {
        case 'event':
            ids = store.getters['eventBooking/getSelectedEventId']
            break
        case 'cart':
            ids = useCart(store).filter(i => i.serviceId && (i.serviceId in i.services)).map(i => i.serviceId)
            ids = [...new Set(ids)].join(',')
            break
        case 'appointment': case 'package':
            ids = store.getters['entities/getBookableFromBookableEntities'](
                store.getters['booking/getSelection']
            ).id
            break
    }
    return ids
}

export {
    validateCoupon
}
