<?php

namespace App\Console\Commands;


use App\MigrationCustomer;
use Box\Spout\Common\Type;
use Box\Spout\Reader\Common\Creator\ReaderFactory;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CustomerMigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migration:start';

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
        try {

            $reader = ReaderFactory::createFromType(Type::CSV); // for XLSX files
            $path = $this->ask('Enter your File Path');
            $chunk_size = $this->ask('Enter Chunk Size');
            //$path = '/var/www/projects/bl_idp/public/Customer_data_without_password.csv';
            $reader->open($path);

            foreach ($reader->getSheetIterator() as $sheet) {
                $this->info('Start Reading');
                $rowNumber = 1;
                $index = 0;
                $insertdata = [];
                $batch = 1;
                foreach ($sheet->getRowIterator() as $row) {
                    $cells = $row->getCells();
                    if ($rowNumber > 1) {
                        $number = $cells[0]->getValue();
                        $birth_date = $cells[3]->getValue();
                        $create_date = $cells[4]->getValue() ?? "12/22/2020 11:07:42 AM";
                        $update_date = $cells[5]->getValue() ?? "12/22/2020 11:07:42 AM";
                        $insertdata[] = array(
                            'mobile' => "0".$number,
                            'msisdn' => "880".$number,
                            'username' => "0".$number,
                            'name' => $cells[1]->getValue(),
                            //'email' => $cells[2]->getValue(),
                            'birth_date' => ($birth_date != '')? Carbon::createFromFormat('d/m/Y', $birth_date)->toDateString(): null ,
                            'created_at' => date("Y-m-d H:i:s", strtotime($create_date)),
                            'updated_at' => date("Y-m-d H:i:s", strtotime($create_date)),
                            // 'gender' => $cells[6]->getValue(),
                            'password' => $cells[8]->getValue(),
                            'user_type' => "CUSTOMER",
                            "status" => 1,
                        );
                    }
                    $index++;
                    $rowNumber++;

                    if(count($insertdata) == $chunk_size){

                        $this->info('Inserting...');

                        MigrationCustomer::insert($insertdata);
                        $this->info("Batch #$batch Inserted");
                        $batch++;
                        $insertdata = [];
                    }
                }
            }

            if(!empty($insertdata)){
                MigrationCustomer::insert($insertdata);
                $this->info("Batch #$batch Inserted");
                $batch++;
            }

            $this->info('Completed');

        } catch (\Exception $e) {
            //dd($e);
            $this->info($e->getMessage());
        }
    }
}
