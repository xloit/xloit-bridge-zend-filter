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

namespace Xloit\Bridge\Zend\Filter\File;

use Xloit\Bridge\Zend\Filter\Exception;
use Xloit\Std\StringUtils;
use Zend\Filter\File\RenameUpload as ZendRenameUpload;

/**
 * A {@link RenameUpload} class.
 *
 * @package Xloit\Bridge\Zend\Filter\File
 */
class RenameUpload extends ZendRenameUpload
{
    /**
     * If this variable is set to TRUE, our library will be able to automatically create non-existed directories.
     *
     * @var bool
     */
    protected $allowCreateFolders = false;

    /**
     * It helps to avoid problems after migrating from case-insensitive file system to case-insensitive
     * (e.g. NTFS->ext or ext->NTFS).
     *
     * @var bool
     */
    protected $caseInsensitiveFilename = false;

    /**
     * Transliterate target filename.
     *
     * @var bool
     */
    protected $transliterateFilename = false;

    /**
     * If this variable is set to TRUE, files dispersion will be supported.
     *
     * @var bool
     */
    protected $enableFileDispersion = false;

    /**
     * Dispersion path.
     *
     * @var string
     */
    protected $dispersionPath;

    /**
     * Target directory.
     *
     * @var string
     */
    protected $targetDirectory;

    /**
     * Set enable files dispersion.
     *
     * @param bool $enableFileDispersion
     *
     * @return $this
     */
    public function setEnableFileDispersion($enableFileDispersion)
    {
        $this->enableFileDispersion = (bool) $enableFileDispersion;

        if ($enableFileDispersion) {
            $this->setAllowCreateFolders(true);
            $this->setTransliterateFilename(true);
        }

        return $this;
    }

    /**
     *
     *
     * @param  array $uploadData $_FILES array
     *
     * @return string
     * @throws \Xloit\Bridge\Zend\Filter\Exception\NoSuchDirectoryException
     */
    protected function getFinalTarget($uploadData)
    {
        $isUploaded = !empty($uploadData['tmp_name']);

        if (!$isUploaded) {
            return $this->getTarget();
        }

        $this->createFolders();

        $finalTarget = parent::getFinalTarget($uploadData);

        if ($isUploaded) {
            $finalTarget = $this->fixCaseInsensitiveFilename($finalTarget);
            $finalTarget = $this->transliterateFilename($finalTarget);
            $finalTarget = $this->fileDispersion($finalTarget);
        }

        return $finalTarget;
    }

    /**
     * Create destination folder on the fly.
     *
     * @return void
     * @throws \Xloit\Bridge\Zend\Filter\Exception\NoSuchDirectoryException
     */
    protected function createFolders()
    {
        $dir    = null;
        $target = $this->getTargetDirectory();

        /** @noinspection IsEmptyFunctionUsageInspection */
        if (!empty($target)) {
            $dir = rtrim($target, '/') . DIRECTORY_SEPARATOR;
            $this->setTarget($dir);
        }

        if ($dir !== null && !is_dir($dir) && $this->isAllowCreateFolders()) {
            /** @noinspection NotOptimalIfConditionsInspection */
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            if (!@mkdir($dir, 0777, true) && !is_dir($dir)) {
                throw new Exception\NoSuchDirectoryException(
                    sprintf('Directory %s does not exists.', $dir)
                );
            }
        }
    }

    /**
     * Retrieve target directory path.
     *
     * @return string
     */
    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    /**
     * Set target directory path.
     *
     * @param string $targetDirectory
     *
     * @return $this
     */
    public function setTargetDirectory($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;

        return $this;
    }

    /**
     * Is allow create folders.
     *
     * @return bool
     */
    public function isAllowCreateFolders()
    {
        return $this->allowCreateFolders;
    }

    /**
     * Allow create folders.
     *
     * @param bool $allowCreateFolders
     *
     * @return $this
     */
    public function setAllowCreateFolders($allowCreateFolders)
    {
        $this->allowCreateFolders = (bool) $allowCreateFolders;

        return $this;
    }

