<?php

/*
 * This file is part of the mremi\composer-completion library.
 *
 * (c) Rémi Marseille <marseille.remi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mremi\ComposerCompletion;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Cache class
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class Cache
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $directory;

    /**
     * @var integer
     */
    private $ttl;

    /**
     * Constructor
     *
     * @param Filesystem $filesystem A Filesystem instance
     * @param string     $directory  The base cache directory
     * @param integer    $ttl        The time to live until the cache expires
     */
    public function __construct(Filesystem $filesystem = null, $directory = null, $ttl = 86400)
    {
        if (!$filesystem) {
            $filesystem = new Filesystem;
        }

        if (!$directory) {
            $directory = sprintf('%s/.composer-completion/cache', getenv('HOME'));
        }

        $this->filesystem = $filesystem;
        $this->directory  = $directory;
        $this->ttl        = $ttl;

        $this->ensureDirectoryExists($this->directory);
    }

    /**
     * Retrieves the vendors by a given search
     *
     * @param string $search
     *
     * @return array
     */
    public function find($search)
    {
        // search the exact match
        $parts = explode('/', $search);
        $cache = $this->computePath($parts[0]);

        if (is_file($cache)) {
            if ($this->isFresh($cache)) {
                return array($cache);
            }

            $this->filesystem->remove($cache);
        }

        // search similar results
        $vendors = array();

        foreach (glob(sprintf('%s/*/*/%s*', $this->directory, $parts[0])) as $vendor) {
            if ($this->isFresh($vendor)) {
                $vendors[] = $vendor;
            } else {
                $this->filesystem->remove($vendor);
            }
        }

        return $vendors;
    }

    /**
     * Returns TRUE whether the given file is fresh
     *
     * @param string $file
     *
     * @return boolean
     */
    public function isFresh($file)
    {
        return filemtime($file) + $this->ttl > time();
    }

    /**
     * Writes data in cache
     *
     * @param string  $vendor A vendor name
     * @param string  $data   Data to write in the given vendor
     * @param boolean $append TRUE to append data
     */
    public function write($vendor, $data, $append = true)
    {
        $path = $this->computePath($vendor);

        $this->ensureDirectoryExists(dirname($path));

        $flags = $append ? FILE_APPEND | LOCK_EX : LOCK_EX;

        file_put_contents($path, sprintf('%s ', $data), $flags);
    }

    /**
     * Clears the whole cache
     */
    public function clear()
    {
        $this->filesystem->remove($this->directory);
    }

    /**
     * Ensures the given directory exists, creates it if it does not
     *
     * @param string $directory
     */
    private function ensureDirectoryExists($directory)
    {
        $this->filesystem->mkdir($directory);
    }

    /**
     * Computes the path by the given vendor name
     *
     * @param string $vendor
     *
     * @return string
     */
    private function computePath($vendor)
    {
        $hashCode  = $this->hashCode($vendor);
        $mask      = 255;
        $firstDir  = $hashCode & $mask;
        $secondDir = ($hashCode >> 8) & $mask;

        return sprintf('%s/%03s/%03s/%s', $this->directory, $firstDir, $secondDir, $vendor);
    }

    /**
     * Hashes the given string to 32 bit
     *
     * @param string $str
     *
     * @return integer
     */
    private function hashCode($str)
    {
        $str  = (string) $str;
        $hash = 0;
        $len  = strlen($str);

        if ($len === 0) {
            return $hash;
        }

        for ($i = 0; $i < $len; $i++) {
            $h = $hash << 5;
            $h -= $hash;
            $h += ord($str[$i]);
            $hash = $h;
            $hash &= 0xFFFFFFFF;
        }

        return $hash;
    }
}
