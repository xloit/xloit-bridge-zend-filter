<?php
/**
 * This source file is part of Xloit project.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License that is bundled with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * <http://www.opensource.org/licenses/mit-license.php>
 * If you did not receive a copy of the license and are unable to obtain it through the world-wide-web,
 * please send an email to <license@xloit.com> so we can send you a copy immediately.
 *
 * @license   MIT
 * @link      http://xloit.com
 * @copyright Copyright (c) 2016, Xloit. All rights reserved.
 */

namespace Xloit\Bridge\Zend\Filter;

use DateTime as PhpDateTime;
use Traversable;
use Xloit\DateTime\DateTime;
use Zend\Filter\DateTimeFormatter;

/**
 * A {@link DateToDateTime} class.
 *
 * @package Xloit\Bridge\Zend\Filter
 */
class DateToDateTime extends DateTimeFormatter
{
    /**
     * Constructor to prevent {@link DateToDateTime} from being loaded more than once.
     *
     * @param array|Traversable $options
     */
    public function __construct($options = null)
    {
        $this->setFormat(PhpDateTime::ATOM);

        parent::__construct($options);
    }

    /**
     * Allow the format key to be format and date_format For consistency with the ZF2 Date Element.
     *
     * @param array|Traversable $options
     *
     * @throws \Zend\Filter\Exception\InvalidArgumentException
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $format = null;

        /** @noinspection UnSafeIsSetOverArrayInspection */
        if (isset($options['date_format'])) {
            $format = $options['date_format'];

            unset($options['date_format']);
        } /** @noinspection UnSafeIsSetOverArrayInspection */ elseif (isset($options['format'])) {
            $format = $options['format'];

            unset($options['format']);
        }

        if ($format) {
            $options['format'] = $format;
        }

        parent::setOptions($options);

        return $this;
    }

    /**
     * Returns the result of filtering $value.
     *
     * @param mixed $value
     *
     * @return DateTime|mixed
     * @throws \Zend\Filter\Exception\InvalidArgumentException
     */
    public function filter($value)
    {
        if ($value === '' || $value === null) {
            return $value;
        }

        if (!is_string($value) && !is_int($value) && !$value instanceof DateTime) {
            return $value;
        }

        /**
         * We try to create a \DateTime according to the format. If the creation fails, we return the string itself.
         * So it's treated by Validate\Date
         */
        $date = is_int($value) ? date_create("@$value") // from timestamp
            : PhpDateTime::createFromFormat($this->format, $value);

        // Invalid dates can show up as warnings (ie. "2007-02-99") and still return a DateTime object
        $errors = PhpDateTime::getLastErrors();

        if ($date === false || (array_key_exists('warning_count', $errors) && $errors['warning_count'] > 0)) {
            return $value;
        }

        return DateTime::instance($date);
    }
}
