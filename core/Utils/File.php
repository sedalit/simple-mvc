<?php

namespace PHPFramework\Utils;

class File {
    public static function getExtension(string $fileName) : string
    {
        return pathinfo($fileName, PATHINFO_EXTENSION);
    }
}