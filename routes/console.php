<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:sync-staging', function () {
    $this->info('Starting database sync for staging/server...');

    $this->comment('1. Running pending migrations...');
    $this->call('migrate', ['--force' => true]);

    $this->comment('2. Ensuring reference data (grade levels, categories)...');
    $this->call('db:seed', ['--class' => 'ReferenceDataSeeder', '--force' => true]);

    $this->comment('3. Seeding Director profiles...');
    $this->call('db:seed', ['--class' => 'DirectorSeeder', '--force' => true]);

    $this->comment('4. Seeding Admission Fees...');
    $this->call('db:seed', ['--class' => 'AdmissionFeeSeeder', '--force' => true]);

    $this->comment('5. Seeding Mandarin Programs...');
    $this->call('db:seed', ['--class' => 'MandarinProgramSeeder', '--force' => true]);

    $this->comment('6. Ensuring storage symlink...');
    $this->call('storage:link');

    $this->comment('7. Clearing optimize / view caches...');
    $this->call('optimize:clear');

    $this->info('All initial content and migrations synced successfully!');
})->purpose('Migrate, seed missing initial content (Directors, Fees, Mandarin Programs), link storage, and clear cache.');
