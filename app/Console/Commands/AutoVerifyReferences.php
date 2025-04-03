<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoVerifyReferences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'references:auto-verify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically verify unverified references and update details';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            Log::info('Automatically verify unverified references and update details');
            // Fetch unverified references
            $unverifiedReferences = DB::table('user_references as ur')
                ->leftJoin('user_reference_details as urd','ur.id', 'urd.reference_id')
                ->where('ur.is_verify', 0)
                ->whereNotNull('urd.reference_by_email')
                ->select('ur.*')
                ->get();
        
            foreach ($unverifiedReferences as $reference) {
                // Update verified flag
                DB::table('user_references')
                    ->where('id', $reference->id)
                    ->update(['is_verify' => 1]);

                // Update verified by details
                DB::table('user_reference_details')
                    ->where([
                        'user_id' => $reference->user_id,
                        'reference_id' => $reference->id
                    ])
                    ->update([
                        'reference_by_verified_by' => 'Automated System',
                        'reference_by_verified_by_date' => now(),
                    ]);
            }

            Log::info('References has been verified successfully');
            return true;
        } catch (\Exception $e) {
            Log::info('Error: ' . $e->getMessage());
            return false;
        }
    }
}
