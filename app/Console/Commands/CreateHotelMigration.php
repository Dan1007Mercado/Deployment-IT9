<?php
// app/Console/Commands/CreateHotelMigration.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateHotelMigration extends Command
{
    protected $signature = 'make:hotel-migration 
                            {table : The table name (guests, room_types, rooms, reservations, bookings, payments, sales)}';

    protected $description = 'Create a migration file for hotel management system tables';

    protected $migrationTemplates = [
        'guests' => [
            'table' => 'guests',
            'timestamp' => '2024_01_01_000001',
            'columns' => [
                '$table->id(\'guest_id\');',
                '$table->string(\'first_name\', 100);',
                '$table->string(\'last_name\', 100);',
                '$table->string(\'contact_number\', 20);',
                '$table->string(\'email\', 150);',
                '$table->timestamp(\'date_registered\')->useCurrent();',
                '$table->enum(\'guest_type\', [\'walk-in\', \'advance\'])->default(\'walk-in\');',
                '$table->timestamps();',
                '$table->index([\'last_name\', \'first_name\']);',
                '$table->index(\'email\');',
            ]
        ],
        'room_types' => [
            'table' => 'room_types',
            'timestamp' => '2024_01_01_000002',
            'columns' => [
                '$table->id(\'room_type_id\');',
                '$table->string(\'type_name\', 100);',
                '$table->text(\'description\')->nullable();',
                '$table->integer(\'capacity\');',
                '$table->decimal(\'base_price\', 10, 2);',
                '$table->text(\'amenities\')->nullable();',
                '$table->integer(\'total_rooms\')->default(0);',
                '$table->timestamps();',
                '$table->unique(\'type_name\');',
            ]
        ],
        'rooms' => [
            'table' => 'rooms',
            'timestamp' => '2024_01_01_000003',
            'columns' => [
                '$table->id(\'room_id\');',
                '$table->foreignId(\'room_type_id\')->constrained(\'room_types\', \'room_type_id\');',
                '$table->string(\'room_number\', 10);',
                '$table->string(\'floor\', 10);',
                '$table->enum(\'room_status\', [\'available\', \'occupied\', \'cleaning\', \'maintenance\'])->default(\'available\');',
                '$table->timestamps();',
                '$table->unique(\'room_number\');',
                '$table->index(\'room_status\');',
                '$table->index([\'room_type_id\', \'room_status\']);',
            ]
        ],
        'reservations' => [
            'table' => 'reservations',
            'timestamp' => '2024_01_01_000004',
            'columns' => [
                '$table->id(\'reservation_id\');',
                '$table->foreignId(\'guest_id\')->constrained(\'guests\', \'guest_id\');',
                '$table->foreignId(\'room_type_id\')->constrained(\'room_types\', \'room_type_id\');',
                '$table->date(\'check_in_date\');',
                '$table->date(\'check_out_date\');',
                '$table->integer(\'num_guests\');',
                '$table->integer(\'num_nights\')->virtualAs(\'DATEDIFF(check_out_date, check_in_date)\');',
                '$table->decimal(\'total_amount\', 10, 2);',
                '$table->enum(\'status\', [\'pending\', \'confirmed\', \'cancelled\', \'completed\'])->default(\'pending\');',
                '$table->timestamp(\'reservation_date\')->useCurrent();',
                '$table->enum(\'reservation_type\', [\'walk-in\', \'advance\'])->default(\'walk-in\');',
                '$table->timestamps();',
                '$table->index([\'check_in_date\', \'check_out_date\']);',
                '$table->index(\'status\');',
                '$table->index([\'room_type_id\', \'status\']);',
            ]
        ],
        'bookings' => [
            'table' => 'bookings',
            'timestamp' => '2024_01_01_000005',
            'columns' => [
                '$table->id(\'booking_id\');',
                '$table->foreignId(\'reservation_id\')->constrained(\'reservations\', \'reservation_id\');',
                '$table->foreignId(\'room_id\')->constrained(\'rooms\', \'room_id\');',
                '$table->timestamp(\'actual_check_in\')->nullable();',
                '$table->timestamp(\'actual_check_out\')->nullable();',
                '$table->enum(\'booking_status\', [\'reserved\', \'checked-in\', \'checked-out\', \'cancelled\', \'no-show\'])->default(\'reserved\');',
                '$table->timestamp(\'booking_date\')->useCurrent();',
                '$table->timestamps();',
                '$table->unique(\'reservation_id\');',
                '$table->index(\'booking_status\');',
                '$table->index([\'room_id\', \'booking_status\']);',
            ]
        ],
        'payments' => [
            'table' => 'payments',
            'timestamp' => '2024_01_01_000006',
            'columns' => [
                '$table->id(\'payment_id\');',
                '$table->foreignId(\'booking_id\')->constrained(\'bookings\', \'booking_id\');',
                '$table->decimal(\'amount\', 10, 2);',
                '$table->timestamp(\'payment_date\')->useCurrent();',
                '$table->enum(\'payment_method\', [\'cash\', \'credit_card\', \'debit_card\', \'online_transfer\', \'mobile_payment\']);',
                '$table->string(\'transaction_id\', 100)->nullable();',
                '$table->enum(\'payment_status\', [\'pending\', \'completed\', \'failed\', \'refunded\'])->default(\'pending\');',
                '$table->string(\'sandbox_reference\', 100)->nullable();',
                '$table->timestamps();',
                '$table->index(\'payment_method\');',
                '$table->index(\'payment_status\');',
                '$table->index(\'transaction_id\');',
                '$table->index([\'booking_id\', \'payment_status\']);',
            ]
        ],
        'sales' => [
            'table' => 'sales',
            'timestamp' => '2024_01_01_000007',
            'columns' => [
                '$table->id(\'sale_id\');',
                '$table->foreignId(\'booking_id\')->constrained(\'bookings\', \'booking_id\');',
                '$table->decimal(\'room_revenue\', 10, 2);',
                '$table->integer(\'nights_sold\');',
                '$table->date(\'sale_date\');',
                '$table->timestamps();',
                '$table->unique(\'booking_id\');',
                '$table->index(\'sale_date\');',
                '$table->index([\'sale_date\', \'room_revenue\']);',
            ]
        ]
    ];

    public function handle()
    {
        $tableName = strtolower($this->argument('table'));

        if (!array_key_exists($tableName, $this->migrationTemplates)) {
            $this->error("Invalid table name: {$tableName}");
            $this->info("Available tables: " . implode(', ', array_keys($this->migrationTemplates)));
            return 1;
        }

        $template = $this->migrationTemplates[$tableName];
        
        // Use fixed timestamp for correct order
        $migrationName = "{$template['timestamp']}_create_{$template['table']}_table";
        $fileName = "{$migrationName}.php";
        $filePath = database_path("migrations/{$fileName}");

        // Build the migration content
        $content = $this->buildMigrationContent($template);
        
        // Create the migration file
        File::put($filePath, $content);

        $this->info("✅ Migration created and populated for {$tableName} table!");
        $this->info("📁 File: {$fileName}");

        return 0;
    }

    protected function buildMigrationContent($template)
    {
        $columns = implode("\n            ", $template['columns']);
        
        return <<<MIGRATION
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('{$template['table']}', function (Blueprint \$table) {
            {$columns}
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('{$template['table']}');
    }
};
MIGRATION;
    }
}