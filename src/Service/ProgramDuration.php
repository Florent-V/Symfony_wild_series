<?php

namespace App\Service;


use App\Entity\Program;

class ProgramDuration
{
    public function calculate(Program $program): string
    {
        $length = 0;
        foreach ($program->getSeasons() as $season) {
            foreach ($season->getEpisodes() as $episode) {
                $length += $episode->getDuration();
            }
        }


        return strval($length);
    }
}