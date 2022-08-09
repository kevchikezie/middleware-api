<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class InstitutionController extends Controller
{
    private $baseUrl = 'https://api.dev.elucidate.co';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Search for an institution
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        //TODO: Implement cache if the same search string is searched for within a certain time frame
                // check is it exists in the cache

                // if not on cache make a call to elucidate API

        if (! $request->query('fullSearch')) {
            return response()->json([
                'status' => 200,
                'body' => [],
            ]);
        }

        $token = $request->header('Authorization');

        $headers = [
            'Authorization' => $token,
            'Content-Type' => 'application/ld+json',
            'Accept' => 'application/ld+json',
        ];

        $searchString = strip_tags($request->query('fullSearch'));

        $url = $this->baseUrl . '/institutions?fullSearch=' . $searchString;

        $response = Http::withHeaders($headers)->get($url);

        $body = json_decode($response->body(), true);

        if ($response->status() != 200) {
            $this->createTicket($searchString, $token);

            return response()->json($body);
        }

        return $body;
    }

    /**
     * Create ticket if an institution does not exist
     * 
     * @param  string  $searchString
     * @param  string  $token
     * @return null
     */
    private function createTicket($searchString, $token)
    {
        $headers = [
            'Authorization' => $token,
            'Content-Type' => 'application/ld+json',
            'Accept' => 'application/ld+json',
        ];

        $url = $this->baseUrl . '/tickets';

        $response = Http::withHeaders($headers)->get($url, [
            'project' => "projects/2a9caad1-19c7-4340-949f-30b81a8a043e",
            'title' => 'missing Institution ' . $searchString,
            'description' => 'add Institution ' . $searchString,
            'createdAt' => Carbon::now(),
            'updatedAt' => Carbon::now(),
        ]);
    }
    
}
