<?php

namespace MyApp;

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\Wamp\WampServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use React\Socket\Server;

/**
 * WAMP server feito com Ratchet PHP
 */
class LoopController
{
    private $ip;
    private $porta;

    private $ioserver = null;
    private $wampServer = null;
    protected $loop;

    public function __construct($porta = 8088, $ip = '0.0.0.0')
    {
        $this->porta = $porta;
        $this->ip = $ip;
    }

    /**
     * Cria e ativa o WAMP server
     */
    public function iniciaServico()
    {
        if (empty($this->ioserver)) {
            // Configura o servidor WebSocket
            $this->loop = Factory::create();
            $webSock = new Server($this->loop);
            $webSock->listen($this->porta, $this->ip);

            // Configurar um objeto de servidor Wamp para manipular assinaturas
            $pusher = new Pusher(array($this, 'paraServico'));
            $this->wampServer = new WampServer($pusher);

            // Configurar um servidor de I/O para manipular os eventos de baixo nível (leitura / gravação) de um soquete
            $this->ioserver = new IoServer(new HttpServer(new WsServer($this->wampServer)), $webSock, $this->loop);
            $this->ioserver->run();
        }
    }

    /**
     * Para o serviço do wamp server
     */
    public function paraServico()
    {
        $this->loop->stop();
    }

}
