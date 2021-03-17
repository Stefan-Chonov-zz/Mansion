<?php


namespace Mansion\Cache;

class CacheManager
{
    /**
     * @var \Redis
     */
    private \Redis $redis;

    /**
     * CacheManager constructor.
     */
    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect(getenv('REDIS_HOST'), getenv('REDIS_PORT'));
    }

    /**
     * Caching data.
     *
     * @param array $data
     */
    public function save(array $data): void
    {
        try {
            foreach ($data as $key => $value) {
                $this->redis->set($key, json_encode($value));
            }
        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }
    }

    /**
     * Retrieves all cached data.
     *
     * @return array
     */
    public function getAll(): array
    {
        $result = array();

        try {
            $keys = $this->redis->keys('*');
            foreach ($keys as $key) {
                $result[$key] = json_decode($this->redis->get($key), true);
            }
        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }

        return $result;
    }

    /**
     * Retrieves cached data by given key.
     *
     * @param string $key
     * @return array
     */
    public function getByKey(string $key): array
    {
        try {
            $result = $this->redis->get($key);
            if ($result) {
                return json_decode($result, true);
            }
        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }

        return array();
    }

    /**
     * Clear all cached data.
     */
    public function clear(): void
    {
        try {
            $this->redis->flushAll();
        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }
    }

    /**
     * Delete cached data for a key.
     *
     * @param string $key
     */
    public function deleteByKey(string $key): void
    {
        try {
            $this->redis->del($key);
        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }
    }
}