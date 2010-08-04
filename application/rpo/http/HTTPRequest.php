<?php
/**
 * Licenciado sobre os termos da CC-GNU GPL versão 2.0 ou posterior.
 *
 * A GNU General Public License é uma licença de Software Livre ("Free Software").
 * Assim como qualquer licença de Software Livre, ela concede a Você o exercício
 * livre dos quatro seguintes direitos:
 *
 * 1. O direito de executar o programa, para qualquer propósito.
 * 2. O direito de estudar como o programa funciona e adptá-lo para suas necessidades.
 * 3. O direito de redistribuir cópias, permitindo assim que você ajude outras pessoas.
 * 4. O direito de aperfeiçoar o programa, e distribuir seus aperfeiçoamentos para o público,
 * beneficiando assim toda a comunidade.
 *
 * Você terá os direitos acima especificados contanto que Você cumpra com os requisitos expressos
 * nesta Licença.
 *
 * Os principais requisitos são:
 *
 * Você deve publicar de forma ostensiva e adequada, em cada cópia, um aviso de direitos autorais
 * (ou "copyright") e uma notificação sobre a exoneração de garantia. Além disso, Você deve manter
 * intactas todas as informações, avisos e notificações referentes à Licença e à ausência de qualquer
 * garantia. Você deve também fornecer a qualquer outra pessoa que receba este Programa uma cópia
 * desta Licença em conjunto com o Programa. Qualquer tradução da GNU General Public License deverá
 * estar acompanhada da GNU General Public License (original em lnglês).
 *
 * Se Você alterar ou transformar a obra, ou desenvolver outra obra baseada nela, Você poderá distribuir
 * o trabalho resultante desde que sob uma licença idêntica a esta. Qualquer tradução da GNU General
 * Public License deverá estar acompanhada da GNU General Public License (original em lnglês).
 *
 * Se Você copiar ou distribuir a obra, você deve incluir junto com ela o seu código-fonte correspondente
 * completo, passível de leitura pela máquina, ou incluir uma oferta por escrito para fornecer o código-fonte,
 * válida por pelo menos 3 anos.
 *
 * COMO O PROGRAMA É LICENCIADO SEM CUSTO, NÃO HÁ NENHUMA GARANTIA PARA O PROGRAMA, NO LIMITE PERMITIDO PELA LEI
 * APLICÁVEL. EXCETO QUANDO DE OUTRA FORMA ESTABELECIDO POR ESCRITO, OS TITULARES DOS DIREITOS AUTORAIS E/OU OUTRAS
 * PARTES, FORNECEM O PROGRAMA "NO ESTADO EM QUE SE ENCONTRA", SEM NENHUMA GARANTIA DE QUALQUER TIPO, TANTO EXPRESSA
 * COMO IMPLÍCITA, INCLUINDO, DENTRE OUTRAS, AS GARANTIAS IMPLÍCITAS DE COMERCIABILIDADE E ADEQUAÇÃO A UMA FINALIDADE
 * ESPECÍFICA. O RISCO INTEGRAL QUANTO À QUALIDADE E DESEMPENHO DO PROGRAMA É ASSUMIDO POR VOCÊ. CASO O PROGRAMA
 * CONTENHA DEFEITOS, VOCÊ ARCARÁ COM OS CUSTOS DE TODOS OS SERVIÇOS, REPAROS OU CORREÇÕES NECESSÁRIAS.
 *
 * EM NENHUMA CIRCUNSTÂNCIA, A MENOS QUE EXIGIDO PELA LEI APLICÁVEL OU ACORDADO POR ESCRITO, QUALQUER TITULAR DE
 * DIREITOS AUTORAIS OU QUALQUER OUTRA PARTE QUE POSSA MODIFICAR E/OU REDISTRIBUIR O PROGRAMA, CONFORME PERMITIDO
 * ACIMA, SERÁ RESPONSÁVEL PARA COM VOCÊ POR DANOS, INCLUINDO ENTRE OUTROS, QUAISQUER DANOS GERAIS, ESPECIAIS,
 * FORTUITOS OU EMERGENTES, ADVINDOS DO USO OU IMPOSSIBILIDADE DE USO DO PROGRAMA (INCLUINDO, ENTRE OUTROS, PERDAS
 * DE DADOS OU DADOS SENDO GERADOS DE FORMA IMPRECISA, PERDAS SOFRIDAS POR VOCÊ OU TERCEIROS OU A IMPOSSIBILIDADE DO
 * PROGRAMA DE OPERAR COM QUAISQUER OUTROS PROGRAMAS), MESMO QUE ESSE TITULAR, OU OUTRA PARTE, TENHA SIDO ALERTADA
 * SOBRE A POSSIBILIDADE DE OCORRÊNCIA DESSES DANOS.
 *
 * @author		João Batista Neto
 * @copyright	Copyright(c) 2010, João Batista Neto
 * @license		http://creativecommons.org/licenses/GPL/2.0/deed.pt
 * @license		http://creativecommons.org/licenses/GPL/2.0/legalcode.pt
 * @package		rpo
 * @subpackage	http
 */
