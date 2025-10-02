<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure profile-photos directory exists
        Storage::disk('public')->makeDirectory('profile-photos');

        // Create Admin User
        $adminPhotoPath = $this->downloadImage(
            'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&crop=face',
            'admin-profile.jpg'
        );

        User::create([
            'id' => Str::uuid(),
            'name' => 'System Administrator',
            'email' => 'admin@lexauniversity.edu',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'department' => 'Administration',
            'phone' => '+1234567890',
            'status' => 'active',
            'photo' => $adminPhotoPath,
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
                'photo_url' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop&crop=face',
                'photo_name' => 'john-doe.jpg',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@student.lexauniversity.edu',
                'matric_no' => '02200470002',
                'department' => 'Engineering',
                'phone' => '+1234567892',
                'photo_url' => 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=400&h=400&fit=crop&crop=face',
                'photo_name' => 'jane-smith.jpg',
            ],
            [
                'name' => 'Michael Johnson',
                'email' => 'michael.johnson@student.lexauniversity.edu',
                'matric_no' => '02200470003',
                'department' => 'Business Administration',
                'phone' => '+1234567893',
                'photo_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&h=400&fit=crop&crop=face',
                'photo_name' => 'michael-johnson.jpg',
            ],
            [
                'name' => 'Sarah Wilson',
                'email' => 'sarah.wilson@student.lexauniversity.edu',
                'matric_no' => '02200470004',
                'department' => 'Medicine',
                'phone' => '+1234567894',
                'photo_url' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&h=400&fit=crop&crop=face',
                'photo_name' => 'sarah-wilson.jpg',
            ],
            [
                'name' => 'David Brown',
                'email' => 'david.brown@student.lexauniversity.edu',
                'matric_no' => '02200470005',
                'department' => 'Law',
                'phone' => '+1234567895',
                'photo_url' => 'https://images.unsplash.com/photo-1507591064344-4c6ce005b128?w=400&h=400&fit=crop&crop=face',
                'photo_name' => 'david-brown.jpg',
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@student.lexauniversity.edu',
                'matric_no' => '02200470006',
                'department' => 'Arts and Humanities',
                'phone' => '+1234567896',
                'photo_url' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=400&h=400&fit=crop&crop=face',
                'photo_name' => 'emily-davis.jpg',
            ],
            [
                'name' => 'Robert Miller',
                'email' => 'robert.miller@student.lexauniversity.edu',
                'matric_no' => '02200470007',
                'department' => 'Social Sciences',
                'phone' => '+1234567897',
                'photo_url' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400&h=400&fit=crop&crop=face',
                'photo_name' => 'robert-miller.jpg',
            ],
            [
                'name' => 'Lisa Anderson',
                'email' => 'lisa.anderson@student.lexauniversity.edu',
                'matric_no' => '02200470008',
                'department' => 'Natural Sciences',
                'phone' => '+1234567898',
                'photo_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&h=400&fit=crop&crop=face',
                'photo_name' => 'lisa-anderson.jpg',
            ],
        ];

        foreach ($students as $student) {
            $photoPath = $this->downloadImage($student['photo_url'], $student['photo_name']);

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
                'photo' => $photoPath,
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('Users seeded successfully with downloaded profile photos!');
    }

    /**
     * Download image from URL and store locally
     */
    private function downloadImage(string $url, string $filename): ?string
    {
        try {
            $this->command->info("Downloading image: {$filename}");

            // Download image with timeout
            $response = Http::timeout(30)->get($url);

            if ($response->successful()) {
                $imagePath = 'profile-photos/' . $filename;
                Storage::disk('public')->put($imagePath, $response->body());

                $this->command->info("✓ Downloaded: {$filename}");
                return $imagePath;
            } else {
                $this->command->warn("✗ Failed to download {$filename}: HTTP {$response->status()}");
                return $this->createDefaultAvatar($filename);
            }

        } catch (\Exception $e) {
            $this->command->error("✗ Error downloading {$filename}: " . $e->getMessage());
            return $this->createDefaultAvatar($filename);
        }
    }

    /**
     * Create a default avatar when image download fails
     */
    private function createDefaultAvatar(string $filename): ?string
    {
        try {
            // Create a simple colored rectangle as default avatar
            $defaultImageContent = $this->generateDefaultAvatar();
            $imagePath = 'profile-photos/default-' . $filename;

            Storage::disk('public')->put($imagePath, $defaultImageContent);
            $this->command->info("✓ Created default avatar: default-{$filename}");

            return $imagePath;

        } catch (\Exception $e) {
            $this->command->error("✗ Failed to create default avatar: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate a simple default avatar image
     */
    private function generateDefaultAvatar(): string
    {
        // Create a simple 400x400 PNG image with a colored background
        $width = 400;
        $height = 400;

        // Create image resource
        $image = imagecreatetruecolor($width, $height);

        // Set background color (light gray)
        $backgroundColor = imagecolorallocate($image, 200, 200, 200);
        imagefill($image, 0, 0, $backgroundColor);

        // Add a simple user icon (circle + rectangle for head and body)
        $iconColor = imagecolorallocate($image, 150, 150, 150);

        // Draw head (circle)
        imagefilledellipse($image, 200, 150, 80, 80, $iconColor);

        // Draw body (rectangle with rounded corners effect)
        imagefilledrectangle($image, 160, 220, 240, 320, $iconColor);

        // Capture output
        ob_start();
        imagepng($image);
        $imageData = ob_get_contents();
        ob_end_clean();

        // Clean up
        imagedestroy($image);

        return $imageData;
    }
}
