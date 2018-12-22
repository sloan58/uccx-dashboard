<?php

namespace App\Models\Uccx;

use Illuminate\Database\Eloquent\Model;

class AgentStat extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'stats' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['stats'];

    /**
     * Supply Agent Stats for past 10 minutes
     */
    public static function getAgentStats()
    {
        
        // Get the last 10 entries in the DB for agent stats
        $stats = self::latest()->take(10)->pluck('stats')->toArray();
        $stats = array_reverse($stats);

        // If we don't have 10 stat items, fill in the rest with
        // zero's so that the chart has 10 minutes worth of data
        for ($i = count($stats); $i < 10; $i++) {
            $label = \Carbon\Carbon::now()->subMinutes($i)->format('h:i');
            array_unshift($stats, [
                'ready' => 0,
                'labels' => $label,
                'talking' => 0,
                'loggedIn' => 0,
                'notReady' => 0,
            ]);
        }

        // Create array for return data
        $uccxData = [];

        // Format the data for the front-end chart
        // Each key (ready, notReady, etc) should have
        // and array of 10 values
        foreach (array_keys($stats[0]) as $key) {
            $uccxData[$key] = array_map(
                function ($stat) use ($key) {
                    return $stat[$key];
                },
                $stats
            );
        }

        return $uccxData;
    }
}
