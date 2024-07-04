<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class updateProfileImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profile-image:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::whereNotNull('profile_image')
                      ->whereNull('profile_image_base64')->get();
        $bar = $this->output->createProgressBar(count($users));
        $bar->start();

        foreach ($users as $user) {
            $file_path = config('filesystems.profile_image_path').$user->profile_image;

            try{
                $path = base64_encode(file_get_contents($file_path));
                $this->info('File exists');
                $user->update([
                    'profile_image_base64' => $path
                ]);
            }catch (\Exception $e){
                $this->info($e->getMessage());
            }
            $bar->advance();
        }
        $bar->finish();
    }
}
