<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Domain\Factory\Bookable\Service;

use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Bookable\Service\PackageCustomer;
use AmeliaBooking\Domain\Factory\Booking\Appointment\AppointmentFactory;
use AmeliaBooking\Domain\Factory\Coupon\CouponFactory;
use AmeliaBooking\Domain\Factory\Payment\PaymentFactory;
use AmeliaBooking\Domain\Factory\User\UserFactory;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Domain\ValueObjects\DateTime\DateTimeValue;
use AmeliaBooking\Domain\ValueObjects\Json;
use AmeliaBooking\Domain\ValueObjects\Number\Float\Price;
use AmeliaBooking\Domain\ValueObjects\Number\Integer\Id;
use AmeliaBooking\Domain\ValueObjects\Number\Integer\IntegerValue;
use AmeliaBooking\Domain\ValueObjects\Number\Integer\WholeNumber;
use AmeliaBooking\Domain\ValueObjects\String\BookingStatus;
use mageekguy\atoum\scripts\treemap\analyzers\size;
use AmeliaBooking\Domain\ValueObjects\String\Token;

/**
 * Class PackageCustomerFactory
 *
 * @package AmeliaBooking\Domain\Factory\Bookable\Service
 */
class PackageCustomerFactory
{
    /**
     * @param $data
     *
     * @return PackageCustomer
     * @throws InvalidArgumentException
     */
    public static function create($data)
    {
        /** @var PackageCustomer $packageCustomer */
        $packageCustomer = new PackageCustomer();

        if (isset($data['id'])) {
            $packageCustomer->setId(new Id($data['id']));
        }

        if (isset($data['packageId'])) {
            $packageCustomer->setPackageId(new Id($data['packageId']));
        }

        if (!empty($data['package'])) {
            $packageCustomer->setPackage(PackageFactory::create($data['package']));
        }

        if (isset($data['customerId'])) {
            $packageCustomer->setCustomerId(new Id($data['customerId']));
        }

        if (isset($data['customer'])) {
            $packageCustomer->setCustomer(UserFactory::create($data['customer']));
        }

        if (isset($data['price'])) {
            $packageCustomer->setPrice(new Price($data['price']));
        }

        $payments = new Collection();
        if (!empty($data['payments'])) {
            /** @var array $paymentsList */
            $paymentsList = $data['payments'];
            foreach ($paymentsList as $paymentKey => $payment) {
                $payments->addItem(PaymentFactory::create($payment), $paymentKey);
            }
        }
        $packageCustomer->setPayments($payments);


        if (!empty($data['end'])) {
            $packageCustomer->setEnd(
                new DateTimeValue(DateTimeService::getCustomDateTimeObject($data['end']))
            );
        }

        if (!empty($data['start'])) {
            $packageCustomer->setStart(
                new DateTimeValue(DateTimeService::getCustomDateTimeObject($data['start']))
            );
        }

        if (!empty($data['purchased'])) {
            $packageCustomer->setPurchased(
                new DateTimeValue(DateTimeService::getCustomDateTimeObject($data['purchased']))
            );
        }

        if (!empty($data['status'])) {
            $packageCustomer->setStatus(
                new BookingStatus($data['status'])
            );
        }

        if (isset($data['bookingsCount'])) {
            $packageCustomer->setBookingsCount(new WholeNumber($data['bookingsCount']));
        }

        if (isset($data['couponId'])) {
            $packageCustomer->setCouponId(new Id($data['couponId']));
        }

        if (isset($data['coupon'])) {
            $packageCustomer->setCoupon(CouponFactory::create($data['coupon']));
        }

        if (!empty($data['tax'])) {
            if (is_string($data['tax'])) {
                $packageCustomer->setTax(new Json($data['tax']));
            } elseif (json_encode($data['tax']) !== false) {
                $packageCustomer->setTax(new Json(json_encode($data['tax'])));
            }
        }

        $packageCustomerServices = new Collection();
        if (!empty($data['packageCustomerServices'])) {
            /** @var array $packageCustomerServicesList */
            $packageCustomerServicesList = $data['packageCustomerServices'];
            foreach ($packageCustomerServicesList as $packageCustomerServiceKey => $packageCustomerService) {
                $packageCustomerServices->addItem(PackageCustomerServiceFactory::create($packageCustomerService), $packageCustomerServiceKey);
            }
        }
        $packageCustomer->setPackageCustomerServices($packageCustomerServices);


        $appointments = new Collection();
        if (!empty($data['appointments'])) {
            $appointmentsList = $data['appointments'];
            foreach ($appointmentsList as $appointmentKey => $appointment) {
                $appointments->addItem(AppointmentFactory::create($appointment), $appointmentKey);
            }
        }
        $packageCustomer->setAppointments($appointments);

        if (isset($data['token'])) {
            $packageCustomer->setToken(new Token($data['token']));
        }

        return $packageCustomer;
    }


