## 文件上传工具类

### 介绍
1. 支持文件分片上传
2. 支持服务器文件存储；通过实现FileStorage接口，可自定义文件存储方式
3. 支持文件秒传、分片断点续传

### 安装

```php
composer require chenshuai1993/sci-uploader -vvv
```

### 使用

```php
//定义分片文件上传路径
$upload ='./upload';
//定义合并后文件存储路径
$storageDir ='./storage';
//声明文件存储类 && 声明分片上传类
$uploader = new MultiPartUpload(new FileStorage($upload, $storageDir));
try {
    $fileName = "xxx.mp4";
    //上传分片
    $uploader->uploadPart($fileName, $fileHash, $partNumber);
    //计算分片数量是否和总数一致
    $count = $uploader->countParts($fileName, $fileHash);
    if ($count == $partTotal){
        //合并分片文件
        $uploader->mergePartsToFile($fileName, $fileHash, $partTotal);
        //完成分片上传(可选)
        $uploader->completeMultipartUpload(function (){
            //这里有个闭包函数、执行完成后的操作
        });
    }
    
    //判断是否存在文件
    $uploader->getFileByHash($fileHash);

    //删除文件
    $uploader->deleteFileByHash($fileHash);
} catch (Exception $e) {
    print_r($e->getCode());
    print_r($e->getMessage());
}

```