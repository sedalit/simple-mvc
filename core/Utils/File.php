<?php

namespace PHPFramework\Utils;

class File {
    public static function getExtension(string $fileName) : string
    {
        return pathinfo($fileName, PATHINFO_EXTENSION);
    }

    public static function getFileName(mixed $file) : string
    {
        if (is_string($file)) {
            return $file;
        } else if (is_array($file)) {
            return $file['name'];
        }

        return '';
    }

    public static function getFileSize(string $filePath) : int
    {
        return filesize($filePath);
    }

    public static function handleUpload(array $file, string $uploadDir = UPLOADS) : ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if ($uploadDir === UPLOADS) {
            $uploadDir .= '/' . date('Y') . '/' . date('m') . '/' . date('d');
        }

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = self::getExtension($file['name']);
        $filename = uniqid('', true) . '.' . strtolower($ext);

        $destination = rtrim($uploadDir, '/') . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $destination;
        }

        return null;
    }
}