<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Laravel\Socialite\Facades\Socialite;

class MeetController extends Controller
{

    public function handleGoogleCallback()
    {

        $user = Socialite::driver('google')->user();

        $client = new Google_Client();
        $client->setAccessToken($user->token);

        $service = new Google_Service_Calendar($client);

        $event = new Google_Service_Calendar_Event(array(
            'summary' => 'Meeting Title',

            'start' => array(
                'dateTime' => Carbon::now()->format('c'),
                'timeZone' => 'Asia/Kolkata',
            ),
            'end' => array(
                'dateTime' => Carbon::now()->addMinutes(15)->format('c'),
                'timeZone' => 'Asia/Kolkata',
            ),
            'conferenceData' => array(
                'createRequest' => array(
                    'conferenceSolutionKey' => array(
                        'type' => 'hangoutsMeet',
                    ),
                    'requestId' => '123',
                ),
            ),
        ));

        $calendarId = 'primary'; // Use 'primary' for the user's primary calendar
        $event = $service->events->insert($calendarId, $event, array('conferenceDataVersion' => 1));

        $meetLink = $event->hangoutLink;

        return $meetLink;

    }
}
