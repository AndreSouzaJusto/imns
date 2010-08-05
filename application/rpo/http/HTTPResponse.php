<?php
/**
 * @file
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
 * http://creativecommons.org/licenses/GPL/2.0/deed.pt
 * http://creativecommons.org/licenses/GPL/2.0/legalcode.pt
 */

/**
 * @brief		Classes e interfaces relacionadas com o protocolo HTTP
 * @package		rpo.http
 */
namespace rpo\http;

use rpo\http\HTTPHeaderSet;
use rpo\http\HTTPBody;
use rpo\http\header\fields\ContentMD5;

/**
 * Representação da resposta de uma requisição HTTP
 * @class		HTTPResponse
 * @extends		Object
 * @implements	HTTPIO
 */
final class HTTPResponse extends \rpo\base\Object implements \rpo\http\HTTPIO {
	/**
	 * Instância única do objeto HTTPResponse
	 * @var		HTTPResponse
	 */
	private static $instance;

	/**
	 * Corpo da resposta HTTP
	 * @var		\HTTPBody
	 */
	private $body;

	/**
	 * Lista de cabeçalhos HTTP que serão enviados
	 * @var		HTTPHeaderSet
	 */
	private $headers;

	/**
	 * Constroi o objeto de resposta e inicia o buffer de saída
	 */
	private function __construct() {
		$this->body = new HTTPBody();
		$this->headers = new HTTPHeaderSet();

		ob_start();
	}

	/**
	 * Recupera o corpo da requisição ou resposta
	 * @return HTTPBody
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * Recupera a lista de cabeçalhos de entrada ou saída
	 * @return HTTPHeaderSet
	 */
	public function getHeaders() {
		return $this->headers;
	}

	/**
	 * Recupera a instância do objeto
	 * @return HTTPResponse
	 */
	public static function getInstance() {
		if ( self::$instance == null ) {
			self::$instance = new HTTPResponse();
		}

		return self::$instance;
	}

	/**
	 * Exibe a resposta
	 */
	public function show() {
		if ( ob_get_length() ){
			/**
			 * Limpando o buffer, caso alguma coisa tenha saído antes da hora
			 */
			ob_end_clean();
		}

		/**
		 * Montando o corpo da resposta
		 */
		$this->body->getComposite()->draw();

		/**
		 * Capturamos o conteúdo do corpo da resposta e calculamos o MD5.
		 * Esse hash, codificado com base64 será utilizado pelo client para ter certeza
		 * que o conteúdo recebido não foi modificado no caminho.
		 */
		$buffer = ob_get_contents();
		$contentMD5 = new ContentMD5( base64_encode( md5( $buffer , true ) ) );

		/**
		 * Verificamos se já existe um Content-MD5 na lista de cabeçalhos.
		 * Se houver, devido a uma outra saída, removemos-o para dar lugar ao novo hash
		 */
		if ( $this->headers->contains( $contentMD5 ) ){
			$this->headers->remove( $contentMD5 );
		}

		/**
		 * Adicionamos o campo de cabeçalho Content-MD5 com o hash do conteúdo servido
		 */
		$this->headers->add( $contentMD5 );
		$this->headers->update();

		/**
		 * Enviando os cabeçalhos de resposta HTTP
		 */
		foreach ( $this->headers as $header ) {
			header( (string) $header , true , $header->getStatusCode() );
		}

		/**
		 * Liberamos o buffer com a resposta
		 */
		ob_end_flush();
	}
}