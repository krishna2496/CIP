<?php

namespace App\Jobs;

use App\Helpers\S3Helper;
use Illuminate\Support\Facades\Storage;
use ScssPhp\ScssPhp\Compiler;

class UpdateStyleSettingsJob extends Job
{
    private const SCSS_PATH = '/assets/scss';
    private const COMPILED_STYLES_CSS_PATH = '\assets\css\style.css';
    private const DEFAULT_THEME_SCSS_PATH = '/scss/default_theme/';

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

        $buildFolderName = $this->tenantName;
        $tenantScssFolderName = $this->tenantName . self::SCSS_PATH;
        $tenantCompiledCssFolderName = $this->tenantName . self::COMPILED_STYLES_CSS_PATH;

        // Create/empty the temporary directory
        Storage::disk('local')->deleteDirectory($buildFolderName);
        Storage::disk('local')->makeDirectory($buildFolderName);

        // Copy default theme SCSS in the temporary folder
        $defaultThemeScssFiles = Storage::disk('resources')->allFiles(self::DEFAULT_THEME_SCSS_PATH);
        foreach ($defaultThemeScssFiles as $scssFile) {
            $scssContent = Storage::disk('resources')->get($scssFile);
            Storage::disk('local')->put($buildFolderName . '/' . basename($scssFile), $scssContent);
        }

        // Download the tenant's custom SCSS from S3 into the temporary folder
        $customScssFiles = Storage::disk('s3')->allFiles($tenantScssFolderName);
        foreach ($customScssFiles as $customScssFile) {
            $customScssContent = Storage::disk('s3')->get($customScssFile);
            Storage::disk('local')->put($buildFolderName . '/' . basename($customScssFile), $customScssContent);
        }

        // Compile SCSS files
        $compiledCss = $this->compileLocalScss();

        // Push compiled styles to S3
        Storage::disk('s3')->put($tenantCompiledCssFolderName, $compiledCss);
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function compileLocalScss()
    {
        $scss = new Compiler();
        $scss->addImportPath(realpath(storage_path() . '/app/' . $this->tenantName . self::SCSS_PATH));

        $assetUrl = S3Helper::makeTenantS3BaseUrl($this->tenantName) . 'assets/images';

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
