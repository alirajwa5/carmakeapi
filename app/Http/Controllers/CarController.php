<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

use App\Models\CarMake;
use App\Models\CarModel;

class CarController extends Controller
{
    const API_URL = 'https://vpic.nhtsa.dot.gov/api/vehicles/GetMakesForVehicleType/car?format=json';
    const API_MODELS_URL = 'https://vpic.nhtsa.dot.gov/api/vehicles/getmodelsformake/honda?format=json';


    public function saveCarMakesFromApi(Client $client)
    {
        try {
            // Make API request to get car makes
            $response = $client->get(self::API_URL);



            // Validate API response structure
            $carMakes = json_decode($response->getBody(), true);



            if (!isset($carMakes['Results']) || !is_array($carMakes['Results'])) {
                throw new \RuntimeException('Invalid API response structure');
            }

            // Start a database transaction
            DB::beginTransaction();

            // Save car makes to the CarMakes table
            foreach ($carMakes['Results'] as $carResult) {

                $makeName = $carResult['MakeName'] ?? null;

                if ($makeName !== null) {
                    $this->saveCarMake($makeName);
                }
            }


            // Commit the transaction if everything went well
            DB::commit();

            return response()->json(['message' => 'Car makes saved successfully'], 200);
        } catch (\Exception $exception) {
            // Rollback any database changes if an exception occurs
            DB::rollBack();

            // Log the error or handle it appropriately
            Log::error('Error saving car makes', ['exception' => $exception]);

            return response()->json(['message' => 'An error occurred while saving car makes'], 500);
        }
    }

    private function saveCarMake($makeName)
    {
        // Validate if the make_name is not empty before saving
        if (!empty($makeName)) {
            // Use the CarMake model to create a new record
            \App\Models\CarMake::create([
                'make_name' => $makeName,
            ]);
        }
    }

    public function saveCarModelsFromApi(Client $client)
{
    try {
        // Set the maximum execution time to 10 minutes (600 seconds)
        $maxExecutionTime = 600;

        // Retrieve the start time from the cache
        $startTimeKey = 'save_car_models_start_time';
        $startTime = Cache::get($startTimeKey, now());

        // Calculate the elapsed time
        $elapsedTime = now()->diffInSeconds($startTime);

        // Check if the elapsed time exceeds the maximum allowed time
        if ($elapsedTime > $maxExecutionTime) {
            return response()->json(['message' => 'Operation timed out'], 500);
        }

        // Store the current time in the cache
        Cache::put($startTimeKey, now(), now()->addSeconds($maxExecutionTime));

        // Retrieve all car makes from the database
        $carMakes = CarMake::all();

        // Start a database transaction
        DB::beginTransaction();

        // Iterate over each car make and fetch its models from the API
        foreach ($carMakes as $carMake) {
            $makeName = rawurlencode(strtolower(rtrim($carMake->make_name, '.')));


                $apiUrl = "https://vpic.nhtsa.dot.gov/api/vehicles/getmodelsformake/{$makeName}?format=json";



            // Make API request to get car models for the specific make
            $response = $client->get($apiUrl);

            $carModels = json_decode($response->getBody(), true);

            // Validate API response structure
            if (isset($carModels['Results']) && is_array($carModels['Results'])) {
                // Save car models to the CarModels table
                foreach ($carModels['Results'] as $carModel) {
                    $this->saveCarModel($carMake->id, $carModel['Model_Name']);
                }
            }
        }

        // Commit the transaction if everything went well
        DB::commit();

        return response()->json(['message' => 'Car models saved successfully'], 200);
    } catch (\Exception $exception) {
        // Rollback any database changes if an exception occurs
        DB::rollBack();

        // Log the error or handle it appropriately
        Log::error('Error saving car models', ['exception' => $exception]);

        return response()->json(['message' => 'An error occurred while saving car models'], 500);
    }
}


    private function saveCarModel($makeId, $modelName)
    {
        // Validate if the model_name is not empty before saving
        if (!empty($modelName)) {

            // Use the CarModel model to create a new record
            CarModel::create([
                'make_id' => $makeId,
                'model_name' => $modelName,
            ]);
        }
    }


    const API_BASE_URL = 'http://api.carmd.com/v3.0/make?year=';
    const API_HEADERS = [
        'headers' => [
            'content-type' => 'application/json',
            'authorization' => 'Basic NjJkOGFlMmMtMWNmMi00NzYyLWI0YTgtNWNkN2UxNmUyZjRm',
            'partner-token' => '35aaadd92d1c4c70b855d1af8db8338b',
        ],
    ];

    // public function saveCarMakesFromApi(Client $client)
    // {
    //     try {
    //         // Start a database transaction
    //         DB::beginTransaction();

    //         // Get the current year
    //         $currentYear = date('Y');

    //         // Iterate through each year from 1996 to the current year
    //         for ($year = 1996; $year <= $currentYear; $year++) {
    //             // Make API request to get car makes for the specific year
    //             $apiUrl = self::API_BASE_URL . $year;
    //             $response = $client->get($apiUrl, self::API_HEADERS);

    //             // Validate API response structure
    //             $apiData = json_decode($response->getBody(), true);
    //             dd($apiData);
    //             $carMakes = $apiData['data'] ?? [];

    //             // Save car makes to the CarMakes table
    //             foreach ($carMakes as $carMake) {
    //                 $this->saveCarMake($carMake);
    //             }
    //         }

    //         // Commit the transaction if everything went well
    //         DB::commit();

    //         return response()->json(['message' => 'Car makes saved successfully'], 200);
    //     } catch (\Exception $exception) {
    //         // Rollback any database changes if an exception occurs
    //         DB::rollBack();

    //         // Log the error or handle it appropriately
    //         Log::error('Error saving car makes', ['exception' => $exception]);

    //         return response()->json(['message' => 'An error occurred while saving car makes'], 500);
    //     }
    // }

    // private function saveCarMake($makeName)
    // {
    //     // Validate if the make_name is not empty before saving
    //     if (!empty($makeName)) {
    //         // Check if the car make already exists in the database
    //         $existingCarMake = CarMake::where('make_name', $makeName)->first();

    //         if (!$existingCarMake) {
    //             // If it doesn't exist, save the new car make
    //             CarMake::create([
    //                 'make_name' => $makeName,
    //             ]);
    //         }
    //     }
    // }
}
