<?php

namespace App\Services;

class CacheService {
    private $cache;
    private const TTL = 3600; // 1 hour

    public function __construct() {
        if (extension_loaded('memcached')) {
            $this->cache = new \Memcached();
            $this->cache->addServer('localhost', 11211);
        }
    }

    public function get($key) {
        if (!$this->cache) {
            return false;
        }
        return $this->cache->get($key);
    }

    public function set($key, $value, $ttl = self::TTL) {
        if (!$this->cache) {
            return false;
        }
        return $this->cache->set($key, $value, $ttl);
    }

    public function delete($key) {
        if (!$this->cache) {
            return false;
        }
        return $this->cache->delete($key);
    }

    public function flush() {
        if (!$this->cache) {
            return false;
        }
        return $this->cache->flush();
    }

    public function isAvailable() {
        return $this->cache !== null;
    }
} 