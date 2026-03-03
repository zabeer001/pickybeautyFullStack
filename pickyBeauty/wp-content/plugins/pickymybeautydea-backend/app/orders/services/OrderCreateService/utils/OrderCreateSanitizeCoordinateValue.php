<?php
if (!defined('ABSPATH')) exit;

class OrderCreateSanitizeCoordinateValue
{
    public static function run($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }
}
