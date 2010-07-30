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
 *    beneficiando assim toda a comunidade.
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
 * @subpackage	mvc
 */
namespace rpo\mvc;

use rpo\util;

use rpo\http;

use rpo\http\HTTPRequest;
use rpo\http\HTTPResponse;
use rpo\http\exception\HTTPException;
use rpo\http\exception\InternalServerErrorException;
use rpo\http\header\fields\Protocol;

/**
 * Controlador principal da aplicação
 * @final
 * @package		rpo
 * @subpackage	mvc
 * @license		http://creativecommons.org/licenses/GPL/2.0/legalcode.pt
 */
final class Application extends \rpo\mvc\ControllerChain {
	/**
	 * Objeto de resposta
	 * @var rpo\http\HTTPResponse
	 */
	private $response;

	/**
	 * Constroi o controlador principal da aplicação
	 */
	public function __construct(){
		parent::__construct();

		$this->response = HTTPResponse::getInstance();
	}

	/**
	 * Cria a exibição padrão de erro
	 * @param HTTPException $e
	 * @FIXME Nesse momento estamos fazendo a saída (echo $e->getMessage()) diretamente daqui
	 * Isso deve ser corrigido com a criação de uma ErrorView, reponsável pela exibição de
	 * mensagens de erro da aplicação
	 */
	private function createErrorResponse( HTTPException $e ){
		$this->getResponse()->getHeaders()->add( new Protocol( Protocol::HTTP_1_1 , $e->getCode() ) );
		$this->getResponse()->getBody()->getComposite()->clear();
		$this->getResponse()->show();
		echo $e->getMessage();
	}

	/**
	 * Recupera o objeto de resposta
	 * @return rpo\http\HTTPResponse
	 */
	public function getResponse(){
		return $this->response;
	}

	/**
	 * Repassa a requisição do usuário à todos os controladores anexados
	 * @param rpo\http\HTTPRequest $request
	 */
	public function handle( HTTPRequest $request ){
		$iterator = $this->getIterator();

		for ( $iterator->rewind() ; $iterator->valid() ; $iterator->next() ){
			try {
				$applicationController = $iterator->current();

				if ( $applicationController->canHandle( $request ) ){
					$applicationController->handle( $request );
				}

				$this->getResponse()->getHeaders()->add( new \rpo\http\header\fields\XPoweredBy( 'RPO-0.1' ) );
				$this->getResponse()->show();
			} catch ( HTTPException $e ){
				$this->createErrorResponse( $e );
				break;
			} catch ( \Exception $e ){
				$this->createErrorResponse( new InternalServerErrorException( $e->getMessage() , $e ) );
				break;
			}
		}
	}
}