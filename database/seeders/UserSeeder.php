<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'id' => Str::uuid(),
            'name' => 'System Administrator',
            'email' => 'admin@lexauniversity.edu',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'department' => 'Administration',
            'phone' => '+1234567890',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create Sample Students
        $students = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@student.lexauniversity.edu',
                'matric_no' => '02200470001',
                'department' => 'Computer Science',
                'phone' => '+1234567891',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@student.lexauniversity.edu',
                'matric_no' => '02200470002',
                'department' => 'Engineering',
                'phone' => '+1234567892',
            ],
            [
                'name' => 'Michael Johnson',
                'email' => 'michael.johnson@student.lexauniversity.edu',
                'matric_no' => '02200470003',
                'department' => 'Business Administration',
                'phone' => '+1234567893',
            ],
            [
                'name' => 'Sarah Wilson',
                'email' => 'sarah.wilson@student.lexauniversity.edu',
                'matric_no' => '02200470004',
                'department' => 'Medicine',
                'phone' => '+1234567894',
            ],
            [
                'name' => 'David Brown',
                'email' => 'david.brown@student.lexauniversity.edu',
                'matric_no' => '02200470005',
                'department' => 'Law',
                'phone' => '+1234567895',
            ],
        ];

        foreach ($students as $student) {
            User::create([
                'id' => Str::uuid(),
                'name' => $student['name'],
                'email' => $student['email'],
                'matric_no' => $student['matric_no'],
                'password' => Hash::make('password'),
                'role' => 'student',
                'department' => $student['department'],
                'phone' => $student['phone'],
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
        }
    }
}
