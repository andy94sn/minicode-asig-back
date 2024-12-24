<?php

namespace App\Models;

use App\Enums\TranslationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'key',
        'type',
        'value',
        'description'
    ];

    protected $casts = [
        'value' => 'array',
        'type' => TranslationType::class
    ];

    public function getTranslation(string $locale)
    {
        return $this->value[$locale] ?? null;
    }

    /**
     *
     * @param string $group
     * @param string $locale
     * @return array
     */
    public static function getTranslationsByGroup(string $group, string $locale): array
    {
        $cacheKey = "translations_{$group}_{$locale}";

        return Cache::remember($cacheKey, 60, function () use ($group, $locale) {
            $group = Group::where('slug', $group)->first();

            if (!$group) {
                return [];
            }

            $translations = $group->translations;

            if ($translations->isEmpty()) {
                return [];
            }

            return $translations->mapWithKeys(function ($translation) use ($locale) {
                return [$translation->key => $translation->value[$locale] ?? null];
            })->toArray();
        });
    }

    public static function getTranslationByKeyAndGroup(string $group, string $key): ?Translation
    {
        $cacheKey = "translations_{$group}_{$key}";

        return Cache::remember($cacheKey, 60, function () use ($group, $key) {
            $group = Group::where('slug', $group)->first();

            if (!$group) {
                return null;
            }

            return $group->translations()->where('key', $key)->first();
        });
    }

    public static function addTranslation(string $group, string $key, array $translations, string $description, string $type): ?array
    {
        $group = Group::where('id', $group)->first();
        $translationType = TranslationType::from($type);
        $values = [];

        if (!$group) {
            return [
                "status" => false,
                "message" => "Grup nu există"
            ];
        }

        foreach ($translations as $locale => $translation) {
            if (!is_string($translation)) {
                return null;
            }

            $values[$locale] = $translation;
        }

        $translation = Translation::create([
            'key' => $key,
            'type' => $translationType->value,
            'value' => $values,
            'description' => $description,
        ]);

        $group->translations()->attach($translation->id);

        Cache::forget("translations_{$group}_{$key}");

        return [
            "status" => true,
            "translation" => $translation
        ];
    }

    public static function updateTranslation(string $group, string $key, array $translations, string $description): ?Translation
    {
        $group = Group::where('slug', $group)->first();

        if (!$group){
            return null;
        }

        $existTranslation = Translation::where('group', $group->slug)->where('key', $key)->first();

        if (!$existTranslation) {
            return null;
        }

        $values = [];

        foreach ($translations as $locale => $translation) {
            if (!is_string($translation)) {
                return null;
            }

            $values[$locale] = $translation;
        }

        $translation->value  = $values;
        $translation->description = $description;
        $translation->save();

        Cache::forget("translations_{$group}_{$key}");
        Cache::put("translations_{$group}_{$key}", $translation, 60);
        Cache::forget("translations_{$group}");

        return $translation;
    }

    public static function deleteTranslation(string $group, string $key): bool
    {
        $group = Group::where('slug', $group)->first();

        if (!$group) {
            return false;
        }

        $translation = Translation::where('group', $group->slug)->where('key', $key)->first();

        if (!$translation) {
            return false;
        }

        $group->translations()->detach($translation->id);
        $translation->delete();


        Cache::forget("translations_{$group}_{$key}");
        Cache::forget("translations_{$group}");

        return true;
    }

    public static function getAllGroupsWithTranslations(): array
    {
        $cacheKey = "translations";

        return Cache::remember($cacheKey, 60, function () {
            return Group::has('translations')
                ->with('translations')
                ->get()
                ->toArray();
        });
    }

    public static function validateTranslation(string $key): bool
    {
        $existingTranslation = Translation::where('key', $key)->first();

        if ($existingTranslation){
            return true;
        }

        return false;
    }

    /**
     *
     * @param string $group
     * @return array
     */
    public static function getAllTranslationsByGroup(string $group): array
    {
        $cacheKey = "translations_{$group}";

        return Cache::remember($cacheKey, 60, function () use ($group) {
            $group = Group::where('slug', $group)->first();

            if (!$group) {
                return [];
            }

            $translations = $group->translations;

            $result = $translations->mapWithKeys(function ($translation) {
                return [$translation->key => $translation->value];
            });

            return $result->toArray();
        });
    }

    public function groups(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_translation');
    }
}
