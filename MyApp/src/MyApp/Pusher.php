<?php

namespace MyApp;

use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class Pusher implements WampServerInterface
{

    protected $total_subscribers = 0;
    protected $paraServico;

    public function __construct($paraServico)
    {
        $this->paraServico = $paraServico;
    }

    /**
     * Chamado para encerrar todo o serviço do webservice caso necessário.
     */
    protected function parar()
    {
        call_user_func($this->paraServico);
    }

    /**
     * Chamada quando um cliente se increve em um tópico
     *
     * @param ConnectionInterface $conexao
     * @param Topic $topico
     */
    public function onSubscribe(ConnectionInterface $conexao, $topico)
    {
        // Adiciona o novo assinante para a lista de inscritos nesse tópico
        $this->inscritosTopicos[$topico->getId()] = $topico;
    }

    /**
     * Chamado quando um cliente cancela a inscrição de um tópico
     *
     * @param ConnectionInterface $conexao
     * @param Topic $topico
     */
    public function onUnSubscribe(ConnectionInterface $conexao, $topico)
    {

    }

    /**
     * Executado quando um cliente inicia a conexão
     *
     * @param ConnectionInterface $conexao
     */
    public function onOpen(ConnectionInterface $conexao)
    {
    }

    /**
     * Executado qunado um cliente encerra a conexão
     *
     * @param ConnectionInterface $conexao
     */
    public function onClose(ConnectionInterface $conexao)
    {
        foreach ($this->inscritosTopicos as $topico) {
            if ($topico->has($conexao)) {
                $topico->remove($conexao);
                $this->onUnSubscribe($conexao, $topico);
                break;
            }
        }

    }

    /**
     * Usado quando um cliente envia dados
     *
     * @param ConnectionInterface $conexao
     * @param string $id
     * @param Topic $topico
     * @param array $parametros
     */
    public function onCall(ConnectionInterface $conexao, $id, $topico, array $parametros)
    {
        $conexao->callError($id, $topico, 'Você não tem permissão para fazer chamadas')->close();
    }

    /**
     * Usado por clientes para publicar mensagens em um tópico
     *
     * @param ConnectionInterface $conexao
     * @param Topic $topico
     * @param string $evento
     * @param array $listaNegra Uma lista de IDs de sessão em que a mensagem deve ser excluída (blackslist)
     * @param array $listaBranca Uma lista de IDs de sessão para os quais a mensagem deve ser enviada (whitelist)
     */
    public function onPublish(ConnectionInterface $conexao, $topico, $evento, array $listaNegra, array $listaBranca)
    {
        $topico->broadcast($evento);
    }

    public function onError(ConnectionInterface $conexao, \Exception $e)
    {

    }
}
