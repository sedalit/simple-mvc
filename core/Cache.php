<?php

namespace PHPFramework;

use PHPFramework\Utils\File;

class Cache {
    protected const EXTENSION = 'txt';

    public function set(string $key, mixed $data, int $seconds = 3600) : void
    {
        $content['data'] = $data;
        $content['end_time'] = time() + $seconds;
        $file = $this->fileNameByKey($key);

        File::serialize($file, $content);
    }

    public function get(string $key, mixed $default = null) : mixed
    {
        $file = $this->fileNameByKey($key);
        $content = File::unserialize($file);

        if (!$content) return $default;

        if (time() <= $content['end_time']) {
            return $content['data'];
        }
        unlink($file);

        return $default;
    }

    public function forget(string $key) : void
    {
        $file = $this->fileNameByKey($key);
        File::unlinkIfExists($file);
    }

    protected function fileNameByKey(string $key) : string
    {
        return CACHE . '/' . md5($key) . '.' . self::EXTENSION;
    }
}