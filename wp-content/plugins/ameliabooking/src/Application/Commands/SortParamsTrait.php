<?php

namespace AmeliaBooking\Application\Commands;

/**
 * Trait SortParamsTrait
 *
 * @package AmeliaBooking\Application\Commands
 */
trait SortParamsTrait
{
    /**
     * @param array  $params
     * @param array  $allowedFields
     *
     * @return array
     */
    protected function parseSortParams($params, array $allowedFields)
    {
        if (empty($params['sort'])) {
            return $params;
        }

        $sort = $params['sort'];
        $isDescending = substr($sort, 0, 1) === '-';
        $sortField = $isDescending ? substr($sort, 1) : $sort;

        if (!in_array($sortField, $allowedFields, true)) {
            unset($params['sort']);

            return $params;
        }

        $params['sort'] = [
            'field' => $sortField,
            'order' => $isDescending ? 'DESC' : 'ASC',
        ];

        return $params;
    }
}
