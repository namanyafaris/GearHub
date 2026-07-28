<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
class SyncImagesToR2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'r2:sync';

    protected $description = 'Upload all existing local images in storage/app/public to Cloudflare R2';

    public function handle()
    {
        $this->info('Memulai sinkronisasi gambar ke Cloudflare R2...');

        // Ambil semua file dari disk 'public' (penyimpanan lokal)
        $files = Storage::disk('public')->allFiles();
        
        $totalFiles = count($files);
        if ($totalFiles === 0) {
            $this->warn('Tidak ada file yang ditemukan di penyimpanan lokal (public disk).');
            return;
        }

        $bar = $this->output->createProgressBar($totalFiles);
        $bar->start();

        foreach ($files as $file) {
            // Abaikan file tersembunyi (misalnya .gitignore)
            if (str_starts_with(basename($file), '.')) {
                $bar->advance();
                continue;
            }

            $contents = Storage::disk('public')->get($file);
            $mimeType = Storage::disk('public')->mimeType($file);

            // Upload ke R2
            Storage::disk('s3')->put($file, $contents, [
                'visibility' => 'public',
                'ContentType' => $mimeType
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Sinkronisasi selesai! Semua ' . $totalFiles . ' file berhasil diunggah ke R2.');
    }
}
