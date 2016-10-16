<?php
/**
 * This source file is part of Virtupeer project.
 *
 * @link      https://virtupeer.com
 * @copyright Copyright (c) 2016, Virtupeer. All rights reserved.
 */

namespace Xloit\Bridge\Zend\Filter;

use Zend\ServiceManager\Factory\InvokableFactory;

/**
 * A {@link Module} class.
 *
 * @package Xloit\Bridge\Zend\Filter
 */
class Module
{
    /**
     * Return default zend-validator configuration for zend-mvc applications.
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'filters' => [
                'aliases'   => [
                    'carriagereturntohtml' => CarriageReturnToHtml::class,
                    'carriageReturnToHtml' => CarriageReturnToHtml::class,
                    'CarriageReturnToHtml' => CarriageReturnToHtml::class,
                    'datetimetodateTime'   => DateTimeToDateTime::class,
                    'dateTimeToDateTime'   => DateTimeToDateTime::class,
                    'DateTimeToDateTime'   => DateTimeToDateTime::class,
                    'datetodatetime'       => DateToDateTime::class,
                    'dateToDateTime'       => DateToDateTime::class,
                    'DateToDateTime'       => DateToDateTime::class,
                    'slugify'              => Slugify::class,
                    'Slugify'              => Slugify::class,
                    'stripcarriagereturn'  => StripCarriageReturn::class,
                    'stripCarriageReturn'  => StripCarriageReturn::class,
                    'StripCarriageReturn'  => StripCarriageReturn::class,
                    'timetodatetime'       => TimeToDateTime::class,
                    'timeToDateTime'       => TimeToDateTime::class,
                    'TimeToDateTime'       => TimeToDateTime::class,
                    'filerenameupload'     => File\RenameUpload::class,
                    'fileRenameUpload'     => File\RenameUpload::class,
                    'FileRenameUpload'     => File\RenameUpload::class,
                    'wordtitlecase'        => Word\TitleCase::class,
                    'wordTitleCase'        => Word\TitleCase::class,
                    'WordTitleCase'        => Word\TitleCase::class
                ],
                'factories' => [
                    CarriageReturnToHtml::class => InvokableFactory::class,
                    DateTimeToDateTime::class   => InvokableFactory::class,
                    DateToDateTime::class       => InvokableFactory::class,
                    Slugify::class              => InvokableFactory::class,
                    StripCarriageReturn::class  => InvokableFactory::class,
                    TimeToDateTime::class       => InvokableFactory::class,
                    File\RenameUpload::class    => InvokableFactory::class,
                    Word\TitleCase::class       => InvokableFactory::class
                ]
            ]
        ];
    }
}
