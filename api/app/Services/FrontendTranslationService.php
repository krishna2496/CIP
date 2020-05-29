<?php

namespace App\Services;

use App\Helpers\S3Helper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

final class FrontendTranslationService
{
    /**
     * @var S3Helper
     */
    private $s3Helper;

    /**
     * FrontendTranslationService constructor.
     */
    public function __construct(S3Helper $s3Helper)
    {
        $this->s3Helper = $s3Helper;
    }

    /**
     * @param string $tenantName
     * @param string $isoCode
     * @return Collection
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getTranslationsForLanguage(string $tenantName, string $isoCode)
    {
        // Retrieve the default translations
        $defaultTranslations = Storage::disk('resources')->get("frontend/translations/${isoCode}.json");
        $translations = collect(json_decode($defaultTranslations, true));

        /*
         * Check for any custom translation.
         * If no don't find any, we can
         * return the default ones.
         */
        $cdnStorage = Storage::disk('s3');
        $customTranslationsPath = $this->s3Helper->getCustomLanguageFilePath($tenantName, $isoCode);
        if (!$cdnStorage->exists($customTranslationsPath)) {
            return $translations;
        }

        // Otherwise we'll download and merge the custom translations with the default ones
        $customTranslationsJson = $cdnStorage->get($customTranslationsPath);
        $customTranslations = collect(json_decode($customTranslationsJson, true));
        $mergedTranslations = collect();
        $translations->each(function ($translationsGroup, $translationsGroupName) use ($customTranslations, $mergedTranslations) {
            $mergedTranslationsGroup = collect($translationsGroup);

            if ($customTranslations->keys()->contains($translationsGroupName)) {
                $mergedTranslationsGroup = $mergedTranslationsGroup->merge($customTranslations->get($translationsGroupName));
            }

            $mergedTranslations->put($translationsGroupName, $mergedTranslationsGroup);
        });

        return $mergedTranslations;
    }
}
