<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Backup database PostgreSQL harian';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Buat nama file dengan tanggal & jam sekarang
        //    Contoh hasil: backup_2024-01-15_01-00-00.sql
        $date     = now()->format('Y-m-d_H-i-s');
        $filename = "backup_{$date}.sql";

        // 2. Tentukan folder penyimpanan backup
        //    Lokasi: storage/app/backups/
        $backupDir = storage_path('app/backups');

        // 3. Buat folder jika belum ada
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        // 4. Ambil konfigurasi database dari .env otomatis
        $host     = env('DB_HOST', '127.0.0.1');  // localhost
        $port     = env('DB_PORT', '5432');         // 5433 (dari .env kamu)
        $database = env('DB_DATABASE');             // verifikasi_rslngbadak
        $username = env('DB_USERNAME');             // postgres
        $password = env('DB_PASSWORD');             // 123456

        // 5. Set password sebagai environment variable
        //    (cara aman agar password tidak muncul di log sistem)
        putenv("PGPASSWORD={$password}");

        // 6. Buat perintah pg_dump untuk export database
        $outputPath = "{$backupDir}/{$filename}";
        $command = "pg_dump -U {$username} -h {$host} -p {$port} {$database} > \"{$outputPath}\"";

        // 7. Jalankan perintah backup
        $this->info("⏳ Sedang membackup database '{$database}'...");
        exec($command, $output, $returnCode);

        // 8. Cek apakah berhasil atau gagal
        if ($returnCode === 0) {
            $this->info("✅ Backup berhasil disimpan!");
            $this->info("📁 Lokasi: {$outputPath}");

            // 9. Hapus otomatis backup yang lebih dari 7 hari
            $this->hapusBackupLama($backupDir);
        } else {
            $this->error('❌ Backup GAGAL!');
            $this->error('Pastikan pg_dump sudah terinstall dan bisa diakses.');
        }
    }

    private function hapusBackupLama(string $dir)
    {
        $this->info("🧹 Memeriksa backup lama...");

        $files       = glob("{$dir}/*.sql");
        $batasWaktu  = now()->subDays(7)->timestamp;
        $totalHapus  = 0;

        foreach ($files as $file) {
            if (filemtime($file) < $batasWaktu) {
                unlink($file);
                $this->warn("🗑️  Dihapus: " . basename($file));
                $totalHapus++;
            }
        }

        if ($totalHapus === 0) {
            $this->info("✅ Tidak ada backup lama yang perlu dihapus.");
        }
    }
}
