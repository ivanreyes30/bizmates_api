<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ForecastPostRequest;
use Exception;


class WeatherForecastController extends Controller
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
    public function show(ForecastPostRequest $request)
    {
        try {
            $validated = $request->validated();
            if($validated){
                if(!isset($request->unit)) {
                    $request->unit = 'metric';
                    $symbol = '°C';
                    $gust = 'm/s';
                }
                else {
                    switch($request->unit) {
                        case 'imperial':
                            $symbol = ' °F';
                            $gust = 'm/h';
                            break;
                        case 'metric':
                            $symbol = ' °C';
                            $gust = 'm/s';
                            break;
                          default:
                            $symbol = ' °K';
                            $gust = 'm/s';
                    }
                }
                $client = new \GuzzleHttp\Client();
                $endpoint = 'https://api.openweathermap.org/data/2.5/forecast?q='.$request->city_name.','.$request->state_code.','.$request->country_code.'&appid='.env('FORECAST_API_KEY').'&units='.$request->unit.'';
                $response = $client->request('GET', $endpoint);
                $response = json_decode($response->getBody()->getContents());
                foreach($response->list as $key => $value) {
                    $result['list'][$key] = array(
                        'temp' => $value->main->temp. $symbol,
                        'feels_like' => $value->main->feels_like. $symbol,
                        'temp_min' => $value->main->temp_min. $symbol,
                        'temp_max' => $value->main->temp_max. $symbol,
                        'pressure' => $value->main->pressure.'hPa',
                        'sea_level' => $value->main->sea_level.'hPa',
                        'grnd_level' => $value->main->grnd_level.'hPa',
                        'humidity' => $value->main->humidity.'%',
                        'temp_kf' => $value->main->temp_kf,
                        'wind' => array(
                            'speed' => $value->wind->speed.'m/s',
                            'deg' => $value->wind->deg.'°',
                        ),
                        'clouds' => $value->clouds->all.'%',
                        'weather' => array(
                            'id' => '',
                            'main' => '',
                            'description' => '',
                            'icon' => ''
                        ),
                        'dt_txt' => $value->dt_txt,
                        'date' => date('F d, Y h:ia', strtotime($value->dt_txt)),
                        'day' => date('l', strtotime($value->dt_txt)),
                    );
                    if(count($value->weather) > 0) {
                        $result['list'][$key]['weather'] = array(
                            'id' => $value->weather[0]->id,
                            'main' => $value->weather[0]->main,
                            'description' => ucwords($value->weather[0]->description),
                            'icon' => $value->weather[0]->icon
                        );
                    }
                }
                $result['city'] = $response->city;
                return response()->json(array('status' => true, 'message' => 'Successfully Fetched Data.', 'data' => $result), 200);
            }
            else {
                throw new Exception('Validation Error', 422);
            }
                      
        }
        catch(\Exception $exception){
            switch ($exception->getCode()) {
                case 404:
                    throw new Exception('City Name, State Code, and Country Code not found.', $exception->getCode());
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
}
