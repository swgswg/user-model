<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/11
 * Time: 17:16
 */

namespace app\lib\excel;

use app\lib\exception\FileUploadException;
use app\lib\exception\ParameterException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Excel
{

    /**
     * 把数据导入到表格
     * @param array $heads 表格列表头
     * @param array $keys  要导入表格的数据列
     * @param array $data  要导入表格的数据
     * @param string $fileName 要生成的文件名称
     * @param string $ext  文件后缀(xlsx 2007格式/xls 2003格式)
     * @param string $title  表格sheet标题
     * @return string  返回路径+文件名
     * @throws ParameterException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public static function dataToExcel( $heads = [], $keys=[], $data=[], $fileName = 'Created by SWG', $ext = 'xlsx', $title = 'swg')
    {
        /**
        $heads = ['A'=> 'aaaa','B'=> 'bbbb'];

        $keys = ['a', 'b', 'c'];
         */
//        $heads = [
//            'ID',
//            '路由',
//            '版本',
//            '路由名称',
//            '路由描述',
//            '路由排序',
//            '路由状态',
//            'create_time',
//            'update_time',
//            'delete_time',
//        ];
//        $keys = [
//            'id', 'auth_route', 'auth_route_version', 'auth_name',
//            'auth_desc', 'auth_order', 'auth_status', 'create_time',
//            'update_time', 'delete_time'
//        ];
        if(!$heads || !$keys){
            throw new ParameterException([
                'message'=> '缺少表格头和表格字段数据',
            ]);
        }

        // 根据表头获取excel列表头字母
        $headsWorld = self::excelHeadWorld($heads);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //循环设置表头
        $i = 0;
        foreach ($heads as $v) {
            $sheet->setCellValue($headsWorld[$i]. '1', $v);
            $sheet->getColumnDimension($headsWorld[$i])->setWidth(20);
            ++$i;
        }

        // Rename worksheet
        $sheet->setTitle($title);

        if($data){
            $i = 2;
            foreach ($data as $k => $v) {
                // Add data
                foreach ($headsWorld as $kk => $vv){
//                    var_dump([$vv.$i, $v[$keys[$kk]]]);
                    if(isset($keys[$kk])){
                        $val = $v[$keys[$kk]];
                    } else {
                        $val = '';
                    }
                    $sheet->setCellValue($vv.$i, $val);
                }
                ++$i;
            }
        }
        // Set alignment
        // $spreadsheet->getActiveSheet()->getStyle('A1:K'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        // $spreadsheet->getActiveSheet()->getStyle('C2:C'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        if ($ext == 'xlsx') {
            //输出07Excel版本
            // $class = "Xlsx";
            $class = "PhpOffice\PhpSpreadsheet\Writer\Xlsx";
        } else {
            //输出Excel03版本
            // $class = "Xls";
            $class = "PhpOffice\PhpSpreadsheet\Writer\Xls";
        }
        $writer = new $class($spreadsheet);
        // $writer = new Xlsx($spreadsheet);
        $path = config('program.static_excel'). $fileName . '.' . $ext;
        $writer->save($path);
        // 清除内存
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return config('program.excel_prefix'). $fileName . '.' . $ext;

    }

    private static function excelHeadWorld($head)
    {
        $world = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G',
            'H', 'I', 'J', 'K', 'L', 'M', 'N',
            'O', 'P', 'Q', 'R', 'S', 'T', 'U',
            'V', 'W', 'X', 'Y', 'Z'
        ];
        $count = count($head);
        $i = floor($count/25) - 1;
        $j = $count % 25;
        self::getWorldArr($world, $i, $j, $data);
        return $data;
    }

    private static function getWorldArr($world, $i, $j,&$data)
    {
        if($i < -1){
            return;
        }
        if(!$data){
            $data = [];
        }
        if($i < 0){
            $a = '';
        } else {
            $a = $world[$i];
        }
        for($k = $j - 1; $k >= 0; $k--){
            array_unshift($data, $a . $world[$k]);
        }
        self::getWorldArr($world,$i - 1,26, $data);
    }

    /**
     * 获取excel表格数据
     * @param array $head 表格列头对应的要获取的数据
     * @param string $file 表格文件位置
     * @param string $typt  表格文件类型(默认xlsx)
     * @return array  返回表格数据 二维数组
     * @throws FileUploadException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public static function getExcelData($head = [] , $file = '', $typt = 'xlsx')
    {
        // $uploadfile = config('program.static_excel').'Created by SWG.xlsx';
        // $inputFileType = IOFactory::identify($uploadfile); //传入Excel路径
        // $objReader = IOFactory::createReader($inputFileType);
        // $objPHPExcel = $objReader->load($uploadfile);

        if(!$head){
            throw new FileUploadException([
                'message'=> '获取表格数据缺少head字段',
                'errorCode' => 60007
            ]);
        }

        if ($typt == 'xlsx') {
            //输出07Excel版本
            $typt = "Xlsx";
        } else {
            //输出Excel03版本
            $typt = "Xls";
        }
        // 有Xls和Xlsx格式两种
        $objReader = IOFactory::createReader($typt);

        // load($filename)可以是上传的表格，或者是指定的表格
        $objPHPExcel = $objReader->load($file);

        //excel中的第一张sheet
        $sheet = $objPHPExcel->getSheet(0);
        $data = $sheet->toArray();
        // 去掉列表头
        $excelHead = array_shift($data);
        $keys = self::keys($head, $excelHead);
        $value = [];
        foreach ($data as $k=>$v){
            $tmp = [];
            foreach ($keys as $kk=>$vv){
                $tmp[$kk] = $v[$vv];
            }
            $value[] = $tmp;
        }
        return $value;
        // 取得总行数
        // $highestRow = $sheet->getHighestRow();

        // 取得总列数
        // $highestColumn = $sheet->getHighestColumn();

    }


    // 变化列表头数据
    private static function keys($head, $excelHead)
    {
        // $head = ['权限路由' => 'auth_route']
        // $excelHead = [0=>'权限路由']
        $key = [];
        foreach ($excelHead as $k=>$v){
            if(array_key_exists($v,$head)){
                $key[$head[$v]] = $k;
            }
        }
        // $keys = ['auth_route'=>0]
        return $key;
    }

}