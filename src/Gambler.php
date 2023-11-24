<?php 
namespace App;

class Gambler {
    public Array $bets;
    public String $name;

    public Int $totalBets = 0;

    public Int $totalWin = 0;
    public Int $totalBet = 0;
    public Int $totalLoss = 0;

    public ?Int $playForBonus = null;

    public function __construct($name) {
        $this->name = $name;
       
    }
    /**
     * Adds a new bet to the gambler's list of bets.
     *
     * @param array $bet The details of the bet.
     * @return void
     */
    
    public function addBet(Array $bet): void
    {

        $this->bets[] = new Bet($bet);

        if($bet['Status'] != 'C'){

            $this->totalWin += $bet['Win'];
            $this->totalBet += $bet['Bet'];
            if(($bet['Bet'] - $bet['Win']) > 0) $this->totalLoss += $bet['Bet'] - $bet['Win'];

            $this->totalBets++;
        }
    }
}