<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/10
 * Time: 17:38
 */

namespace app\api\controller\v1;


use app\api\service\Excel as ExcelService;
use think\facade\Request;
use app\api\controller\v1\common\Output;
use app\lib\exception\FileUploadException;
use app\api\service\ImageUpload as ImageUploadService;

class FileUpload extends BaseController
{
    private $file;
    private $isOriginal;
    private $OssOrLocal = 1;

    private function getFile()
    {
        $this->file = Request::file('file');
        if(!$this->file){
            throw new FileUploadException([
                'message'=> '没有文件',
                'errorCode' => 60001
            ]);
        }

        $this->isOriginal = Request::post('isOriginal', 0);
        $this->OssOrLocal = Request::post('OssOrLocal', 1);
    }


    /**
     * 上传图片文件文件(默认上传到本地)
     * @throws FileUploadException
     */
    public function uploadImage()
    {
        $this->getFile();
        if($this->OssOrLocal == 1){
            return $this->uploadToLocal();
        } else {
            return $this->uploadToOss();
        }
    }


    // 上传excel文件
    public function uploadExcel()
    {
        $this->getFile();
        $f = ExcelService::excelUp($this->file, $this->isOriginal, $this->OssOrLocal);
        return Output::out('上传', $f);
    }


    /**
     * 文件上传到OSS
     * @param file 文件名/多文件时一个数组
     * @param isOriginal 是否使用原文件名
     * @return \think\response\Json
     */
    public function uploadToOss()
    {
        if(is_array($this->file)){
            $f = ImageUploadService::moreFile($this->file, $this->isOriginal, 2);
            return Output::out('上传', $f);
        } else {
            $f = ImageUploadService::oneFile($this->file, $this->isOriginal, 2);
            return Output::out('上传文件', $f);
        }

    }


    /**
     * 上传文件到本地
     * @return \think\response\Json
     */
    public function uploadToLocal()
    {
        if(is_array($this->file)){
            $f = ImageUploadService::moreFile($this->file, $this->isOriginal, 1);
            return Output::out('上传', $f);
        } else {
            $f = ImageUploadService::oneFile($this->file, $this->isOriginal, 1);
            return Output::out('上传文件', $f);
        }
    }


    // 把excel表格数据导入到数据库
    public function excelToDb()
    {
        $this->getFile();
        $head = [];
//        $head = [
////            'ID' => 'id',
//            '路由' => 'auth_route',
//            '版本' => 'auth_route_version',
//            '路由名称' => 'auth_name',
//            '路由描述' => 'auth_desc',
//            '路由排序' => 'auth_order',
//            '路由状态' => 'auth_status',
//            'create_time' => 'create_time',
//        ];
        $data = ExcelService::getExcelData($this->file, $head, $save = 0, $isOriginal = 0, $OssOrLocal = 1);
        var_dump($data);

        // 把数据导入数据库 二维数组形式 字段=>值
//        $res = db('authorities_copy')->insertAll($data);
//        var_dump($res);
    }

    // 把数据导入到excel
    public function dataToExcel()
    {
        $heads = [
            'ID',
            '路由',
            '版本',
            '路由名称',
            '路由描述',
            '路由排序',
            '路由状态',
            '创建时间',
        ];
        $keys = [
            'id', 'auth_route', 'auth_route_version', 'auth_name',
            'auth_desc', 'auth_order', 'auth_status', 'create_time',
        ];

        $data = \app\api\model\Auth::authWhere()->toArray();

        $url = ExcelService::dataToExcel($heads, $keys, $data, $ext = 'xlsx', $fileName = '', $title = 'swg');
        return Output::out('导入', $url);
    }
}