<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;

class RehashStaffPasswords extends Command
{
    protected $signature = 'staff:rehash-passwords';
    protected $description = 'Rehash all staff passwords using Bcrypt';

    public function handle()
    {
        $staffMembers = Staff::all();

        foreach ($staffMembers as $staff) {
            // Skip if already bcrypt (starts with $2y$)
            if (!str_starts_with($staff->password, '$2y$')) {
                $staff->password = Hash::make($staff->password);
                $staff->save();
                $this->info("Rehashed password for staff ID {$staff->id}");
            }
        }

        $this->info('All staff passwords rehashed successfully.');
    }
}