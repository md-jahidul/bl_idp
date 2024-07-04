<?php
namespace App\Services;


use App\Models\User;
use App\Models\UserNew;
use Box\Spout\Common\Type;
use Box\Spout\Reader\Common\Creator\ReaderFactory;
use Carbon\Carbon;
use DateTime;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class DataMigrationService
{


    protected $model;
    /**
     * @var array
     */
    protected $config;

    /**
     * DataMigrationService constructor.
     * @param UserNew $model
     */
    public function __construct(UserNew $model)
    {
        $this->model = $model;
    }

    /**
     * @param $date
     * @param string $format
     * @return bool
     */
    public function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function mapDataFromExcel($request)
    {
        try {

            $reader = ReaderFactory::createFromType(Type::CSV); // for XLSX files
            $path = $request->file('user_data_file')->getRealPath();
            $reader->open($path);

            $insertdata = [];
            foreach ($reader->getSheetIterator() as $sheet) {
                $rowNumber = 1;
                $index = 0;
                foreach ($sheet->getRowIterator() as $row) {
                    $cells = $row->getCells();
                    $totalCell = count($cells);
                    if ($rowNumber > 1) {
                        $number = $cells[0]->getValue();
                        $insertdata[] = array(
                            'mobile' => "0".$number,
                            'msisdn' => "880".$number,
                            'username' => "0".$number,
                            'name' => $cells[1]->getValue(),
                           // 'email' => $cells[2]->getValue(),
                            'birth_date' => null,
                            'created_at' => null,
                            'updated_at' => null,
                           // 'gender' => $cells[6]->getValue(),
                            'password' => $cells[8]->getValue(),
                            'user_type' => "CUSTOMER",
                             "status" => 1,
                        );
                    }
/*                    if (!empty($insertdata)) {
                        try {
                            $this->model->insert($insertdata[$index]);
                        } catch (\Exception $e) {
                           // nothing to do
                            Log::error('migration error:'. $number .'--'.$e->getMessage());
                        }
                    }*/

                    $index++;
                    $rowNumber++;
                }
            }

            $insert_data = collect($insertdata); // Make a collection to use the chunk method

            $chunks = $insert_data->chunk(100);

            foreach ($chunks as $chunk)
            {
                dd($chunk->toArray());
                //\DB::table('items_details')->insert($chunk->toArray());
                $this->model->insert($chunk->toArray());
            }

            if (!empty($insertdata)) {
              //  $this->model->insert($insertdata);
               // User::updateOrCreate($insertdata);
                $response = [
                    'success' => 1,
                    'message' => "user data  uploaded successfully!"
                ];
            } else {
                $response = [
                    'success' => 0,
                    'message' => "Excel file format is not correct!"
                ];
            }

            return response()->json($response, 200);

        } catch (\Exception $e) {
            dd($e);
            $response = [
                'success' => 0,
                'message' => $e->getMessage()
            ];
            return response()->json($response, 500);
        }
    }
}
