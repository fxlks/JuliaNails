<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Domain\Services\Report;

/**
 * Interface ReportServiceInterface
 *
 * @package AmeliaBooking\Domain\Services\Report
 */
interface ReportServiceInterface
{
    public function generateReport(array $rows, string $name, string $delimiter);
}
