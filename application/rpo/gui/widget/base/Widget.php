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
 * @subpackage	gui\widget\base
 */
namespace rpo\gui\widget\base;

use rpo\gui\UIObject;
use rpo\gui\composite\Component;

/**
 * Interface para definição de um widget para a interface de usuário
 * @abstract
 * @package		rpo
 * @subpackage	gui\widget\base
 * @license		http://creativecommons.org/licenses/GPL/2.0/legalcode.pt
 */
abstract class Widget extends \rpo\gui\composite\Component {
	/**
	 * Pai do widget
	 * @var \rpo\gui\widget\base\Widget
	 */
	private $father;

	/**
	 * Verifica se o componente aceita um outro componente como filho
	 * @param \rpo\gui\widget\base\Widget $component
	 */
	protected function accept( Widget $component ) {
		return true;
	}

	/**
	 * Adiciona um novo filho ao componente
	 * @param \rpo\gui\composite\Component $component
	 * @see \rpo\gui\composite\Component::appendChild()
	 * @throws \InvalidArgumentException Se o filho não for aceito pelo Widget
	 * @throws \InvalidArgumentException Se não for passado um widget
	 */
	public function appendChild( Component $child ){
		if ( $child->getClass()->isSubclassOf( 'rpo\gui\widget\base\Widget' ) ){
			if ( $this->accept( $child ) ){
				$child->setParent( $this );

				parent::appendChild( $child );
			} else {
				throw new InvalidArgumentException( 'Widget não aceito como filho' );
			}
		} else {
			throw new InvalidArgumentException( 'Era esperado um Widget' );
		}
	}

	/**
	 * Recupera o pai do widget
	 * @return \rpo\gui\widget\base\Widget
	 */
	public function getParent(){
		return $this->father;
	}

	/**
	 * Verifica se o Widget possui um pai
	 * @return boolean
	 */
	public function isOrphan(){
		return is_null( $this->father );
	}

	/**
	 * Remove um filho do componente
	 * @param \rpo\gui\composite\Component $child
	 * @see \rpo\gui\composite\Component::removeChild
	 */
	public function removeChild( Component $child ) {
		parent::removeChild( $child );
		$child->setOrphan();
	}

	/**
	 * Remove o widget de seu pai
	 * @see \rpo\gui\composite\Component::removeChild
	 * @throws \BadMethodCallException Se o filho for orfão
	 * @throws \RuntimeException Se o filho não for orfão, mas não estiver contido na lista de filhos do pai
	 */
	public function removeFromParent(){
		if ( $this->isOrphan() ){
			throw new BadMethodCallException( 'Esse componente é orfão' );
		} else {
			$parent = $this->getParent();

			if ( $parent->contains( $this ) ){
				$parent->removeChild( $this );
			} else {
				throw new RuntimeException( 'Esse componente não está contido na lista de filhos do pai' );
			}
		}
	}

	/**
	 * Define o widget como orfão
	 * @see \rpo\gui\widget\base\Widget::isOrphan
	 * @throws \LogicException Se o widget já for orfão
	 */
	public function setOrphan(){
		if ( $this->isOrphan() ){
			throw new LogicException( 'Esse widget já é orfão' );
		} else {
			$this->father = null;
		}
	}

	/**
	 * Define o pai do widget
	 * @param \rpo\gui\widget\base\Widget $father
	 * @throws \LogicException Se o componente já possuir um pai
	 */
	public function setParent( Widget $father ){
		if ( $this->isOrphan() ){
			$this->father = $father;
		} else {
			throw new LogicException( 'Esse componente já possui um pai.' );
		}
	}
}