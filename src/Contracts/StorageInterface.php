<?php

namespace Chenshuai1993\SciUploader\Contracts;

interface StorageInterface
{
    /**
     * 保存方法
     *
     * @param string $fileName 文件名
     * @param string $fileHash 文件哈希值
     * @param string $filePath 文件路径
     * @param array $extendParams 扩展参数，默认为空数组
     *
     * @return void
     */
    public function save(string $fileName, string $fileHash, string $filePath, array $extendParams = []);


    /**
     * 删除文件根据给定的文件哈希值删除文件
     *
     * @param string $fileHash 文件哈希值
     *
     * @return void
     */
    public function delete(string $fileHash);


    /**
     * 查找:根据给定的文件哈希值删除文件
     *
     * @param string $fileHash 文件哈希值
     *
     * @return void
     */
    public function findByFileMd5(string $fileHash);
}