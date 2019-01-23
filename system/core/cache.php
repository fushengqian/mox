<?php
class core_cache
{
    private $cache_factory;
    private $frontendName = 'Core';
    
    private $frontendOptions = array(
        'lifeTime' => 3600,
        'automatic_serialization' => TRUE
    );
    
    // 支持 File, Memcached, APC, Xcache, 手册参考: http://framework.zend.com/manual/zh/zend.cache.html
    private $backendName = 'File';
    
    private $backendOptions = array(
        // Memcache 配置
        'servers' => array(
            array(
                'host' => '127.0.0.1',
                'port' => 11211,
                'persistent' => true,
                'timeout' => 5,
                'compression' => false,    // 压缩
                'compatibility' => false    // 兼容旧版 Memcache servers
            )
        )
    );
    
    private $groupPrefix = '_group_';
    private $cachePrefix = '_cache_';
    
    public function __construct()
    {
        $this->groupPrefix = G_COOKIE_HASH_KEY . $this->groupPrefix;
        $this->cachePrefix = G_COOKIE_HASH_KEY . $this->cachePrefix;
        $this->cache_factory = Zend_Cache::factory($this->frontendName, $this->backendName, $this->frontendOptions, $this->backendOptions);
        return true;
    }
    
    /**
     * SET
     * @param  $key
     * @param  $value
     * @param  $group
     * @param  $lifetime
     * @return boolean
     */
    public function set($key, $value, $lifetime = 0, $group = null)
    {
        if (! $key)
        {
            return false;
        }
        
        $result = $this->cache_factory->save($value, $this->cachePrefix . $key, array(), $lifetime);
        
        if ($group)
        {
            if (is_array($group))
            {
                if (count($group) > 0)
                {
                    foreach ($group as $cg)
                    {
                        $this->setGroup($cg, $key, $lifetime);
                    }
                }
            }
            else
            {
                $this->setGroup($group, $key, $lifetime);
            }
        }
        
        return $result;
    }
    
    /**
     * GET
     * @param  $key
     */
    public function get($key)
    {
        if (! $key)
        {
            return false;
        }
        
        return $this->cache_factory->load($this->cachePrefix . $key);
    }
    
    /**
     * SET_GROUP
     * @param  $group_name
     * @param  $key
     */
    public function setGroup($group_name, $key, $lifetime)
    {
        $groupData = $this->get($this->groupPrefix . $group_name);
        
        if (is_array($groupData) && in_array($key, $groupData))
        {
            return false;
        }
        
        $groupData[] = $key;
        
        return $this->set($this->groupPrefix . $group_name, $groupData, $lifetime);
    }
    
    /**
     * GET GROUP
     * @param  $group_name
     */
    public function getGroup($group_name)
    {
        return $this->get($this->groupPrefix . $group_name);
    }
    
    /**
     * CLEAN GROUP
     * @param  $group_name
     */
    public function cleanGroup($group_name)
    {
        $groupData = $this->get($this->groupPrefix . $group_name);
        
        if ($groupData && is_array($groupData))
        {
            foreach ($groupData as $item)
            {
                $this->delete($item);
            }
        }
        
        $this->delete($this->groupPrefix . $group_name);
    }
    
    /**
     * DELETE
     * @param  $key
     */
    public function delete($key)
    {
        $key = $this->cachePrefix . $key;
        return $this->cache_factory->remove($key);
    }
    
    /**
     * CLEAN
     */
    public function clean()
    {
        return $this->cache_factory->clean(Zend_Cache::CLEANING_MODE_ALL);
    }
    
    /**
     * START
     * @param  $key
     */
    public function start($key)
    {
        $key = $this->cachePrefix . $key;
        $this->cache_factory->start($key);
    }
    
    /**
     * END
     */
    public function end()
    {
        $this->cache_factory->end();
    }
}

