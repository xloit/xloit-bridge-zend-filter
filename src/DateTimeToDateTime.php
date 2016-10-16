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

use Traversable;

/**
 * A {@link DateTimeToDateTime} class.
 *
 * @package Xloit\Bridge\Zend\Filter
 */
class DateTimeToDateTime extends DateToDateTime
{
    /**
     * Constructor to prevent {@link DateTimeToDateTime} from being loaded more than once.
     *
     * @param array|Traversable $options
     */
    public function __construct($options = null)
    {
        $this->setFormat('Y.m.d H:i:s');

        parent::__construct($options);
    }

    /**
     * Allow the format key to be format and date_format For consistency with the ZF2 Date Element.
     *
     * @param  array|Traversable $options
     *
     * @return static
     * @throws \Zend\Filter\Exception\InvalidArgumentException
     */
    public function setOptions($options)
    {
        $format = null;

        /** @noinspection UnSafeIsSetOverArrayInspection */
        if (isset($options['datetime_format'])) {
            $format = $options['datetime_format'];

            unset($options['datetime_format']);
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
}
