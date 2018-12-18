<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use App\Models\Uccx\AgentStat;
use Illuminate\Console\Command;
use App\Events\UccxDataUpdated;
use GuzzleHttp\Exception\RequestException;

class CallUccxApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uccx:call-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call the UCCX API to get Agent Statistics';

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
        \Log::info('CallUccxApi: Calling UCCX API');

        $client = new Client();
        \Log::info('CallUccxApi: Created Guzzle Client for HTTPS comms');

        $agentStats = [];
        \Log::info('CallUccxApi: Created $agentStats array to store data');

        \Log::info('CallUccxApi: Checking application environment');
        if (\App::environment('local')) {

            \Log::info('CallUccxApi: App is in local/dev mode.  Generating dummy data');
            
            $agentStats['ready'] = mt_rand(0,100);
            $agentStats['notReady'] = mt_rand(0,100);
            $agentStats['talking'] = mt_rand(0,100);
            $agentStats['loggedIn'] = $agentStats['ready'] + $agentStats['notReady'] + $agentStats['talking'];
            $labels = Carbon::now()->format('h:i');
            $agentStats['labels'] = $labels;

            \Log::info('CallUccxApi: App is in local/dev mode.  Dummy data is ready', $agentStats);

        } else {

            \Log::info('CallUccxApi: App is NOT in local/dev mode.  Calling UCCX API now');

            try {
                $res = $client->request('GET', 'https://hq-uccx.abc.inc/adminapi/agentstats', [
                    'auth' => [
                        'administrator', 'ciscopsdt'
                    ],
                    'verify' => false
                ]);
            } catch (RequestException $e) {
                \Log::error('CallUccxApi: Received an error when calling the UCCX API - ', [$e->getMessage()]);
                exit;
            }
    
            \Log::info('CallUccxApi: Received valid response from UCCX API');
            
            $xml = simplexml_load_string($res->getBody());
            \Log::info('CallUccxApi: Response body is - ', [$xml]);

            $json = json_encode($xml);
            \Log::info('CallUccxApi: JSON encoded response body is - ', [$json]);

            $agentStats = json_decode($json,TRUE);
            \Log::info('CallUccxApi: JSON decoded response body ($agentStats) is - ', [$agentStats]);

            $labels = Carbon::now()->format('h:i');
            \Log::info('CallUccxApi: Set chart labels - ', [$labels]);

            $agentStats['labels'] = $labels;
            \Log::info('CallUccxApi: Added labels to $agentStats - ', [$labels]);
        }

        \Log::info('CallUccxApi: checking if ready agents == 0');
        if($agentStats['ready'] == 0) {

            \Log::info('CallUccxApi: Ready agents does == 0.  Posting notice to Teams Room');

            try {
                $res = $client->request('POST', 'https://api.ciscospark.com/v1/messages', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . env('TEAMS_BOT_TOKEN'),
                    ],
                    'verify' => false,
                        RequestOptions::JSON => [
                        'toPersonEmail' => env('TEAMS_NOTIFY_ADDRESS'),
                        'text' => 'Heads up!  There are no agents ready in the UCCX queue!'
                    ]
                ]);
            } catch (RequestException $e) {
                \Log::error('CallUccxApi: Received an error when posting to the Webex Teams room - ', [$e->getMessage()]);
            }
            
        }

        \Log::info('CallUccxApi: Storing local model statistics in DB');
        \App\Models\Uccx\AgentStat::create([
            'stats' => $agentStats
            
        ]);

        \Log::info('CallUccxApi: Generating Websockets event to notify listening clients');
        event(new UccxDataUpdated(AgentStat::getAgentStats()));
    }
}
