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
        $client->setClientId(config('services.google.client_id')); //GOOGLE_CLIENT_ID = 1034891502667-912jfmkiuj0lt5pltedjbdc6ecgip764.apps.googleusercontent.com
        $client->setClientSecret(config('services.google.client_secret')); //GOOGLE_CLIENT_SECRET = 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
        $client->setRedirectUri(config('services.google.redirect'));
        $client->addScope(GoogleCalender::CALENDAR_EVENTS);
        

        return redirect($client->createAuthUrl());
    }

    public function handleGoogleCallback(Request $request)
    {
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
}
