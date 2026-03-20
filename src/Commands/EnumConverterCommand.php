<?php

namespace GearboxSolutions\EnumConverter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EnumConverterCommand extends Command
{
    public $signature = 'convert-enums {--js : convert to JS Enum } {--force : Force processing of enums}';

    public $description = 'Convert PHP enums to JS/TS enums';

    public function handle(): int
    {
        collect(config('enum-converter-laravel.enum_paths'))
            ->each(function ($outputPath, $path) {
                $files = File::allFiles(base_path($path));

                collect($files)->each(function ($file) use ($outputPath) {
                    if (! $this->option('force') && ! $this->hasFileChanged($file)) {
                        return;
                    }

                    $class = Str::of($file)->replace('.php', '')
                        ->replace(base_path(), '')
                        ->explode('/')
                        ->map(fn ($item) => Str::ucfirst($item))
                        ->implode('\\');

                    $items = collect($class::cases())->mapWithKeys(function ($item) {
                        return [$item->name => $item->value ?? $item->name];
                    });

                    $output = '';
                    if ($this->option('js')) {
                        $output = $this->convertToJSEnum($items, $outputPath);
                    } else {
                        $output = $this->convertToTSEnum($items, $outputPath);
                    }

                    $outputFile = Str::of($file->getFilename())
                        ->replace('.php', config('enum-converter-laravel.enum_extension'));

                    $name = class_basename($class);

                    $output = str_replace('%enumName%', $name, $output);

                    $this->info("Writing {$class} to file {$outputPath}/{$outputFile}");
                    File::put($outputPath.'/'.$outputFile, $output);
                });
            });

        return self::SUCCESS;
    }

    private function convertToTSEnum($items, $outputPath): string
    {
        $data = $items->map(function ($item, $key) {
            $item = $this->convertValue($item);

            return "    {$key}: {$item},";
        })->implode("\n");

        $data = "const %enumName% = {\n{$data}\n} as const;\n\nexport default %enumName%;\n\nexport type %enumName% = (typeof %enumName%)[keyof typeof %enumName%];";

        return $data;
    }

    private function convertToJSEnum($items): string
    {
        $data = $items->map(function ($item, $key) {
            $item = $this->convertValue($item);

            return "    {$key}: {$item},";
        })->implode("\n");

        $data = "export const %enumName% = Object.freeze({\n{$data}\n}";

        return $data;
    }

    private function convertValue($item)
    {
        return match (gettype($item)) {
            'string' => "\"$item\"",
            'integer', 'double' => $item,
            'NULL' => 'null',
            'boolean' => $item ? 'true' : 'false',
            default => $item,
        };
    }

    private function hasFileChanged($file)
    {
        if (! config('enum-converter-laravel.enable_file_hash_check', false)) {
            return true;
        }

        $fileHash = hash_file('sha256', $file->getPathname());

        $cacheKey = 'enum-converter-'.hash('sha256', $file->getPathname());

        $previousHash = Cache::get($cacheKey, '');

        if ($fileHash !== $previousHash) {
            Cache::put($cacheKey, $fileHash);

            return true;
        }

        return false;
    }
}
