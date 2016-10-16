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

use Cocur\Slugify\RuleProvider\DefaultRuleProvider;
use Cocur\Slugify\RuleProvider\RuleProviderInterface;
use Cocur\Slugify\Slugify as BaseSlugify;
use Traversable;
use Zend\Filter\AbstractFilter;

/**
 * A {@link Slugify} class.
 *
 * @package Xloit\Bridge\Zend\Filter
 */
class Slugify extends AbstractFilter
{
    /**
     *
     *
     * @var array
     */
    protected $rules = [];

    /**
     *
     *
     * @var RuleProviderInterface
     */
    protected $provider;

    /**
     *
     *
     * @var string
     */
    protected $separator = '-';

    /**
     *
     *
     * @see   Slugify::$options
     *
     * @var array
     */
    protected $options = [
        'regexp'    => BaseSlugify::LOWERCASE_NUMBERS_DASHES,
        'lowercase' => true,
        'rulesets'  => [
            'default',
            'burmese',
            'hindi',
            'georgian',
            'norwegian',
            'vietnamese',
            'ukrainian',
            'latvian',
            'finnish',
            'greek',
            'czech',
            'arabic',
            'turkish',
            'polish',
            'german',
            'russian'
        ]
    ];

    /**
     * Constructor to prevent {@link Slug} from being loaded more than once.
     *
     * @param array|Traversable     $options
     * @param RuleProviderInterface $provider
     *
     * @throws \Zend\Filter\Exception\InvalidArgumentException
     */
    public function __construct($options = null, RuleProviderInterface $provider = null)
    {
        if (!$provider) {
            /** @noinspection UnSafeIsSetOverArrayInspection */
            if (isset($options['provider']) && $options['provider'] instanceof RuleProviderInterface) {
                $provider = $options['provider'];

                unset($options['provider']);
            } else {
                $provider = new DefaultRuleProvider();
            }
        }

        if (is_array($options) || $options instanceof Traversable) {
            $this->setOptions($options);
        }

        $this->setProvider($provider);
    }

    /**
     *
     *
     * @param string $separator
     *
     * @return static
     */
    public function setSeparator($separator)
    {
        $this->separator = $separator;

        return $this;
    }

    /**
     *
     *
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    /**
     *
     *
     * @param array $rules
     *
     * @return static
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     *
     *
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     *
     *
     * @param RuleProviderInterface $provider
     *
     * @return static
     */
    public function setProvider(RuleProviderInterface $provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     *
     *
     * @return RuleProviderInterface
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Adds a custom rule to Slugify.
     *
     * @param string $character   Character
     * @param string $replacement Replacement character
     *
     * @return static
     */
    public function addRule($character, $replacement)
    {
        $this->rules[$character] = $replacement;

        return $this;
    }

    /**
     * Adds multiple rules to Slugify.
     *
     * @param array $rules
     *
     * @return static
     */
    public function addRules(array $rules)
    {
        foreach ($rules as $character => $replacement) {
            $this->addRule($character, $replacement);
        }

        return $this;
    }

    /**
     *
     *
     * @param string $ruleSet
     *
     * @return Slugify
     */
    public function activateRuleSet($ruleSet)
    {
        return $this->addRules($this->getProvider()->getRules($ruleSet));
    }

    /**
     * Returns the result of filtering $value.
     *
     * @param  mixed $value
     *
     * @return mixed
     * @throws Exception\RuntimeException
     */
    public function filter($value)
    {
        if (!is_scalar($value) && !is_array($value)) {
            return $value;
        }

        if ($this->separator === null) {
            throw new Exception\RuntimeException('You must provide a separator for this filter to work.');
        }

        $slugify = new BaseSlugify($this->options, $this->provider);

        $slugify->addRules($this->rules);

        return $slugify->slugify($value);
    }
}
