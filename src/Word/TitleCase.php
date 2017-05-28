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

namespace Xloit\Bridge\Zend\Filter\Word;

use Zend\Filter\AbstractUnicode;

/**
 * A {@link TitleCase} class.
 *
 * @package Xloit\Bridge\Zend\Filter\Word
 */
class TitleCase extends AbstractUnicode
{
    /**
     * An array of words that should not be capitalized in title case.
     *
     * @var array
     */
    protected static $_excludeWords = [
        'of',
        'a',
        'the',
        'and',
        'an',
        'or',
        'nor',
        'but',
        'is',
        'if',
        'then',
        'else',
        'when',
        'at',
        'from',
        'by',
        'on',
        'off',
        'for',
        'in',
        'out',
        'over',
        'to',
        'into',
        'with'
    ];

    /**
     * Filter options.
     *
     * @var array
     */
    protected $options = [
        'encoding' => null
    ];

    /**
     * Constructor to prevent {@link TitleCase} from being loaded more than once.
     *
     * @param string|array|\Traversable $encodingOrOptions
     *
     * @throws \Zend\Filter\Exception\InvalidArgumentException
     * @throws \Zend\Filter\Exception\ExtensionNotLoadedException
     */
    public function __construct($encodingOrOptions = null)
    {
        if ($encodingOrOptions !== null) {
            if (static::isOptions($encodingOrOptions)) {
                $this->setOptions($encodingOrOptions);
            } else {
                $this->setEncoding($encodingOrOptions);
            }
        }
    }

    /**
     * Returns the result of filtering $value.
     *
     * @param mixed $value
     *
     * @return string
     * @throws \Zend\Filter\Exception\RuntimeException
     */
    public function filter($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        $value = (string) $value;

        // Split the string into separate words
        /** @var array $words */
        $words   = explode(' ', (string) $value);
        $results = [];

        foreach ($words as $key => $word) {
            // If this is the first, or it's not a small word, capitalize it
            if ($key === 0 || !in_array($word, static::$_excludeWords, true)) {
                if ($this->options['encoding'] !== null) {
                    $word = mb_convert_case($word, MB_CASE_TITLE, $this->options['encoding']);
                } else {
                    $word = ucwords(strtolower($word));
                }
            }

            $word = trim($word);

            if (strlen($word) > 0) {
                $results[] = $word;
            }
        }

        return implode(' ', $results);
    }
}
