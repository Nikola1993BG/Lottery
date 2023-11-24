<?php
namespace App;


class Lottery
{
    private Array $bonuses;
    private Array $gamblers;

    private Array $gamblersForBonus;

    private Array $winners = [];

    public function __construct() { }
   
    /**
     * Imports gamblers and their bets into the lottery system.
     *
     * @param array $bets An array of bets, each containing the gambler's name and their bet.
     * @return void
     */
    public function importGamblersAndBets(Array $bets): void
    {
        foreach($bets as $bet){
            if(isset($this->gamblers[$bet['Name']])){
                $this->gamblers[$bet['Name']]->addBet($bet);
            }
            else{
                $this->gamblers[$bet['Name']] = new Gambler($bet['Name']);
                $this->gamblers[$bet['Name']]->addBet($bet);
            }
        }
    }

    /**
     * Import bonuses and assign them to gamblers based on conditions.
     *
     * @param array $bonuses The array of bonuses to import.
     * @return void
     */
    public function importBonuses($bonuses): void
    {

        foreach($bonuses as $bonus){
            $this->bonuses[$bonus['prize']] = $bonus;

            foreach($this->gamblers as $gambler){

                if($gambler->playForBonus === NULL){

                    foreach($bonus['conditions'] as $condition){

                        $type = 'total'.ucfirst($condition['type']);
                        if($this->getCond($gambler->$type, $condition['cond'], $condition['val'])){
                            $gambler->playForBonus = $bonus['prize'];
                        }
                        else{
                            $gambler->playForBonus = null;
                            continue 2;
                        }
                    }
                    $this->gamblersForBonus[$bonus['prize']][] = $gambler;
                }       
            }
        }
       // echo '<pre>'.print_r($this->gamblersForBonus, true).'</pre>';
    }

    /**
     * Play the lottery game and determine the winners.
     */
    public function play()
    {
        $winners = []; $this->winners = [];
        foreach($this->bonuses as $prize => $bonus){

            if(isset($this->gamblersForBonus[$prize])){
                $gamblersForBonusCount = count($this->gamblersForBonus[$prize]);
                $num = $bonus['qty']>$gamblersForBonusCount ? $gamblersForBonusCount : $bonus['qty'];
                $winner = array_rand($this->gamblersForBonus[$prize], $num);
                if(!is_array($winner)) $winner = [$winner];
                $winners[$prize] = $winner;
            }
        }
        foreach($winners as $prize => $winner){
            foreach($winner as $wKey){
                $this->winners[] = $this->gamblersForBonus[$prize][$wKey];
            }
        }
    }

    /**
     * Returns the winners of the lottery.
     *
     * @return array The array of winners.
     */
    public function getWinners(): array
    {
        return $this->winners;
    }

    private function getCond($val1, $operator, $val2){
        switch($operator){
            case '>':
                return $val1 > $val2;
            case '<':
                return $val1 < $val2;
            case '>=':
                return $val1 >= $val2;
            case '<=':
                return $val1 <= $val2;
            case '==':
                return $val1 == $val2;
            case '!=':
                return $val1 != $val2;
        }
    }
    
    /**
     * Retrieves the gambler with the specified name.
     *
     * @param string $name The name of the gambler.
     * @return Gambler|null The gambler object if found, null otherwise.
     */
    public function getGambler($name): ?Gambler
    {
        return $this->gamblers[$name];
    }

    /**
     * Returns the array of gamblers participating in the lottery.
     *
     * @return array The array of gamblers.
     */
    public function getGamblers(): array
    {
        return $this->gamblers;
    }
   
}