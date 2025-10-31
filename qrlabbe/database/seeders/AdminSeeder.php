// database/seeders/AdminSeeder.php
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Perintah ini akan "cari email, kalau ga ada, baru buat"
        // Ini mencegah duplikat kalau seedernya dijalankan berkali-kali
        User::firstOrCreate(
            ['email' => 'admin@lab.com'], // Kunci pencarian
            [
                'name' => 'Admin Lab',
                'password' => Hash::make('admin123'),
                'role' => 'admin'
            ]
        );
    }
}