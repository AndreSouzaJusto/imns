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
 * @copyright	Copyright(c) 2010
 * @license		http://creativecommons.org/licenses/GPL/2.0/deed.pt
 * @license		http://creativecommons.org/licenses/GPL/2.0/legalcode.pt
 * @package		com
 * @subpackage	base
 * @version     $Id$
 */
namespace com\base;

use rpo\mvc\Controller;
use rpo\mvc\ControllerChain;
use rpo\http\HTTPRequest;
use rpo\http\HTTPRequestMethod;
use rpo\http\header\fields\Protocol;

/**
 * Controlador base da rede social
 * @final
 * @license		http://creativecommons.org/licenses/GPL/2.0/legalcode.pt
 * @package		com
 * @subpackage	base
 * @version     $Id$
 */
final class Imns extends \rpo\mvc\ControllerChain {

	/**
	 * @var \com\math\MathView
	 */
	private $view;

	/**
	 * Lista de Content-Types que poderão ser enviados como Accept pelo usuário
	 * e que são aceitos pela aplicação.
	 * @var array
	 */
	private $contentTypes = array( 'text/html' , 'application/xhtml+xml' );

	/**
	 * Lista de idiomas que poderão ser enviados como Accept-Language pelo usuário
	 * e que são aceitos pela aplicação.
	 * @var array
	 */
	private $languages = array( 'pt-br' );

	/**
	 * Configura o controlador
	 * @param \rpo\mvc\ControllerChain $controller
	 */
	protected function configure( ControllerChain $controller ) {
		//TODO: Adicionar os controladores da rede social
	}

	/**
	 * Verifica se o controlador manipula a requisição
	 * @param \rpo\http\HTTPRequest $request
	 * @return boolean
	 */
	public function canHandle( HTTPRequest $request ) {
		$method = $request->getMethod();
		$headers = $this->getResponse()->getHeaders();

		/**
		 * Verificação do protocolo e versão. Apenas servimos requisições HTTP
		 */
		if ( $request->getProtocol() == Protocol::HTTP ) {
			/**
			 * Verificamos a versão do protocolo, se for HTTP/1.0, enviaremos o campo Upgrade informando
			 * que estamos entregando o conteúdo utilizando HTTP/1.1
			 * Caso a versão HTTP seja diferente de HTTP/1.0 e HTTP/1.1, adicionamos o cabeçalho HTTP-Version
			 * informando o protocolo HTTP/1.1 e disparamos a exceção HttpVersionNotSupportedException.
			 */
			if ( $request->getProtocolVersion() == Protocol::HTTP_1_0 ){
				$headers->add( new \rpo\http\header\fields\Upgrade( Protocol::HTTP , Protocol::HTTP_1_1 ) );
			} elseif ( $request->getProtocolVersion() != Protocol::HTTP_1_1 ){
				$headers->add( new \rpo\http\header\fields\HTTPVersion( Protocol::HTTP , Protocol::HTTP_1_1 ) );
				throw new \rpo\http\exception\HttpVersionNotSupportedException( 'Protocolo não suportado.' );
			}

			if ( ( $method == HTTPRequestMethod::GET ) || ( $method == HTTPRequestMethod::POST ) ) {

				foreach ( $request->getHeaders() as $header ) {
					switch ( $header->getClass()->getName() ) {
						case 'rpo\http\header\fields\Accept' :
							$acceptable = false;

							foreach ( $header as $priority ) {
								if ( ( $priority == '*' ) || in_array( $priority , $this->contentTypes ) ) {
									$acceptable = true;
									$contentType = $priority == '*' ? array_shift( $this->acceptable ) : $priority;

									$headers->add( new \rpo\http\header\fields\ContentType( $contentType ) );

									break;
								}
							}

							if (  !$acceptable ) {
								throw new \rpo\http\exception\NotAcceptableException( 'Not Acceptable' );
							}
							break;

						case 'rpo\http\header\fields\AcceptLanguage' :
							$acceptable = false;

							foreach ( $header as $priority ) {
								if ( ( $priority == '*' ) || in_array( $priority , $this->languages ) ) {
									$acceptable = true;
									$contentLanguage = ( $priority == '*' ) ? array_shift( $this->contentLanguages ) : $priority;
									$headers->add( new \rpo\http\header\fields\ContentLanguage( $contentLanguage ) );
								}
							}

							if (  !$acceptable ) {
								throw new \rpo\http\exception\NotAcceptableException( 'A aplicação não suporta o idioma requerido.' );
							}
							break;

						case 'rpo\http\header\fields\Connection' :
							$value = $header->getValue();

							if ( ( $value == 'keep-alive' ) || ( $value == 'close' ) ) {
								$headers->add( new \rpo\http\header\fields\Connection( $value ) );
							} else {
								throw new \rpo\http\exception\BadRequestException( 'Bad Request' );
							}
							break;

						case 'rpo\http\header\fields\KeepAlive' :
							$keepAlive = $header->getValue();

							if ( (int) $keepAlive == $keepAlive ) {
								$headers->add( new \rpo\http\header\fields\KeepAlive( (int) $keepAlive ) );
							} else {
								throw new \rpo\http\exception\BadRequestException( 'Bad Request' );
							}
							break;

						case 'rpo\http\header\fields\AcceptCharset' :
							\rpo\base\String::setDefaultEncoding( $header->getValue() );
							break;

						case 'rpo\http\header\fields\Host' :
							if ( $header->getPort() != 80 ){
								throw new \rpo\http\exception\BadRequestException( 'A porta utilizada pela requisição é inválida.' );
							}
					}

				}

			} else {
				$headers->add( new \rpo\http\header\fields\Allow( 'GET, POST' ) );
				throw new \rpo\http\exception\MethodNotAllowedException( 'Método não permitido' );
			}
		} else {
			throw new \rpo\http\exception\BadRequestException( 'Protocolo não suportado.' );
		}

		return parent::canHandle( $request );
	}

	/**
	 * Manipula a requisição do usuário
	 * @param \rpo\http\HTTPRequest $request
	 */
	public function handle( HTTPRequest $request ) {
		foreach ( $this->getIterator() as $controller ) {
			if ( $controller->canHandle( $request ) ) {
				$controller->handle( $request );
			}
		}
	}
}