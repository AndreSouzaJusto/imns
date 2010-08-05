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
 * @brief		Cabeçalhos HTTP
 * @package		rpo.http.header
 */
namespace rpo\http\header;

use SplPriorityQueue;

/**
 * Base para implementação de um campo de cabeçalho que possui prioridade
 * @class		AbstractHTTPPriorityHeaderField
 * @extends		AbstractHTTPHeaderField
 * @implements	HTTPPriorityHeaderField
 */
abstract class AbstractHTTPPriorityHeaderField extends \rpo\http\header\AbstractHTTPHeaderField implements \rpo\http\header\HTTPPriorityHeaderField {
	/**
	 * Fila de cabeçalhos segundo sua prioridade
	 * @var	SplPriorityQueue
	 */
	private $queue;

	/**
	 * Constroi um campo de cabeçalho HTTP com valores prioritários
	 * @param $name string Nome do cabeçalho
	 * @param $value string Valor do cabeçalho
	 * @param $status integer Código de status
	 */
	public function __construct( $name , $value , $status = null ) {
		parent::__construct( $name , $value , $status );

		$this->queue = new SplPriorityQueue();
		$this->queue->setExtractFlags( SplPriorityQueue::EXTR_BOTH );
		$this->parse( $value );
	}

	/**
	 * Interpreta o valor do cabeçalho
	 * @param $value string
	 */
	private function parse( $value ) {
		$match = array();

		if ( preg_match_all( '/,?(?<values>[^;]+)(;q\=(?<priority>[0-9](\.[0-9]+)?))?/' , $value , $match ) ) {
			$items = array_combine( $match[ 'values' ] , $match[ 'priority' ] );

			foreach ( $items as $value => $priority ) {
				$priority = (float) ( empty( $priority ) ? 1 : $priority );
				$parts = explode( ',' , $value );

				foreach ( $parts as $item ) {
					$this->queue->insert( $item , $priority );
				}
			}
		}
	}

	/**
	 * Recupera o valor atual
	 * @return string
	 * @see AbstractHTTPPriorityHeaderField::getValue()
	 */
	public function current() {
		return $this->getValue();
	}

	/**
	 * Recupera o valor atual
	 * @return string
	 */
	public function getValue() {
		$current = $this->queue->current();

		return $current[ 'data' ];
	}

	/**
	 * Recupera a prioridade do valor
	 * @return float
	 */
	public function getPriority() {
		$current = $this->queue->current();

		return $current[ 'priority' ];
	}

	/**
	 * Recupera o offset do item atual
	 * @return integer
	 * @see Iterator::key()
	 */
	public function key() {
		return $this->queue->key();
	}

	/**
	 * Avança para o próximo item
	 * @see Iterator::next()
	 */
	public function next() {
		$this->queue->next();
	}

	/**
	 * Reinicia o Iterator
	 * @see Iterator::rewind()
	 */
	public function rewind() {
		$this->queue->rewind();
	}

	/**
	 * Verifica se o Iterator é válido
	 * @see Iterator::rewind()
	 */
	public function valid() {
		return $this->queue->valid();
	}
}