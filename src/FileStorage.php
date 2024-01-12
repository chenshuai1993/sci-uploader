<?php

/*
 * This file is part of the chenshuai1993/sci-uploader.
 *
 * (c) chenshuai1993 <chen.shuaishuai@scimall.org.cn>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Chenshuai1993\SciUploader;

use Chenshuai1993\SciUploader\Contracts\StorageInterface;
use Chenshuai1993\SciUploader\Exceptions\InvalidArgumentException;

class FileStorage implements StorageInterface
{
    protected $uploadDir = null;

    protected $storageDir = null;

    public function __construct(?string $uploadDir, ?string $storageDir)
    {
        if (!empty($uploadDir)) {
            $this->uploadDir = $uploadDir;
        }
        if (!empty($storageDir)) {
            $this->storageDir = $storageDir;
        }
    }

    public function getUploadDir(): ?string
    {
        return $this->uploadDir;
    }

    public function setUploadDir(?string $uploadDir): void
    {
        $this->uploadDir = $uploadDir;
    }

    /**
     * @return null
     */
    public function getStorageDir()
    {
        return $this->storageDir;
    }

    /**
     * @param null $storageDir
     */
    public function setStorageDir($storageDir): void
    {
        $this->storageDir = $storageDir;
    }

    /**
     * 保存文件.
     *
     * @param string $fileName     文件名
     * @param string $fileHash     文件哈希值
     * @param string $filePath     文件路径
     * @param array  $extendParams 文件扩展参数数组
     *
     * @throws InvalidArgumentException
     */
    public function save(string $fileName, string $fileHash, string $filePath, array $extendParams = []): array
    {
        $result = [];
        $uploadDir = rtrim($this->getUploadDir(), DIRECTORY_SEPARATOR);
        $storageDir = rtrim($this->getStorageDir(), DIRECTORY_SEPARATOR);
        if (empty($uploadDir)) {
            throw new InvalidArgumentException(InvalidArgumentException::UPLOAD_PATH_NOT_CONFIG); // 文件上传路径未配置
        }
        if (empty($storageDir)) {
            throw new InvalidArgumentException(InvalidArgumentException::STORAGE_PATH_NOT_CONFIG); // 文件存储路径未配置
        }
        if (empty($fileName)) {
            throw new InvalidArgumentException(InvalidArgumentException::FILE_NAME_IS_EMPTY); // 文件名称为空
        }
        if (empty($fileHash)) {
            throw new InvalidArgumentException(InvalidArgumentException::FILE_HASH_IS_EMPTY); // 文件hash为空
        }
        if (empty($filePath)) {
            throw new InvalidArgumentException(InvalidArgumentException::FILE_PATH_IS_EMPTY); // 文件路径为空
        }

        $storagePath = $storageDir.DIRECTORY_SEPARATOR.$fileHash;
        if (!file_exists($storageDir)) {
            mkdir($storageDir, 0777, true);
            chmod($storageDir, 0777);
        }

        file_put_contents($storagePath, $filePath, LOCK_EX);
        chmod($storagePath, 0777);

        return $result;
    }

    /**
     * 删除指定的文件.
     *
     * @param string $fileHash 文件的哈希值
     *
     * @throws InvalidArgumentException
     */
    public function delete(string $fileHash): array
    {
        $result = [];
        $uploadDir = rtrim($this->getUploadDir(), DIRECTORY_SEPARATOR);
        $storageDir = rtrim($this->getStorageDir(), DIRECTORY_SEPARATOR);
        if (empty($uploadDir)) {
            throw new InvalidArgumentException(InvalidArgumentException::UPLOAD_PATH_NOT_CONFIG); // 文件上传路径未配置
        }
        if (empty($storageDir)) {
            throw new InvalidArgumentException(InvalidArgumentException::STORAGE_PATH_NOT_CONFIG); // 文件存储路径未配置
        }
        if (empty($fileHash)) {
            throw new InvalidArgumentException(InvalidArgumentException::FILE_HASH_IS_EMPTY); // 文件hash为空
        }

        $storagePath = $storageDir.DIRECTORY_SEPARATOR.$fileHash;

        if (!file_exists($storagePath)) {
            return $result;
        }

        unlink($storagePath);

        return $result;
    }

    /**
     * 根据文件的MD5值查找并返回对应的文件信息.
     *
     * @param string $fileHash 文件的MD5值
     *
     * @return array|mixed[] 文件信息数组，如果找不到文件则返回空数组
     *
     * @throws InvalidArgumentException
     */
    public function findByFileMd5(string $fileHash): array
    {
        $uploadDir = rtrim($this->getUploadDir(), DIRECTORY_SEPARATOR);
        $storageDir = rtrim($this->getStorageDir(), DIRECTORY_SEPARATOR);
        if (empty($uploadDir)) {
            throw new InvalidArgumentException(InvalidArgumentException::UPLOAD_PATH_NOT_CONFIG); // 文件上传路径未配置
        }
        if (empty($storageDir)) {
            throw new InvalidArgumentException(InvalidArgumentException::STORAGE_PATH_NOT_CONFIG); // 文件存储路径未配置
        }
        if (empty($fileHash)) {
            throw new InvalidArgumentException(InvalidArgumentException::FILE_HASH_IS_EMPTY); // 文件hash为空
        }

        $storagePath = $storageDir.DIRECTORY_SEPARATOR.$fileHash;

        if (!file_exists($storagePath)) {
            return [];
        }

        return [
            'file' => file_get_contents($storagePath),
        ];
    }
}