namespace rpo\http;

use \ReflectionFunction;
use \ReflectionException;
use rpo\http\HTTPHeaderSet;
use rpo\http\HTTPBody;
use rpo\http\header\fields\Protocol;

/**
 * Representação da requisição do usuário
 * @final
 * @package		rpo
 * @subpackage	http
 * @license		http://creativecommons.org/licenses/GPL/2.0/legalcode.pt
 */
final class HTTPRequest extends \rpo\base\Object implements \rpo\http\HTTPIO {
	/**
	 * Instância única da requisição do usuário
	 * @access	private
	 * @var		\rpo\http\HTTPRequest
	 * @staticvar
	 */
	private static $instance;

	/**
	 * Corpo da requisição
	 * @access	private
	 * @var		\rpo\http\HTTPBody
	 */
	private $body;

	/**
	 * Lista de cabeçalhos da requisição
	 * @access	private
	 * @var		\rpo\http\HTTPHeaderSet
	 */
	private $headers;

	/**
	 * Protocolo utilizado pela requisição
	 * @var string
	 */
	private $protocol;

	/**
	 * Versão do protocolo
	 * @var float
	 */
	private $protocolVersion;

	/**
	 * URI da requisição do usuário
	 * @access	private
	 * @var		string
	 */
	private $uri;

	/**
	 * Constroi o objeto de requisição
	 * @param string $base Diretório base da requisição (eq: RewriteBase)
	 */
	private function __construct( $base ) {
		$match = array();

		if ( preg_match( '/(?<protocol>[^\/]+)\/(?<version>[1-9]+(\.[0-9]+))/' , $_SERVER[ 'SERVER_PROTOCOL' ] , $match ) ){
			$this->protocol = $match[ 'protocol' ];
			$this->protocolVersion = (float) $match[ 'version' ];
		}

		$this->uri = preg_replace( sprintf( '/^%s(.*)/' , preg_quote( $base , '/' ) ) , '$1' , $_SERVER[ 'REQUEST_URI' ] );
	}

	/**
	 * Recupera o corpo da requisição ou resposta
	 * @return \rpo\http\HTTPBody
	 */
	public function getBody() {
		if ( is_null( $this->body ) ) {
			$this->body = new HTTPBody();
			$this->body->get = (object) $_GET;
			$this->body->post = (object) $_POST;
		}

		return $this->body;
	}

	/**
	 * Recupera a lista de cabeçalhos de entrada ou saída
	 * @return rpo\http\HTTPHeaderSet
	 */
	public function getHeaders() {
		if ( is_null( $this->headers ) ) {
			try {
				$getallheaders = new ReflectionFunction( 'getallheaders' );

				$this->headers = new HTTPHeaderSet( $getallheaders->invoke() );
			} catch ( ReflectionException $e ) {
				$this->headers = new HTTPHeaderSet();
			}
		}

		return $this->headers;
	}

	/**
	 * Recupera o método de requisição feita pelo usuário
	 * @return string
	 * @see HTTPRequestMethod::DELETE, HTTPRequestMethod::GET, HTTPRequestMethod::POST, HTTPRequestMethod::PUT
	 */
	public function getMethod() {
		return $_SERVER[ 'REQUEST_METHOD' ];
	}

	/**
	 * Recupera o protocolo utilizado pela requisição
	 * @return string
	 */
	public function getProtocol() {
		return $this->protocol;
	}

	/**
	 * Recupera a versão do protocolo
	 * @return float
	 */
	public function getProtocolVersion() {
		return $this->protocolVersion;
	}

	/**
	 * Recupera o URI da requisição do usuário
	 * @return string
	 */
	public function getURI() {
		return $this->uri;
	}

	/**
	 * Recupera a instância do objeto de requisição do usuário
	 * @return rpo\http\HTTPRequest
	 */
	public static function getInstance( $base = '/' ) {
		if ( self::$instance == null ) {
			self::$instance = new HTTPRequest( $base );
		}

		return self::$instance;
	}
}