<?php
namespace App\Controllers;

use App\Core\View;
use App\Lottery;
class HomeController{

    /**
     * This method is the index action of the HomeController.
     * It retrieves data from files, initializes the casino object, and handles the play action.
     * It also renders the index.html view with the necessary data.
     *
     * @return void
     */
    public function index(): void
    {

        $bets = [];

        if(file_exists(ROOT_PATH.'/storage/bets.csv')){

            $bets = array_map('str_getcsv', file(ROOT_PATH.'/storage/bets.csv'));
            array_walk($bets, function(&$a) use ($bets) {
                $a = array_combine($bets[0], $a);
            });
            array_shift($bets);
        }

        $bonuses = [];
        if(file_exists(ROOT_PATH.'/storage/algorithm.json')){
            $algorithm = file_get_contents(ROOT_PATH.'/storage/algorithm.json');
            
            $algorithm = json_decode($algorithm,true);  

            $bonuses = $algorithm['bonuses'];

            uasort($bonuses, function ($a, $b) {
                $a = count($a['conditions']) - $a['prize']/10000;
                $b = count($b['conditions']) - $b['prize']/10000;
                return ($a == $b) ? 0 : (($a < $b) ? 1 : - 1);
            });
    
        }


        if(file_exists(ROOT_PATH.'/storage/store')){
            $casino = file_get_contents(ROOT_PATH.'/storage/store');
            $casino = unserialize($casino);
            
        }
        else{
            $casino = new Lottery();
            $casino->importGamblersAndBets($bets);
            $casino->importBonuses($bonuses);            

            $s = serialize($casino);
            file_put_contents(ROOT_PATH.'/storage/store', $s);
        }

        if(isset($_POST['play'])){

            if(file_exists(ROOT_PATH.'/storage/store')){
                unlink(ROOT_PATH.'/storage/store');
            }

            $casino->play();
            $s = serialize($casino);
            file_put_contents(ROOT_PATH.'/storage/store', $s);

            header('Location: /');

        }

        $winners = $casino->getWinners();
        $allGamblers = $casino->getGamblers();

     //   echo '<pre>'.print_r($casino->getWinners(), true).'</pre>';
       
        View::renderTwig('index.html', 
        [
            'bets'=>$bets, 
            'winners'=>$winners, 
            'bonuses'=>$bonuses  
        ]);
    }

    /**
     * Uploads a file to the server.
     *
     * @throws \Exception If the file type is not supported.
     */
   
    public function upload(): void
    {
        $file = $_FILES['file'];
        $fileOrigName = $file['name'];
        $fileTmpName = $file['tmp_name'];
       
        $fileExt = explode('.', $fileOrigName);
        $fileActualExt = strtolower(end($fileExt));

        if($fileActualExt =='json'){
            $fileName = 'algorithm.json';
        }
        else if($fileActualExt =='csv'){
            $fileName = 'bets.csv';
        }
        else{
            throw new \Exception('File type not supported');
        }

        $targetFile = ROOT_PATH.'/storage/'.$fileName;

        if (move_uploaded_file($fileTmpName, $targetFile)) {

            if(file_exists(ROOT_PATH.'/storage/store')){
                unlink(ROOT_PATH.'/storage/store');
            }

            header('Location: /');
        } else {
            throw new \Exception('File type not supported');
        }
    }

    
}