<?php
namespace App\Helpers;

use App\Jobs\UploadAssetsFromLocalToS3StorageJob;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Leafo\ScssPhp\Compiler;
use App;

class S3Helper
{
    /**
     * Compiled local scss file and generate style.css file
     *
     * @param string $tenantName
     * @return mix
     */
    public static function compileLocalScss($tenantName, $options = [])
    {
        try {
            $scss = new Compiler();
            $scss->addImportPath(realpath(storage_path().'\app\\'.$tenantName.'\assets\scss'));
            
            $importScss = '@import "_variables";';
            
            // Color set & other file || Color set & no file

            if ((isset($options['primary_color']) && $options['isVariableScss'] == 0)) {
                $importScss .= '$primary: '.$options['primary_color'].';';
            }
            
            $importScss .= '@import "custom";
                            @import "../../../../node_modules/bootstrap/scss/bootstrap";
                            @import "../../../../node_modules/bootstrap-vue/src/index";';

            $css = $scss->compile($importScss);
            
            // Delete if folder is already there
            if (Storage::disk('local')->exists($tenantName.'\assets\css\style.css')) {
                // Delete existing one
                Storage::disk('local')->delete($tenantName.'\assets\css\style.css');
            }

            // Put compiled css file into local storage
            if (Storage::disk('local')->put($tenantName.'\assets\css\style.css', $css)) {

                // Copy default theme folder to tenant folder on s3
                dispatch(new UploadAssetsFromLocalToS3StorageJob($tenantName));
                /*if(!Self::uploadLocalCompiledFileOnS3Folder($tenantName)){
                    return Helpers::errorResponse(trans('messages.status_code.HTTP_STATUS_422'),
                                        trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                                        trans('messages.custom_error_code.ERROR_10010'),
                                        trans('messages.custom_error_message.10010'));
                }*/
            }
        } catch (\Exception $e) {
            return Helpers::errorResponse(
                trans('messages.status_code.HTTP_STATUS_422'),
                trans('messages.status_type.HTTP_STATUS_TYPE_422'),
                trans('messages.custom_error_code.ERROR_10010'),
                trans('messages.custom_error_message.10010')
            );
        }

        // Set response data
        $apiStatus = app('Illuminate\Http\Response')->status();
        $apiMessage = trans('api_success_messages.success.CSS_COMPILED_SUCESSFULLY');

        return Helpers::response($apiStatus, $apiMessage);
    }
}
