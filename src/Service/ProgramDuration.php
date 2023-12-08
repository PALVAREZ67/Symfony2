<?php

namespace App\Service;

use App\Entity\Program;
use App\Entity\Season;

class ProgramDuration
{
    public function calculate(Program $program): string
    {
        $totalDuration = 0;

        foreach($program->getSeasons() as $season) {
            foreach($season->getEpisodes() as $episode) {
                $totalDuration += $episode->getDuration();
            }
        }

        $dayDuration = $totalDuration / 1440;
        $nbDay = substr($dayDuration, 0,1);
        $hourDuration = ($dayDuration - $nbDay) * 24;
        $nbHour = substr($hourDuration, 0, 1);
        $minutesDuration = ($hourDuration - $nbHour) * 60;
        $nbMinutes = floor($minutesDuration);

        return $nbDay . ' jours ' . $nbHour . " heures " . $nbMinutes . " minutes " . " ou " . $totalDuration . " minutes";
    }
}