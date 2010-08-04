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
 * @subpackage	util
 */
namespace rpo\util;

use \Iterator;
use \IteratorIterator;
use \ArrayObject;
use \InvalidArgumentException;
use \ReflectionClass;
use rpo\base\Object;
use rpo\base\BaseObject;
use rpo\util\Collection;

/**
 * Base para implementação da interface Collection.
 * @abstract
 * @package		rpo
 * @subpackage	util
 * @license		http://creativecommons.org/licenses/GPL/2.0/legalcode.pt
 */
abstract class AbstractCollection extends \rpo\base\Object implements \rpo\util\Collection {
	/**
	 * Lista de objetos da coleção
	 * @access	protected
	 * @var		\ArrayObject
	 */
	protected $storage;

	/**
	 * Iterator que será utilizado pelo método getIterator
	 * @var \ReflectionClass
	 */
	private $iterator;

	/**
	 * Constroi um novo objeto Collection
	 */
	public function __construct() {
		$this->storage = new ArrayObject();
		$this->setIteratorClass( 'IteratorIterator' );
	}

	/**
	 * Verifica se um determinado objeto pode ser aceito pela Collection
	 * @abstract
	 * @param \rpo\base\BaseObject $object
	 * @return boolean
	 */
	abstract protected function accept( BaseObject $object );

	/**
	 * Adiciona um novo objeto à coleção
	 * @param \rpo\base\BaseObject $object
	 * @throws \InvalidArgumentException Se o objeto não for aceito pela Collection
	 */
	public function add( BaseObject $object ) {
		if ( $this->accept( $object ) ) {
			$this->storage->append( $object );
		} else {
			throw new InvalidArgumentException( 'O objeto especificado não pode ser aceito por esta coleção.' );
		}
	}

	/**
	 * Adiciona todos os objetos de uma outra coleção à esta coleção
	 * @param \rpo\util\Collection $collection
	 */
	public function addAll( Collection $collection ) {
		foreach ( $collection as $element ) {
			$this->add( $element );
		}
	}

	/**
	 * Limpa a coleção
	 * @see \rpo\util\Collection::isEmpty()
	 */
	public function clear() {
		$this->storage = new ArrayObject();
	}

	/**
	 * Verifica se um determinado objeto está contido na coleção
	 * @param \rpo\base\BaseObject $object
	 * @return boolean
	 */
	public function contains( BaseObject $object ) {
		foreach ( $this as $element ) {
			if ( $object->equals( $element ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Verifica se a coleção contém todos os objetos de uma outra coleção
	 * @param \rpo\util\Collection $collection
	 * @return boolean
	 */
	public function containsAll( Collection $collection ) {
		foreach ( $collection as $element ) {
			if (  !$this->contains( $element ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Recupera o total de elementos na coleção
	 * @return integer
	 * @see \Countable::count()
	 */
	public function count() {
		return $this->storage->count();
	}

	/**
	 * Substitui todos os elementos de uma coleção pelos contidos na matriz
	 * @param array $items Lista de itens
	 * @return \rpo\util\AbstractCollection Retorna a própria instância do objeto
	 */
	public function exchangeArray( array $items ) {
		$this->removeAll();

		foreach ( $items as $item ) {
			if ( $item instanceof \rpo\base\BaseObject ) {
				$this->add( $item );
			} else {
				throw new UnexpectedValueException( 'Todos os itens da matriz devem ser instâncias de BaseObject' );
			}
		}

		return $this;
	}

	/**
	 * Recupera um objeto Iterator para os elementos da Collection
	 * @return \Iterator
	 * @see \IteratorAggregate::getIterator()
	 */
	public function getIterator() {
		return $this->iterator->newInstance( clone $this->storage );
	}

	/**
	 * Recupera a classe que será utilizada para recuperar o Iterator de elementos
	 * da coleção
	 * @return string
	 * @see \IteratorAggregate::getIterator()
	 */
	public function getIteratorClass() {
		return $this->iterator->getName();
	}

	/**
	 * Verifica se a coleção está vazia
	 * @return boolean
	 * @see \rpo\util\Collection::clear()
	 */
	public function isEmpty() {
		return $this->storage->count() == 0;
	}

	/**
	 * Remove um elemento da coleção
	 * @param \rpo\base\BaseObject $object
	 */
	public function remove( BaseObject $object ) {
		foreach ( $this as $offset => $element ) {
			if ( $object->equals( $element ) ) {
				$this->storage->offsetUnset( $offset );
			}
		}
	}

	/**
	 * Remove todos os elementos da coleção que estão contidos em outra coleção
	 * @param \rpo\util\Collection $collection
	 */
	public function removeAll( Collection $collection ) {
		foreach ( $collection as $object ) {
			foreach ( $this as $offset => $element ) {
				if ( $object->equals( $element ) ) {
					$this->storage->offsetUnset( $offset );
				}
			}
		}
	}

	/**
	 * Mantém na coleção apenas os objetos contidos em outra coleção
	 * @param \rpo\util\Collection $collection
	 */
	public function retainAll( Collection $collection ) {
		foreach ( $this as $offset => $object ) {
			if (  !$collection->contains( $object ) ) {
				$this->storage->offsetUnset( $offset );
			}
		}
	}

	/**
	 * Define a classe que será utilizada para retornar o objeto Iterator
	 * @param string $class Nome da classe que implementa a interface Iterator
	 * @see \rpo\util\Collection::getIteratorClass()
	 * @throws \InvalidArgumentException Se a classe especificada não implementar Iterator
	 */
	public function setIteratorClass( $class ) {
		$reflection = new ReflectionClass( $class );

		if (  !$reflection->implementsInterface( '\Iterator' ) ) {
			throw new InvalidArgumentException( 'A classe especificada precisa implementar a interface Iterator' );
		}

		$this->iterator = $reflection;
	}

	/**
	 * Converte o objeto Collection em uma matriz contendo todos os elementos
	 * @return array
	 */
	public function toArray() {
		return $this->storage->getArrayCopy();
	}

	/**
	 * Atualiza os índices da coleção depois de várias operações add() e remove()
	 */
	public function update() {
		$this->storage->exchangeArray( array_values( $this->storage->getArrayCopy() ) );
	}
}