import moment from "moment";

function useFrontendCustomer (store, customer) {
  return Object.assign(
    {},
    customer,
    {
      gender: customer.gender ? customer.gender : '',
      note: customer.note ? customer.note : '',
      phone: customer.phone ? customer.phone : '',
      birthday: customer.birthday ? moment(customer.birthday.date.split(' ')[0]).toDate() : null,
      translations: customer.translations ? JSON.parse(customer.translations) : {defaultLanguage: ''}
    }
  )
}

function useBackendCustomer (store) {
  let customer = store.getters['customerInfo/getCustomer']

  return Object.assign(
    {},
    customer,
    {
      birthday: customer.birthday ? moment(customer.birthday).format('YYYY-MM-DD') : null,
      translations: customer.translations.defaultLanguage ? JSON.stringify(customer.translations) : null,
    }
  )
}

export {
  useFrontendCustomer,
  useBackendCustomer,
}
