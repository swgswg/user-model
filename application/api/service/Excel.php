<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/11
 * Time: 11:40
 */

namespace app\api\service;


use app\lib\excel\Excel as ExcelLib;
use app\api\validate\ExcelValidate;
use app\lib\exception\FileUploadException;

class Excel extends File
{
    /**
     * 上传文件excel文件
     * @param $file 上传的文件
     * @param int $isOriginal 0不使用原文件名/1使用原文件名
     * @param int $OssOrLocal 1本地存储/2阿里云OSS存储
     * @return mixed
     * @throws FileUploadException
     * @throws \OSS\Core\OssException
     */
    public static function excelUp($file, $isOriginal = 0, $OssOrLocal = 1)
    {
        $fileInfo = $file->getInfo();
        self::checkExcel($fileInfo);

        $saveFilePath = config('program.static_excel');
        $prefix = config('program.excel_prefix');
        $f = self::upload($fileInfo, $isOriginal, $OssOrLocal, $saveFilePath, $prefix);
        // $f = ['url'=> '', 'fileName'=> ''];
        return $f;
    }

    private static function checkExcel($fileInfo)
    {
        $info['size']   = self::getSize($fileInfo);
        $info['ext']    = self::getExt($fileInfo);
        $info['tmp_name'] = $fileInfo['tmp_name'];
        (new ExcelValidate())->checkParam($info);
        return $info;
    }


    /**
     * 获取excel数据
     * @param $file  上传的文件
     * @param array $head  列表头转换到数据库的字段
     * @param int $save  是否需要保存文件
     * @param int $isOriginal 保存文件是否是使用原文件名 0随机名/1原文件名
     * @param int $OssOrLocal 保存在本地还是OSS 1本地/2OSS
     * @return array
     * @throws FileUploadException
     * @throws \OSS\Core\OssException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public static function getExcelData($file, $head = [], $save = 0, $isOriginal = 0, $OssOrLocal = 1)
    {
        $fileInfo = $file->getInfo();
        if($save){
            $f = self::excelUp($file,$isOriginal = 0, $OssOrLocal = 1);
//            $fileName = $f['url'];
            $ext = self::getExt($fileInfo);
            $fileName = self::getTmpName($fileInfo);
        } else {
            $info = self::checkExcel($fileInfo);
            $fileName = $info['tmp_name'];
            $ext = $info['ext'];
        }

        // $head = ['用户手机'=>'mobile'];
        $excelData = ExcelLib::getExcelData($head, $fileName, $ext);
        return $excelData;
    }


    /**
     * 把数据保存到表格
     * @param array $heads  表格头
     * @param array $keys   需要的数据字段
     * @param array $data   要保存的数据
     * @param string $fileName 文件名称
     * @param string $ext   文件后缀
     * @param string $title  sheet的标题
     * @return string  返回路径+文件名
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \app\lib\exception\ParameterException
     */
    public static function dataToExcel($heads = [], $keys = [], $data = [], $ext = 'xlsx', $fileName = '', $title = 'swg')
    {
//        $heads = [
//            'A' => 'ID',
//            'B' => '路由',
//            'C' => '版本',
//            'D' => '路由名称',
//            'E' => '路由描述',
//            'F' => '路由排序',
//            'G' => '路由状态',
//            'H' => 'create_time',
//            'I' => 'update_time',
//            'J' => 'delete_time',
//        ];
//        $keys = [
//            'id', 'auth_route', 'auth_route_version', 'auth_name',
//            'auth_desc', 'auth_order', 'auth_status', 'create_time',
//            'update_time', 'delete_time'
//        ];

        // 没有文件名使用随机名
        if(!$fileName){
            $fileName = self::randFileName();
        }

        return ExcelLib::dataToExcel($heads, $keys, $data, $fileName, $ext, $title);

//        return self::exportExcel($spreadsheet, 'xls', '登陆日志');
    }


    /**
     * 导出Excel
     * @param  string $format       格式:excel2003 = xls, excel2007 = xlsx
     * @param  string $savename     保存的文件名
     * @return filedownload         浏览器下载
     */
    /**
     * 下载文件
     * @param $filename 文件名(全路径)
     */
    public static function exportExcel($filename)
    {
//        if ($format == 'xls') {
//            //输出Excel03版本
//            header('Content-Type:application/vnd.ms-excel');
//        } elseif ($format == 'xlsx') {
//            //输出07Excel版本
//            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//        }
        //输出名称
        header('Content-Disposition:attachment;filename=' . basename($filename) );
        header('content-length:'. filesize($filename));
        //禁止缓存
        header('Cache-Control: max-age=0');
        readfile($filename);
//        unlink($filePath);

//        // Redirect output to a client’s web browser (Xlsx)
//        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//        header('Content-Disposition: attachment;filename="01simple.xlsx"');
//        header('Cache-Control: max-age=0');
//        // If you're serving to IE 9, then the following may be needed
//        header('Cache-Control: max-age=1');
//
//        // If you're serving to IE over SSL, then the following may be needed
//        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
//        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
//        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
//        header('Pragma: public'); // HTTP/1.0
    }

}