<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceReview;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ServiceReviewSeeder extends Seeder
{
    // ── Reviewer personas ────────────────────────────────────────────────────
    private array $reviewers = [
        ['name' => 'Ahmed Al Mansoori',   'email' => 'ahmed.mansoori@example.com'],
        ['name' => 'Sara Al Rashidi',     'email' => 'sara.rashidi@example.com'],
        ['name' => 'James Harrington',    'email' => 'james.h@example.com'],
        ['name' => 'Priya Nair',          'email' => 'priya.nair@example.com'],
        ['name' => 'Mohammed Al Hashimi', 'email' => 'mo.hashimi@example.com'],
        ['name' => 'Emily Chen',          'email' => 'emily.chen@example.com'],
        ['name' => 'Khalid Al Shamsi',    'email' => 'khalid.shamsi@example.com'],
        ['name' => 'Fatima Al Zaabi',     'email' => 'fatima.zaabi@example.com'],
        ['name' => 'Ravi Sharma',         'email' => 'ravi.sharma@example.com'],
        ['name' => 'Laura Hoffmann',      'email' => 'laura.hoffmann@example.com'],
        ['name' => 'Tariq Al Nuaimi',     'email' => 'tariq.nuaimi@example.com'],
        ['name' => 'Aisha Bint Hamdan',   'email' => 'aisha.hamdan@example.com'],
    ];

    // ── Comments keyed by rating ─────────────────────────────────────────────
    private array $comments = [
        5 => [
            'Absolutely outstanding service. Everything was handled professionally and delivered ahead of schedule. Highly recommend.',
            'Exceeded all my expectations. The team was knowledgeable, responsive, and got everything right the first time.',
            'Flawless experience from start to finish. Clear communication, transparent pricing, and the result was perfect.',
            'Best service I\'ve used in the UAE. Got my documents sorted in record time with zero hassle.',
            'Five stars without hesitation. They walked me through every step and kept me informed throughout the process.',
            'Incredible attention to detail. Every question was answered promptly and the final outcome was exactly what we needed.',
            'Truly professional team. They went above and beyond to ensure everything was completed correctly and on time.',
        ],
        4 => [
            'Very good overall. Process was smooth and the staff was helpful. Minor delay in receiving documents but nothing serious.',
            'Solid service and knowledgeable team. Would have been 5 stars if communication had been slightly quicker.',
            'Happy with the outcome. A couple of back-and-forth emails to clarify requirements, but everything worked out well.',
            'Great experience. Pricing was clear and the work was done accurately. Would use again for future requirements.',
            'Professional and reliable. The service took a day longer than estimated but the quality was definitely worth it.',
            'Good value for money. The process was explained clearly and the team handled everything without me having to chase.',
        ],
        3 => [
            'Service was okay but nothing special. Took longer than expected and I had to follow up a couple of times.',
            'Average experience. The end result was correct but the process felt disorganised at times.',
            'Acceptable service. Got the job done but communication could be much better. Had to ask for updates proactively.',
            'Decent service but felt like just another number. Would expect more personalised attention for the price.',
            'The outcome was fine but the journey was frustrating. Mixed signals at various stages of the process.',
            'Three stars — did what it promised but nothing more. Room for improvement on responsiveness and clarity.',
        ],
        2 => [
            'Disappointed with this service. Long delays, poor communication, and I had to correct mistakes in the submitted documents.',
            'Below expectations. The process took almost twice the estimated time and I had to chase repeatedly for updates.',
            'Not impressed. Errors were made that caused unnecessary delays. Staff seemed unsure about the requirements.',
            'Would not recommend based on my experience. Paid a premium price for an average result with multiple follow-ups needed.',
            'Service was slow and communication was poor. Felt like my case was being managed by someone unfamiliar with the process.',
        ],
        1 => [
            'Terrible experience. Submitted wrong documents, caused delays with the authority, and showed no accountability.',
            'Complete waste of money and time. Zero communication, missed deadlines, and the issue still isn\'t fully resolved.',
            'Awful service. The team had no idea what they were doing and I ended up doing most of the work myself.',
            'One star is too generous. Rude staff, incorrect submissions, and no refund offered despite their obvious errors.',
            'Worst service I\'ve experienced in UAE. I had to escalate the matter myself after weeks of being ignored.',
        ],
    ];

    public function run(): void
    {
        // Create reviewer users (role = customer)
        $users = [];
        foreach ($this->reviewers as $data) {
            $users[] = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => Hash::make('password'),
                    'role'              => 'customer',
                    'is_active'         => true,
                    'email_verified_at' => now(),
                ]
            );
        }

        $services = Service::all();

        if ($services->isEmpty()) {
            $this->command->warn('No services found — run ServiceSeeder first.');
            return;
        }

        /*
         * Review assignment plan per service:
         *   - Pick a random subset of 4–10 reviewers
         *   - Weight ratings so the distribution looks realistic (more 4s & 5s,
         *     fewer 1s), but always include at least one low rating so the
         *     "disable review" moderation feature can be demonstrated
         *   - A handful of 1-star reviews are pre-disabled so the admin can see
         *     the effect of moderation immediately
         */
        $weightedPool = [5, 5, 5, 4, 4, 4, 4, 3, 3, 2, 1];

        foreach ($services as $service) {
            // Shuffle users and pick 4–9 reviewers for this service
            shuffle($users);
            $count     = rand(4, min(9, count($users)));
            $reviewers = array_slice($users, 0, $count);

            // Guarantee at least one 5-star and one low-rating review per service
            $forcedRatings = [5, rand(1, 2)];
            $usedRatings   = [];

            foreach ($reviewers as $idx => $user) {
                // Use forced rating for first two reviewers, then random weighted
                if (isset($forcedRatings[$idx])) {
                    $rating = $forcedRatings[$idx];
                } else {
                    $rating = $weightedPool[array_rand($weightedPool)];
                }
                $usedRatings[] = $rating;

                $comment = $this->pickComment($rating);

                // Pre-disable some 1-star reviews to demonstrate moderation
                $isActive = !($rating === 1 && rand(0, 1) === 0);

                ServiceReview::updateOrCreate(
                    ['service_id' => $service->id, 'user_id' => $user->id],
                    [
                        'rating'    => $rating,
                        'comment'   => $comment,
                        'is_active' => $isActive,
                        'created_at' => now()->subDays(rand(1, 180)),
                        'updated_at' => now()->subDays(rand(0, 30)),
                    ]
                );
            }

            $avg     = round(array_sum($usedRatings) / count($usedRatings), 1);
            $this->command->line(
                "  <fg=cyan>{$service->name}</> — {$count} reviews, avg <fg=yellow>{$avg}</>"
            );
        }

        $total = ServiceReview::count();
        $this->command->info("Done. {$total} reviews seeded across {$services->count()} services.");
    }

    private function pickComment(int $rating): string
    {
        $pool = $this->comments[$rating];
        return $pool[array_rand($pool)];
    }
}
