<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Storage;
use ScssPhp\ScssPhp\Compiler;

class UpdateStyleSettingsJob extends Job
{
    private const SCSS_PATH = '/assets/scss';
    private const COMPILED_STYLES_CSS_PATH = '\assets\css\style.css';

    /**
     * @var string
     */
    private $tenantName;

    /**
     * @var array
     */
    private $options;

    /**
     * @var ?string
     */
    private $fileName;

    /**
     * UpdateStyleSettingsJob constructor.
     * @param string $tenantName
     * @param array $options
     * @param string|null $fileName
     */
    public function __construct(string $tenantName, array $options, ?string $fileName)
    {
        $this->tenantName = $tenantName;
        $this->options = $options;
        $this->fileName = $fileName;
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        /*
         * The compiling must happen locally. For this reason, we must create a temporary folder
         * to allow the system to compile the custom styles.
         *
         * How does it work ?
         * - If nothing exist locally, we should try first to download the custom styles from
         * the customer on S3 bucket.
         * - If nothing already exist yet on S3, copy the default styles in the temporary folder
         * - Update the file, primary or secondary color that might have been sent with the request.
         * - Trigger the compilation
         * - Push the updated compiled style.css to S3 customer assets folder.
         */
        $tenantStylesExistLocally = Storage::disk('local')->exists($this->tenantName);
        if (!$tenantStylesExistLocally) {
            $defaultThemeFolderName = env('AWS_S3_DEFAULT_THEME_FOLDER_NAME');
            $stylesheets = Storage::disk('s3')->allFiles($this->tenantName . self::SCSS_PATH)
                ?: Storage::disk('local')->allFiles($defaultThemeFolderName . self::SCSS_PATH);

            // $stylesheets will never be empty
            $filesComeFromS3 = strpos($stylesheets[0], $this->tenantName) !== false;

            foreach ($stylesheets as $stylesheet) {
                $destinationPath = str_replace($defaultThemeFolderName, $this->tenantName, $stylesheet);
                $file = $filesComeFromS3 ? Storage::disk('s3')->get($stylesheet) : Storage::disk('local')->get($stylesheet);
                Storage::disk('local')->put($destinationPath, $file);
            }
        }

        // if we update a file, we need to load it from S3 to allow compiling with the recent changes
        if (!empty($this->fileName)) {
            $filePath = $this->tenantName . self::SCSS_PATH . '/' . $this->fileName;
            $file = Storage::disk('s3')->get($filePath);
            Storage::disk('local')->put($filePath, $file);
        }

        // Second compile SCSS files and upload generated CSS file on S3
        $css = $this->compileLocalScss();

        // Push compiled styles to S3
        Storage::disk('s3')->put($this->tenantName . self::COMPILED_STYLES_CSS_PATH, $css);
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function compileLocalScss()
    {
        $scss = new Compiler();
        $scss->addImportPath(realpath(storage_path() . '/app/' . $this->tenantName . self::SCSS_PATH));

        $assetUrl = 'https://'
            . env('AWS_S3_BUCKET_NAME')
            . '.s3.'
            . env('AWS_REGION')
            . '.amazonaws.com/'
            . $this->tenantName
            . '/assets/images';

        $importScss = '@import "_variables";';

        // Color set & other file || Color set & no file
        if ((isset($this->options['primary_color']) && $this->options['isVariableScss'] === 0)) {
            $importScss .= '$primary: ' . $this->options['primary_color'] . ';';
        }

        $importScss .= '@import "_assets";
        $assetUrl: "'.$assetUrl.'";
        @import "' . base_path() . '/node_modules/bootstrap/scss/bootstrap";
        @import "' . base_path() . '/node_modules/bootstrap-vue/src/index";
        @import "custom";';

        return $scss->compile($importScss);
    }
}
