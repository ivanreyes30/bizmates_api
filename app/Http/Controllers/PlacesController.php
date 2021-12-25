<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PlacePostRequest;
use Exception;

class PlacesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(PlacePostRequest $request)
    {
        try {
            $validated = $request->validated();
            if($validated){
                $query = array(
                    // 'll' => $request->ll,
                    'near' => $request->near,
                    'category' => $request->category
                );
                $client = new \GuzzleHttp\Client();
                $response = $client->request('GET', 'https://api.foursquare.com/v3/places/search?'.http_build_query($query).'', [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => env('PLACES_API_KEY'),
                    ],
                ]);
                $response = json_decode($response->getBody()->getContents());
                foreach($response->results as $key => $value) {
                    $image = $client->request('GET', 'https://api.foursquare.com/v3/places/'.$value->fsq_id.''.'/photos', [
                        'headers' => [
                            'Accept' => 'application/json',
                            'Authorization' => env('PLACES_API_KEY'),
                        ],
                    ]);
                    $image = json_decode($image->getBody()->getContents());
                    foreach($image as $key2 => $value2) {
                        $response->results[$key]->images[] = $value2->prefix.'original'.$value2->suffix;
                    }
                }
                return response()->json(array('status' => true, 'message' => 'Successfully Fetched Data.', 'data' => $response), 200);
            }
            else {
                throw new Exception('Validation Error', 422);
            }
                
        }
        catch(\Exception $exception){
            switch ($exception->getCode()) {
                case 400:
                    throw new Exception('Invalid Location.', $exception->getCode());
                    break;
                default:
                    throw $exception;
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function category_taxonomy()
    {
        try {
            $path = storage_path() . "/json/integrated_category_taxonomy.json";
            $json = json_decode(file_get_contents($path)); 
            $count = 0;
            foreach($json as $key => $value) { 
                $result[$count] = array(
                    'id' => $key,
                    'label' => $value->labels->en,
                    'parent' => false
                );
                foreach($result as $key2 => $value2) {
                    if(count($value->parents) > 0) {
                        if($value2['id'] == $value->parents[0]) {
                            $result[$key2]['parent'] = true;
                        }
                    }
                    else {
                        $result[$key2]['parent'] = true;
                    }
                }
                $count++;
            }
            return response()->json(array('status' => true, 'message' => 'Successfully Fetched Data.', 'data' => $result), 200);       
        }
        catch(\Exception $exception){
            throw $exception;
        }
        
    }
}
