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

use Zend\Filter\AbstractFilter;

/**
 * A {@link CarriageReturnToHtml} class.
 *
 * @package Xloit\Bridge\Zend\Filter
 */
class CarriageReturnToHtml extends AbstractFilter
{
    /**
     * Convert paragraphs of text into filtered HTML.
     *
     * @param mixed $value
     *
     * @return string
     */
    public function filter($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        // Strip unicode bombs, and make sure all newlines are UNIX newlines.
        $value = preg_replace('{^\xEF\xBB\xBF|\x1A}', '', $value);
        $value = preg_replace('{\r\n?}', "\n", $value);

        $lambda = function($text) {
            $text = htmlspecialchars($text, ENT_COMPAT, 'UTF-8');
            /** @noinspection NotOptimalRegularExpressionsInspection */
            $text = preg_replace('/--/', '&mdash;', $text);
            $text = nl2br($text, false);
            $text = sprintf('<p>%s</p>', $text);

            return $text;
        };

        $paragraphs = preg_split('/\n{2,}/', $value, -1, PREG_SPLIT_NO_EMPTY);
        $paragraphs = array_map($lambda, $paragraphs);

        return implode("\n\n", $paragraphs);
    }
}
