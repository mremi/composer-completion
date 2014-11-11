<?php

/*
 * This file is part of the mremi\composer-completion library.
 *
 * (c) Rémi Marseille <marseille.remi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mremi\ComposerCompletion\Tests;

use Mremi\ComposerCompletion\Cache;

/**
 * Tests the Cache class
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class CacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $directory;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * Initializes the properties used by tests
     */
    protected function setUp()
    {
        $this->filesystem = $this->getMock('Symfony\Component\Filesystem\Filesystem');
        $this->directory  = sprintf('%s/composer-completion/tests/cache', sys_get_temp_dir());
        $this->cache      = new Cache($this->filesystem, $this->directory);
    }

    /**
     * Cleanups the properties used by tests
     */
    protected function tearDown()
    {
        $this->filesystem = null;
        $this->cache      = null;
    }

    /**
     * Tests the isFresh method
     */
    public function testIsFresh()
    {
        $file = tempnam(sys_get_temp_dir(), '');

        $this->assertTrue($this->cache->isFresh($file));

        touch($file, time() - 86400);
        clearstatcache();

        $this->assertFalse($this->cache->isFresh($file));

        unlink($file);
    }

    /**
     * Tests the clear method
     */
    public function testClear()
    {
        $this->filesystem->expects($this->once())->method('remove')->with($this->equalTo($this->directory));

        $this->cache->clear();
    }

    /**
     * Tests the ensureDirectoryExists method
     */
    public function testEnsureDirectoryExists()
    {
        $method = new \ReflectionMethod($this->cache, 'ensureDirectoryExists');
        $method->setAccessible(true);

        $this->filesystem->expects($this->once())->method('mkdir')->with($this->equalTo('foo'));

        $method->invoke($this->cache, 'foo');
    }

    /**
     * Tests the computePath method
     */
    public function testComputePath()
    {
        $method = new \ReflectionMethod($this->cache, 'computePath');
        $method->setAccessible(true);

        $this->assertEquals(sprintf('%s/252/092/mremi', $this->directory), $method->invoke($this->cache, 'mremi'));
        $this->assertEquals(sprintf('%s/207/178/psr', $this->directory), $method->invoke($this->cache, 'psr'));
        $this->assertEquals(sprintf('%s/155/015/symfony', $this->directory), $method->invoke($this->cache, 'symfony'));
    }

    /**
     * Tests the hashCode method
     */
    public function testHashCode()
    {
        $method = new \ReflectionMethod($this->cache, 'hashCode');
        $method->setAccessible(true);

        $this->assertEquals('104160508', $method->invoke($this->cache, 'mremi'));
        $this->assertEquals('111311', $method->invoke($this->cache, 'psr'));
        $this->assertEquals('2551648155', $method->invoke($this->cache, 'symfony'));
    }
}
