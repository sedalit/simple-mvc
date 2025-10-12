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

    public static function write(string $filePath, string $content) : bool
    {
        return file_put_contents($filePath, $content);
    }

    public static function serialize(string $filePath, mixed $content) : bool
    {
        return self::write($filePath, serialize($content));
    }

    public static function unlinkIfExists(string $filePath) : bool
    {
        if (file_exists($filePath)) {
            return unlink($filePath);
        }

        return false;
    }

    public static function unserialize(string $filePath) : mixed
    {
        if (file_exists($filePath)) {
            return unserialize(file_get_contents($filePath));
        }

        return null;
    }

    public static function handleUpload(array $files, string $uploadDir = UPLOADS) : ?array
    {
        $result = [];

        $isMulti = is_array($files['name']);
        $filesCount = $isMulti ? count($files['name']) : 1;

        for ($i = 0; $i < $filesCount; $i++) {
            $file = [
                'name' => $isMulti ? $files['name'][$i] : $files['name'],
                'type'     => $isMulti ? $files['type'][$i]     : $files['type'],
                'tmp_name' => $isMulti ? $files['tmp_name'][$i] : $files['tmp_name'],
                'error'    => $isMulti ? $files['error'][$i]    : $files['error'],
                'size'     => $isMulti ? $files['size'][$i]     : $files['size'],
            ];

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
                $result[] = $destination;
            }
        }

        return !empty($result) ? $result : null;
    }
}