    /**
     * @param array $rows
     *
     * @return Collection
     * @throws InvalidArgumentException
     */
    public static function createCollection($rows)
    {
        $packageCustomers = [];

        foreach ($rows as $row) {
            $packageCustomerId = !empty($row['package_customer_id']) ? $row['package_customer_id'] : null;
            $packageId = !empty($row['package_id']) ? $row['package_id'] : null;
            $serviceId = !empty($row['service_id']) ? $row['service_id'] : null;
            $packageServiceId = !empty($row['package_service_id']) ? $row['package_service_id'] : null;
            $packageCustomerServiceId = !empty($row['package_customer_service_id']) ? $row['package_customer_service_id'] : null;
            $customerId = !empty($row['package_customer_customerId']) ? $row['package_customer_customerId'] : null;
            $appointmentId = !empty($row['appointment_id']) ? $row['appointment_id'] : null;
            $paymentId = !empty($row['payment_id']) ? $row['payment_id'] : null;
            $couponId = !empty($row['coupon_id']) ? $row['coupon_id'] : null;
            $providerId = !empty($row['provider_id']) ? $row['provider_id'] : null;

            if (!array_key_exists($packageCustomerId, $packageCustomers)) {
                $packageCustomers[$packageCustomerId] = [
                    'id'            => $packageCustomerId,
                    'packageId'     => $row['package_customer_packageId'],
                    'purchased'     => $row['package_customer_purchased'],
                    'end'           => $row['package_customer_end'],
                    'status'        => $row['package_customer_status'],
                    'customerId'    => $row['package_customer_customerId'],
                    'bookingsCount' => $row['package_customer_bookingsCount'],
                    'price'         => $row['package_customer_price'],
                    'tax'           => $row['package_customer_tax'],
                    'couponId'      => $row['package_customer_couponId'],
                    'token'         => !empty($row['package_customer_token']) ? $row['package_customer_token'] : null,
                ];
            }

            if ($packageId && empty($packageCustomers[$packageCustomerId]['package'])) {
                $packageCustomers[$packageCustomerId]['package'] = [
                    'id'   => $packageId,
                    'name' => $row['package_name'],
                    'color' => $row['package_color'],
                    'pictureThumbPath' => $row['package_pictureThumbPath'],
                    'pictureFullPath' => $row['package_pictureFullPath'],
                    'calculatedPrice' => $row['package_calculatedPrice'],
                    'discount' => $row['package_discount'],
                    'bookable' => []
                ];
            }

            if ($packageServiceId && $serviceId && !empty($packageCustomers[$packageCustomerId]['package'])) {
                $packageCustomers[$packageCustomerId]['package']['bookable'][$packageServiceId] = [
                    'service' => [
                        'id'   => $row['service_id'],
                        'name' => $row['service_name']
                    ]
                ];
            }

            if ($customerId && empty($packageCustomers[$packageCustomerId]['customer'])) {
                $packageCustomers[$packageCustomerId]['customer'] = [
                    'id'   => $customerId,
                    'firstName' => $row['customer_firstName'],
                    'lastName' => $row['customer_lastName'],
                    'note' => $row['customer_note'],
                    'email' => $row['customer_email'],
                    'type' => 'customer'
                ];
            }


            if ($packageCustomerServiceId && empty($packageCustomers[$packageCustomerId]['packageCustomerServices'][$packageCustomerServiceId])) {
                $packageCustomers[$packageCustomerId]['packageCustomerServices'][$packageCustomerServiceId] = [
                    'id'   => $packageCustomerServiceId,
                    'bookingsCount' => $row['package_customer_service_bookingsCount'],
                ];
            }

            if ($paymentId && empty($packageCustomers[$packageCustomerId]['payments'][$paymentId])) {
                $packageCustomers[$packageCustomerId]['payments'][$paymentId] = [
                    'id'     => $paymentId,
                    'status' => $row['payment_status'],
                    'amount' => $row['payment_amount'],
                    'gateway' => $row['payment_gateway'],
                    'dateTime' => $row['payment_dateTime'],
                    'wcOrderId' => $row['payment_wcOrderId'],
                    'wcOrderItemId' => $row['payment_wcOrderItemId'],
                    'created' => $row['payment_created']
                ];
            }

            if ($appointmentId && empty($packageCustomers[$packageCustomerId]['appointments'][$appointmentId])) {
                $packageCustomers[$packageCustomerId]['appointments'][$appointmentId] = [
                    'id'   => $appointmentId,
                    'customerBookingId' => !empty($row['booking_id']) ? $row['booking_id'] : null,
                    'providerId' => $row['appointment_providerId'],
                    'serviceId' => $row['appointment_serviceId'],
                    'provider' => !empty($row['provider_id']) ? [
                        'id'   => $row['provider_id'],
                        'firstName' => $row['provider_firstName'],
                        'lastName' => $row['provider_lastName'],
                        'email' => $row['provider_email'],
                        'type' => 'provider',
                        'badgeId' => $row['provider_badgeId'],
                        'pictureFullPath' => $row['provider_pictureFullPath'],
                        'pictureThumbPath' => $row['provider_pictureThumbPath'],
                    ] : null,
                    'notifyParticipants' => $row['appointment_notifyParticipants'],
                    'bookingStart' => $row['appointment_bookingStart'],
                    'bookingEnd' => $row['appointment_bookingEnd'],
                    'status' => $row['appointment_status']
                ];
            }

            if ($couponId && empty($packageCustomers[$packageCustomerId]['coupon'])) {
                $packageCustomers[$packageCustomerId]['coupon'] = [
                    'id' => $couponId,
                    'discount' => $row['coupon_discount'],
                    'deduction' => $row['coupon_deduction'],
                    'status' => $row['coupon_status'],
                ];
            }
        }

        $packageCustomersCollection = new Collection();

        foreach ($packageCustomers as $packageCustomerKey => $packageCustomerArray) {
            $packageCustomersCollection->addItem(
                self::create($packageCustomerArray),
                $packageCustomerKey
            );
        }

        return $packageCustomersCollection;
    }
}
