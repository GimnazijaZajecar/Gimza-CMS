<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Почетни подаци преузети са сајта www.gimza.edu.rs:
 *  - SchoolSeeder: одељења, предмети и запослени (налози),
 *  - ContentSeeder: категорије, новости, пројекти, странице и мени,
 *  - GallerySeeder: подгалерије са линковима ка албумима.
 *
 * Покретање: php artisan migrate:fresh --seed
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Лозинка за све налоге из seeder-а (из .env или насумична).
     */
    public static string $password = '';

    public function run(): void
    {
        static::$password = config('gimza.admin.password') ?: Str::password(12, symbols: false);

        $admin = User::create([
            'first_name' => 'Администратор',
            'last_name' => 'сајта',
            'title' => 'Администратор сајта',
            'username' => config('gimza.admin.username'),
            'email' => config('gimza.admin.email'),
            'password' => static::$password,
            'role' => UserRole::Admin,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Сви записи се воде као да их је направио администратор.
        auth()->setUser($admin);

        $this->call([
            SchoolSeeder::class,
            ContentSeeder::class,
            GallerySeeder::class,
        ]);

        if ($this->command) {
            $example = User::query()->where('role', UserRole::Profesor)->whereHas('subjects')->orderBy('id')->first();

            $this->command->newLine();
            $this->command->info('Налози за пријаву на /admin:');
            $this->command->table(['Улога', 'Имејл', 'Лозинка'], [
                ['Администратор', $admin->email, static::$password],
                ['Професор (пример)', $example?->email, static::$password],
            ]);
            $this->command->line('Сви запослени имају налог име.презиме@gimza.test и исту лозинку (нпр. '.$example?->email.').');

            if (! config('gimza.admin.password')) {
                $this->command->warn('Лозинка је насумична. Упишите ADMIN_PASSWORD у .env ако желите своју.');
            }
        }
    }
}