    /**
     * Fix case-insensitive filename.
     *
     * @param string $finalTarget
     *
     * @return string
     */
    protected function fixCaseInsensitiveFilename($finalTarget)
    {
        $targetFile = basename($finalTarget);

        if ($this->isCaseInsensitiveFilename()) {
            $finalTarget = str_replace(
                $targetFile, StringUtils::lower($targetFile), $finalTarget
            );
        }

        return $finalTarget;
    }

    /**
     *
     *
     *
     * @return bool
     */
    public function isCaseInsensitiveFilename()
    {
        return $this->caseInsensitiveFilename;
    }

    /**
     * Set case insensitive filenames.
     *
     * @param bool $caseInsensitiveFilename
     *
     * @return $this
     */
    public function setCaseInsensitiveFilename($caseInsensitiveFilename)
    {
        $this->caseInsensitiveFilename = (bool) $caseInsensitiveFilename;

        return $this;
    }

    /**
     * Transliterate filename.
     *
     * @param string $finalTarget
     *
     * @return string
     */
    protected function transliterateFilename($finalTarget)
    {
        $targetFile = basename($finalTarget);
        if ($this->isTransliterateFilename()) {
            $filename    = pathinfo($targetFile)['filename'];
            $finalTarget = str_replace(
                $filename, StringUtils::slug($filename), $finalTarget
            );
        }

        return $finalTarget;
    }

    /**
     *
     *
     *
     * @return bool
     */
    public function isTransliterateFilename()
    {
        return $this->transliterateFilename;
    }

    /**
     *
     *
     * @param bool $transliterateFilename
     *
     * @return $this
     */
    public function setTransliterateFilename($transliterateFilename)
    {
        $this->transliterateFilename = (bool) $transliterateFilename;
        if ($transliterateFilename) {
            $this->setCaseInsensitiveFilename(true);
        }

        return $this;
    }

    /**
     * Process file dispersion.
     *
     * @param string $finalTarget
     *
     * @return string
     * @throws \Xloit\Bridge\Zend\Filter\Exception\NoSuchDirectoryException
     */
    protected function fileDispersion($finalTarget)
    {
        if (strpos($finalTarget, DIRECTORY_SEPARATOR . 'original_image' . DIRECTORY_SEPARATOR) !== false) {
            return $finalTarget;
        }
        if ($this->isFileDispersionEnabled()) {
            $file           = pathinfo($finalTarget, PATHINFO_BASENAME);
            $dispersionPath = $this->getDispersionPath($file);
            $directory      = pathinfo($finalTarget, PATHINFO_DIRNAME) . $dispersionPath;
            $finalTarget    = $directory . $file;

            $this->setTargetDirectory($directory);
            $this->createFolders();
        }

        return $finalTarget;
    }

    /**
     * Is  file dispersion enabled.
     *
     * @return bool
     */
    public function isFileDispersionEnabled()
    {
        return $this->enableFileDispersion;
    }

    /**
     * Get dispersion path.
     *
     * @param string $fileName
     *
     * @return string
     */
    private function getDispersionPath($fileName)
    {
        $char           = 0;
        $dispersionPath = DIRECTORY_SEPARATOR . 'original_image' . DIRECTORY_SEPARATOR;

        while ($char < 2 && $char < strlen($fileName)) {
            /** @noinspection IsEmptyFunctionUsageInspection */
            if (empty($dispersionPath)) {
                $dispersionPath = DIRECTORY_SEPARATOR . ('.' === $fileName[$char] ? '_' : $fileName[$char]);
            } else {
                $dispersionPath =
                    $this->addDirectorySeparator($dispersionPath) . ('.' === $fileName[$char] ? '_' : $fileName[$char]);
            }

            $char++;
        }

        return $dispersionPath . DIRECTORY_SEPARATOR;
    }

    /**
     * Add dir separator.
     *
     * @param string $dir
     *
     * @return string
     */
    private function addDirectorySeparator($dir)
    {
        /** @noinspection SubStrUsedAsArrayAccessInspection */
        if (substr($dir, -1, 1) !== DIRECTORY_SEPARATOR) {
            $dir .= DIRECTORY_SEPARATOR;
        }

        return $dir;
    }
}
