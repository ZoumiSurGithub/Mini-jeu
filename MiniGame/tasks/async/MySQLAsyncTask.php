<?php

namespace Zoumi\MiniGame\tasks\async;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use Zoumi\MiniGame\DataBase;
use Zoumi\MiniGame\Main;

class MySQLAsyncTask extends AsyncTask {

    private $query;
    private $time;

    public function __construct(string $query)
    {
        $this->query = $query;
    }

    public function onRun()
    {
        try {
            $this->time = time();
            DataBase::getData()->query($this->query);
        }catch (\mysqli_sql_exception $exception){

        }
    }

    public function onCompletion(Server $server)
    {
        $server->getLogger()->warning("Requête effetcué en " . Main::getInstance()->convert(time() - $this->time));
    }

}