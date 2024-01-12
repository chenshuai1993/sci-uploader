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

use Chenshuai1993\SciUploader\Contracts\MultiPartUploadInterface;
use Chenshuai1993\SciUploader\Contracts\StorageInterface;
use Chenshuai1993\SciUploader\Exceptions\InvalidArgumentException;

class MultiPartUpload implements MultiPartUploadInterface
{
    protected $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function getStorage(): StorageInterface
    {
        return $this->storage;
    }

    public function setStorage(StorageInterface $storage): void
    {
        $this->storage = $storage;
    }

    /**
     * @desc 上传分片
     *
     * @param string $fileName   文件名
     * @param string $fileHash   文件哈希值
     * @param int    $partNumber 分片序号
     *
     * @throws \Exception
     */
    public function uploadPart(string $fileName, string $fileHash, int $partNumber): array
    {
        $results = [];
        $uploadDir = $this->storage->getUploadDir();

        if (empty($uploadDir)) {
            throw new InvalidArgumentException(InvalidArgumentException::UPLOAD_PATH_NOT_CONFIG); // 文件上传路径未配置
        }

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
            chmod($uploadDir, 0777);
        }

        // 根据文件哈希值和文件名构建目标文件夹路径
        $fileDir = $uploadDir.DIRECTORY_SEPARATOR.$fileHash;

        // 如果目标文件夹不存在，则创建路径并设置权限为0777
        if (!file_exists($fileDir)) {
            mkdir($fileDir, 0777, true);
            chmod($fileDir, 0777);
        }

        // 构建完整的目标文件路径
        $targetFile = $fileDir.DIRECTORY_SEPARATOR.$fileName.'.part'.$partNumber;

        // 如果目标文件已经存在，则直接返回上传成功的信息
        if (file_exists($targetFile)) {
            return $results;
        }

        // 将分片保存到服务器
        $moveResult = is_uploaded_file($_FILES['file']['tmp_name']) && move_uploaded_file($_FILES['file']['tmp_name'], $targetFile);
        if (empty($moveResult)) {
            throw new InvalidArgumentException(InvalidArgumentException::MOVE_FILE_FAIL); // 移动文件失败
        }

        return $results;
    }

    /**
     * 合并分片.
     *
     * @desc 合并分片
     *
     * @param string $fileName     文件名
     * @param string $fileHash     文件哈希值
     * @param int    $totalNumbers 文件分片总数
     *
     * @throws InvalidArgumentException
     */
    public function mergePartsToFile(string $fileName, string $fileHash, int $totalNumbers): array
    {
        $results = [];
        $uploadDir = $this->storage->getUploadDir();

        if (empty($uploadDir)) {
            throw new InvalidArgumentException(InvalidArgumentException::UPLOAD_PATH_NOT_CONFIG); // 文件上传路径未配置
        }

        $outputFile = $uploadDir.DIRECTORY_SEPARATOR.$fileHash.DIRECTORY_SEPARATOR.$fileName;
        $outputHandle = fopen($outputFile, 'ab');

        // 合并分片
        for ($i = 1; $i <= $totalNumbers; ++$i) {
            $part = file_get_contents($outputFile.'.part'.$i);
            fwrite($outputHandle, $part);
        }

        fclose($outputHandle);
        chmod($outputFile, 0777);

        // 入库
        $this->storage->save($fileName, $fileHash, $outputFile, []);

        // 删除分片
        for ($i = 1; $i <= $totalNumbers; ++$i) {
            unlink($outputFile.'.part'.$i);
        }

        return $results;
    }

    /**
     * 计算当前文件hash对应已上传分片总数.
     *
     * @param string $fileName 文件名
     * @param string $fileHash 文件哈希值
     *
     * @throws InvalidArgumentException
     */
    public function countParts(string $fileName, string $fileHash): int
    {
        $uploadDir = $this->storage->getUploadDir();

        if (empty($uploadDir)) {
            throw new InvalidArgumentException(InvalidArgumentException::UPLOAD_PATH_NOT_CONFIG); // 文件上传路径未配置
        }

        $dir = $uploadDir.DIRECTORY_SEPARATOR.$fileHash.DIRECTORY_SEPARATOR; // 目录路径
        $prefix = "{$fileName}.part*"; // 文件名模式
        // 使用 glob 函数匹配文件数量
        $files = glob($dir.$prefix.'*');

        return count($files);
    }

    /**
     * 完成分片上传后的操作.
     */
    public function completeMultipartUpload(?\Closure $closure = null): void
    {
        if (is_callable($closure)) {
            // 如果传入的是闭包，执行闭包
            $closure();
        }
    }

    /**
     * 通过文件 hash 获取文件信息.
     */
    public function getFileByHash(string $fileHash): array
    {
        $find = $this->storage->findByFileMd5($fileHash);
        if (!empty($find)) {
            return [
                'file' => $find['file'],
            ];
        }

        return [];
    }

    /**
     * 根据文件哈希值删除文件.
     *
     * @param string $fileHash 文件哈希值
     *
     * @return bool 是否成功删除文件
     */
    public function deleteFileByHash(string $fileHash): bool
    {
        $find = $this->storage->findByFileMd5($fileHash);
        if (empty($find)) {
            return false;
        }

        $this->storage->delete($fileHash);

        return true;
    }
}
