<?php
/*
 * Carrega as configurações
 */
require '../application/config.php';

/*
 * Define os namespaces usados
 */
use rpo\mvc\Application;
use rpo\http\HTTPRequest;

/*
 * Cria nossa aplicação e adiciona uma instância Imns
 * A classe Imns é o controlador base da rede social
 */
$application = new Application();
$application->attach( new com\base\Imns() );

/*
 * Executa tudo!
 * Para isso primeiro é criada uma nova instância da classe HTTPRequest que representa a requisição do usuário
 * Esse objeto será passado para cada controlador fazer sua parte com o método handle() (Veja: rpo\mvc\Controller.php)
 */
$application->handle( HTTPRequest::getInstance( '/imns/' ) );