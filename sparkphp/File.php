<?php

namespace SparkPHP;

// File handling class
class File
{
    public $root = '/'; // Root directory for file operations

    // Upload a file to the specified relative path
    public function upload($file, $relativePath, $name = null, $update = false)
    {
        // Check if file is provided and uploaded without errors
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'No file uploaded or upload error'];
        }

        $fullPath = $this->root . '/' . ltrim($relativePath, '/');
        $dir = dirname($fullPath);

        // Create directory if it doesn't exist
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = $name ?? basename($file['name']);
        $targetFile = $dir . '/' . $filename;

        // If not updating, avoid overwriting existing files by renaming
        if (!$update && file_exists($targetFile)) {
            $fileInfo = pathinfo($filename);
            $base = $fileInfo['filename'];
            $ext = isset($fileInfo['extension']) ? '.' . $fileInfo['extension'] : '';
            $i = 1;
            do {
                $newName = $base . '_' . $i . $ext;
                $targetFile = $dir . '/' . $newName;
                $i++;
            } while (file_exists($targetFile));
            $filename = $newName;
        }

        // Move the uploaded file to the target location
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return [
                'success' => true,
                'path' => $targetFile,
                'filename' => $filename,
                'relative' => ltrim($relativePath, '/'),
            ];
        } else {
            return ['error' => 'Failed to move uploaded file'];
        }
    }

    // Delete a file at the specified relative path
    public function delete($relativePath)
    {
        $fullPath = $this->root . '/' . ltrim($relativePath, '/');
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    // Get info about a file (size, modified time, path)
    public function info($relativePath)
    {
        $fullPath = $this->root . '/' . ltrim($relativePath, '/');
        if (file_exists($fullPath)) {
            return [
                'size' => filesize($fullPath),
                'modified' => filemtime($fullPath),
                'path' => $fullPath
            ];
        }
        return null;
    }

    // List files in a directory
    public function list($relativeDir)
    {
        $fullDir = $this->root . '/' . trim($relativeDir, '/');
        if (!is_dir($fullDir)) return [];
        return array_values(array_diff(scandir($fullDir), ['.', '..']));
    }
}
