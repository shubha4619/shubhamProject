<?php

namespace App\Http\Controllers;

use App\Models\StoreMeetLink;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Google_Client as GoogleClient;
use Google_Service_Calendar as GoogleCalender;
use Google_Service_Calendar_Event as GoogleEvent;
use Google_Service_Calendar_EventConferenceData as GoogleMeetEvents;
use Illuminate\Support\Facades\Validator;

class MeetController extends Controller
{
    public function redirectToGoogle()
    {

        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
        $client->addScope(GoogleCalender::CALENDAR_EVENTS);
        

        return redirect($client->createAuthUrl());
    }

    public function handleGoogleCallback(Request $request)
    {
        // dd('asdasd');
        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
        $client->addScope(GoogleCalender::CALENDAR_EVENTS);

        $accessToken = $client->fetchAccessTokenWithAuthCode($request->code);
        // dd($accessToken);
        $client->setAccessToken($accessToken);

        // Create a Google Calendar event with a Google Meet link
        $service = new GoogleCalender($client);
        
        $event = new GoogleEvent(array(
            'summary' => 'Meeting Title',
            'start' => array(
                'dateTime' => '2023-08-15T10:00:00',
                'timeZone' => 'America/New_York',
            ),
            'end' => array(
                'dateTime' => '2023-08-15T11:00:00',
                'timeZone' => 'America/New_York',
            ),

            'conferenceData' => [
                "createRequest" => [
                  "conferenceSolutionKey" => [
                    "type" => "hangoutsMeet"
                  ],
                  "requestId" => "123"
                ]
            ]
        ));


        $calendarId = 'primary'; // Use 'primary' for the user's primary calendar
        $event = $service->events->insert($calendarId, $event, array('conferenceDataVersion' => 1));

        return ($event['hangoutLink']) ;

    }

    public function sendError($error, $errorMessages = [], $code = 200)
    {
    	$response = [
            'status' => false,
            'message' => $error,
        ];


        if(!empty($errorMessages)){
            $response['data'] = $errorMessages; 
        }


        return response()->json($response, $code);
    }

    public function store(Request $request){
          

        $validator = Validator::make($request->all(), [
            'title' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first() );
        }

        $data  = new StoreMeetLink();
        $data->title = $request->get('title');
        if($data->save()){
          return  $this->redirectToGoogle();
        }


    }
}
