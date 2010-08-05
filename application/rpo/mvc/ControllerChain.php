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
 * @brief		Classes e Interfaces para definição de um MVC hierárquico
 * @package		rpo.mvc
 */
namespace rpo\mvc;

use \RuntimeException;
use rpo\mvc\ControllerList;
use rpo\http\HTTPResponse;
use rpo\http\HTTPRequest;

/**
 * Interface para definição de um encadeamento de controladores
 * @class		ControllerChain
 * @extends		Object
 * @implements	Controller
 * @implements	IteratorAggregate
 */
abstract class ControllerChain extends \rpo\base\Object implements \rpo\mvc\Controller, \IteratorAggregate {
	/**
	 * Lista de controladores
	 * @var		ControllerList
	 */
	private $controllers;

	/**
	 * Iterator de controladores
	 * @var		Iterator
	 */
	private $iterator;

	/**
	 * Controlador anterior na hierarquia de controladores
	 * @var		ControllerChain
	 */
	private $previous;

	/**
	 * Constroi o objeto de encadeamento de controladores
	 */
	public function __construct() {
		$this->controllers = new ControllerList();
	}

	/**
	 * Anexa um controlador à aplicação
	 * @param $controller ControllerChain
	 * @return boolean TRUE se o controlador tiver sido anexado com sucesso
	 */
	public function attach( ControllerChain $controller ) {
		if (  !$this->controllers->contains( $controller ) ) {
			$this->controllers->add( $controller );
			$controller->setPrevious( $this );
			$controller->configure( $this );

			return true;
		}

		return false;
	}

	/**
	 * Verifica se o controlador manipula a requisição
	 * @param $request HTTPRequest
	 * @return boolean;
	 */
	public function canHandle( HTTPRequest $request ) {
		$iterator = $this->getIterator();

		for ( $iterator->rewind() ; $iterator->valid() ; $iterator->next() ) {
			$controller = $iterator->current();

			if ( $controller->canHandle( $request ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Configura o controlador
	 * @param $controller ControllerChain
	 */
	protected function configure( ControllerChain $controller ) {
	}

	/**
	 * Remove um controlador da aplicação
	 * @param $controller ControllerChain
	 * @return boolean TRUE se o controlador tiver sido removido
	 */
	public function detach( ControllerChain $controller ) {
		$this->controllers->detach( $controller );

		return  !$this->controllers->contains( $controller );
	}

	/**
	 * Recupera um iterator de controladores
	 * @return Iterator
	 */
	public function getIterator() {
		return $this->controllers->getIterator();
	}

	/**
	 * Recupera a resposta
	 * @return HTTPResponse
	 */
	public function getResponse() {
		$ret = null;

		if ( $this->previous instanceof Controller ) {
			$ret = $this->previous->getResponse();
		}

		if ( $ret instanceof HTTPResponse ) {
			return $ret;
		} else {
			throw new RuntimeException( 'Sem resposta.' );
		}
	}

	/**
	 * Define o controlador anterior
	 * @param $controller ControllerChain
	 */
	public function setPrevious( ControllerChain $controller ) {
		$this->previous = $controller;
	}
}