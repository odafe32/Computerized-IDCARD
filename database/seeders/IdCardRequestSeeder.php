<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\IdCardRequest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class IdCardRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all students
        $students = User::where('role', 'student')->get();

        // Get admin user for reviewer
        $admin = User::where('role', 'admin')->first();

        if ($students->isEmpty() || !$admin) {
            $this->command->warn('Please run UserSeeder first to create students and admin.');
            return;
        }

        $reasons = ['new', 'replacement', 'lost', 'damaged', 'name_change'];
        $statuses = ['pending', 'approved', 'rejected', 'printed', 'ready', 'collected'];

        // Create sample requests for each student
        foreach ($students as $student) {
            // Create 1-3 requests per student
            $requestCount = rand(1, 3);

            for ($i = 0; $i < $requestCount; $i++) {
                $reason = $reasons[array_rand($reasons)];
                $status = $statuses[array_rand($statuses)];

                // Create the request
                $request = IdCardRequest::create([
                    'id' => Str::uuid(),
                    'user_id' => $student->id,
                    'reason' => $reason,
                    'additional_info' => $this->getAdditionalInfo($reason),
                    'photo_path' => null, // Will use user's photo
                    'status' => $status,
                    'reviewed_by' => in_array($status, ['approved', 'rejected', 'printed', 'ready', 'collected']) ? $admin->id : null,
                    'reviewed_at' => in_array($status, ['approved', 'rejected', 'printed', 'ready', 'collected']) ? now()->subDays(rand(1, 30)) : null,
                    'admin_feedback' => $this->getAdminFeedback($status, $reason),
                    'card_number' => in_array($status, ['printed', 'ready', 'collected']) ? $this->generateCardNumber() : null,
                    'printed_at' => in_array($status, ['printed', 'ready', 'collected']) ? now()->subDays(rand(1, 15)) : null,
                    'collected_at' => $status === 'collected' ? now()->subDays(rand(1, 7)) : null,
                    'collected_by' => $status === 'collected' ? $student->name : null,
                    'collection_notes' => $status === 'collected' ? 'ID card collected successfully. Student verified identity.' : null,
                    'created_at' => now()->subDays(rand(1, 60)),
                ]);

                $this->command->info("Created ID card request {$request->request_number} for {$student->name}");
            }
        }

        // Create some specific scenario requests
        $this->createSpecificScenarios($students, $admin);
    }

/**
     * Create specific scenario requests for testing
     */
    private function createSpecificScenarios($students, $admin)
    {
        if ($students->count() < 5) {
            return;
        }

        // Scenario 1: Recent pending request
        IdCardRequest::create([
            'id' => Str::uuid(),
            'user_id' => $students[0]->id,
            'reason' => 'new',
            'additional_info' => 'New student requiring first ID card.',
            'status' => 'pending',
            'created_at' => now()->subHours(2),
        ]);

        // Scenario 2: Recently approved request ready for printing
        IdCardRequest::create([
            'id' => Str::uuid(),
            'user_id' => $students[1]->id,
            'reason' => 'replacement',
            'additional_info' => 'Previous card was damaged in an accident.',
            'status' => 'approved',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now()->subHours(6),
            'admin_feedback' => 'Approved. Please proceed with card generation.',
            'created_at' => now()->subDays(1),
        ]);

        // Scenario 3: Rejected request with detailed feedback
        IdCardRequest::create([
            'id' => Str::uuid(),
            'user_id' => $students[2]->id,
            'reason' => 'lost',
            'additional_info' => 'Lost my ID card during campus event.',
            'status' => 'rejected',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now()->subDays(2),
            'admin_feedback' => 'Request rejected. Please file a police report for lost ID card before requesting replacement. Submit the report with your next request.',
            'created_at' => now()->subDays(3),
        ]);

        // Scenario 4: Printed card ready for collection
        $readyRequest = IdCardRequest::create([
            'id' => Str::uuid(),
            'user_id' => $students[3]->id,
            'reason' => 'name_change',
            'additional_info' => 'Legal name change after marriage. Documents attached.',
            'status' => 'ready',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now()->subDays(5),
            'admin_feedback' => 'Approved after document verification.',
            'card_number' => $this->generateCardNumber(),
            'printed_at' => now()->subDays(2),
            'created_at' => now()->subDays(7),
        ]);

        // Scenario 5: Recently collected card
        IdCardRequest::create([
            'id' => Str::uuid(),
            'user_id' => $students[4]->id,
            'reason' => 'damaged',
            'additional_info' => 'Card damaged due to water exposure.',
            'status' => 'collected',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now()->subDays(10),
            'admin_feedback' => 'Approved. Old card will be deactivated.',
            'card_number' => $this->generateCardNumber(),
            'printed_at' => now()->subDays(5),
            'collected_at' => now()->subDays(1),
            'collected_by' => $students[4]->name,
            'collection_notes' => 'Student provided damaged card for deactivation. New card issued successfully.',
            'created_at' => now()->subDays(12),
        ]);

        $this->command->info('Created specific scenario requests for testing');
    }

    /**
     * Generate additional info based on reason
     */
    private function getAdditionalInfo($reason)
    {
        $additionalInfoOptions = [
            'new' => [
                'New student requiring first ID card.',
                'First semester student needs ID card for campus access.',
                'Transfer student requiring new university ID.',
            ],
            'replacement' => [
                'Previous card was damaged and needs replacement.',
                'Old card is worn out and barely readable.',
                'Card magnetic strip is not working properly.',
            ],
            'lost' => [
                'Lost my ID card during campus event.',
                'Card was lost while traveling home for holidays.',
                'Misplaced card during library study session.',
                'Lost card in campus parking lot.',
            ],
            'damaged' => [
                'Card damaged due to water exposure.',
                'Card cracked after accidental drop.',
                'Card bent and photo is peeling off.',
                'Card damaged in washing machine accident.',
            ],
            'name_change' => [
                'Legal name change after marriage. Documents attached.',
                'Name correction due to spelling error on original card.',
                'Updated name after legal name change process.',
            ],
        ];

        $options = $additionalInfoOptions[$reason] ?? ['Standard request for ID card.'];
        return $options[array_rand($options)];
    }

    /**
     * Generate admin feedback based on status and reason
     */
    private function getAdminFeedback($status, $reason)
    {
        if (!in_array($status, ['approved', 'rejected', 'printed', 'ready', 'collected'])) {
            return null;
        }

        if ($status === 'rejected') {
            $rejectionReasons = [
                'Insufficient documentation provided. Please submit required documents.',
                'Photo quality is poor. Please provide a clearer passport-size photo.',
                'Request does not meet university guidelines. Please review requirements.',
                'Previous card must be returned before issuing replacement.',
                'Student account has pending fees. Please clear dues before requesting ID card.',
            ];
            return $rejectionReasons[array_rand($rejectionReasons)];
        }

        $approvalFeedbacks = [
            'Approved. All requirements met.',
            'Request approved after document verification.',
            'Approved. Please collect within 7 days.',
            'All documentation verified. Approved for processing.',
            'Request meets all criteria. Approved.',
        ];

        return $approvalFeedbacks[array_rand($approvalFeedbacks)];
    }

    /**
     * Generate a unique card number
     */
    private function generateCardNumber()
    {
        $year = date('Y');
        $random = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        return "LU{$year}{$random}";
    }
}
