<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DataMigrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DataMigrationController extends Controller
{

    protected $dataMigration;

    public function __construct(DataMigrationService $dataMigration)
    {
        $this->middleware('auth');
        $this->dataMigration = $dataMigration;
    }

    public function create()
    {
        return view('user-data-entry');
    }

    public function uploadUserDataByExcel(Request $request)
    {
        try {

            $this->dataMigration->mapDataFromExcel($request);

            $response = [
                'success' => 'SUCCESS'
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => 'FAILED',
                'errors' => $e->getMessage()
            ];
            return response()->json($response, 500);
        }
    }
}
