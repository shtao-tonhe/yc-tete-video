<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: kane <chengjin005@163.com>
// +----------------------------------------------------------------------
namespace app\user\controller;

use cmf\controller\AdminBaseController;
use cmf\lib\Upload;
use think\facade\View;

/**
 * 附件上传控制器
 * Class Asset
 * @package app\asset\controller
 */
class AssetController extends AdminBaseController{

    public function initialize(){
        parent::initialize();
        $adminId = cmf_get_current_admin_id();
        $userId  = cmf_get_current_user_id();
        if (empty($adminId) && empty($userId)) {
            $this->error($Think.\lang('ILLEGAL_UPLOAD'));
        }
    }

    /**
     * webuploader 上传
     */
    public function webuploaderBF(){
        
        if ($this->request->isPost()) {

            $uploader = new Upload();

            $result = $uploader->upload();

            if ($result === false) {
                $this->error($uploader->getError());
            } else {
                $this->success($Think.\lang('UPLOAD_SUCCESSFUL'), '', $result);
            }

        } else {
            $uploadSetting = cmf_get_upload_setting();

            $arrFileTypes = [
                'image' => ['title' => 'Image files', 'extensions' => $uploadSetting['file_types']['image']['extensions']],
                'video' => ['title' => 'Video files', 'extensions' => $uploadSetting['file_types']['video']['extensions']],
                'audio' => ['title' => 'Audio files', 'extensions' => $uploadSetting['file_types']['audio']['extensions']],
                'file'  => ['title' => 'Custom files', 'extensions' => $uploadSetting['file_types']['file']['extensions']]
            ];

            $arrData = $this->request->param();
            if (empty($arrData["filetype"])) {
                $arrData["filetype"] = "image";
            }

            $fileType = $arrData["filetype"];

            if (array_key_exists($arrData["filetype"], $arrFileTypes)) {
                $extensions                = $uploadSetting['file_types'][$arrData["filetype"]]['extensions'];
                $fileTypeUploadMaxFileSize = $uploadSetting['file_types'][$fileType]['upload_max_filesize'];
            } else {
                $this->error($Think.\lang('UPLOAD_FILE_TYPE_ERROR'));
            }


            View::share('filetype', $arrData["filetype"]);
            View::share('extensions', $extensions);
            View::share('upload_max_filesize', $fileTypeUploadMaxFileSize * 1024);
            View::share('upload_max_filesize_mb', intval($fileTypeUploadMaxFileSize / 1024));
            $maxFiles  = intval($uploadSetting['max_files']);
            $maxFiles  = empty($maxFiles) ? 20 : $maxFiles;
            $chunkSize = intval($uploadSetting['chunk_size']);
            $chunkSize = empty($chunkSize) ? 512 : $chunkSize;
            View::share('max_files', $arrData["multi"] ? $maxFiles : 1);
            View::share('chunk_size', $chunkSize); //// 单位KB
            View::share('multi', $arrData["multi"]);
            View::share('app', $arrData["app"]);

            $content = hook_one('fetch_upload_view');

            $tabs = ['local', 'url', 'cloud'];

            $tab = !empty($arrData['tab']) && in_array($arrData['tab'], $tabs) ? $arrData['tab'] : 'local';

            if (!empty($content)) {
                $this->assign('has_cloud_storage', true);
            }

            if (!empty($content) && $tab == 'cloud') {
                return $content;
            }

            $tab = $tab == 'cloud' ? 'local' : $tab;

            $this->assign('tab', $tab);
            return $this->fetch(":webuploader");

        }
    }


    /**
     * webuploader 上传
     */
    public function webuploader(){
        
        if ($this->request->isPost()) {

            /*$allow_wj="jpg,JPG,png,PNG,gif,GIF,Gif,jpeg,JPEG";
            $allow=explode(",",$allow_wj);

            if (!get_file_suffix($_FILES['file']['name'],$allow)){
                $this->error("请上传正确格式的图片");
            }*/

            $files["file"]=$_FILES["file"];
            $type='img';

            $rs=adminUploadFiles($files,$type);

            if($rs['code']!=0){
                $this->error($rs['msg']);
                
            }

            $this->success($Think.\lang('UPLOAD_SUCCESSFUL'), '', $rs);

        } else {
            $uploadSetting = cmf_get_upload_setting();

            $arrFileTypes = [
                'image' => ['title' => 'Image files', 'extensions' => $uploadSetting['file_types']['image']['extensions']],
                'video' => ['title' => 'Video files', 'extensions' => $uploadSetting['file_types']['video']['extensions']],
                'audio' => ['title' => 'Audio files', 'extensions' => $uploadSetting['file_types']['audio']['extensions']],
                'file'  => ['title' => 'Custom files', 'extensions' => $uploadSetting['file_types']['file']['extensions']]
            ];

            $arrData = $this->request->param();
            if (empty($arrData["filetype"])) {
                $arrData["filetype"] = "image";
            }

            $fileType = $arrData["filetype"];

            if (array_key_exists($arrData["filetype"], $arrFileTypes)) {
                $extensions                = $uploadSetting['file_types'][$arrData["filetype"]]['extensions'];
                $fileTypeUploadMaxFileSize = $uploadSetting['file_types'][$fileType]['upload_max_filesize'];
            } else {
                $this->error($Think.\lang('UPLOAD_FILE_TYPE_ERROR'));
            }


            View::share('filetype', $arrData["filetype"]);
            View::share('extensions', $extensions);
            View::share('upload_max_filesize', $fileTypeUploadMaxFileSize * 1024);
            View::share('upload_max_filesize_mb', intval($fileTypeUploadMaxFileSize / 1024));
            $maxFiles  = intval($uploadSetting['max_files']);
            $maxFiles  = empty($maxFiles) ? 20 : $maxFiles;
            $chunkSize = intval($uploadSetting['chunk_size']);
            $chunkSize = empty($chunkSize) ? 512 : $chunkSize;
            View::share('max_files', $arrData["multi"] ? $maxFiles : 1);
            View::share('chunk_size', $chunkSize); //// 单位KB
            View::share('multi', $arrData["multi"]);
            View::share('app', $arrData["app"]);

            $content = hook_one('fetch_upload_view');

            $tabs = ['local', 'url', 'cloud'];

            $tab = !empty($arrData['tab']) && in_array($arrData['tab'], $tabs) ? $arrData['tab'] : 'local';

            if (!empty($content)) {
                $this->assign('has_cloud_storage', true);
            }

            if (!empty($content) && $tab == 'cloud') {
                return $content;
            }

            $tab = $tab == 'cloud' ? 'local' : $tab;

            $this->assign('tab', $tab);
            return $this->fetch(":webuploader");

        }
    }

}
