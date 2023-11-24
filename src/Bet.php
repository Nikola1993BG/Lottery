<?php
namespace App;
class Bet {

    private Int $win;
    private Int $bet;
    private String $status;

   
    public function __construct(Array $bet) 
    { 
        $this->bet = $bet['Bet'];
        $this->win = $bet['Win'];
        $this->status = $bet['Status'];

    }

}