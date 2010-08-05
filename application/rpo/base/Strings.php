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
 * @brief		Pacote base da RPO
 * @details		Toda estrutura da RPO baseia-se no pacote base, onde são definidos a interface
 * BaseObject e o objeto Object dos quais grande parte dos objetos da RPO derivam.
 * @package		rpo.base
 */
namespace rpo\base;

use \stdClass;
use \InvalidArgumentException;
use \UnexpectedValueException;
use rpo\base\BaseObject;

/**
 * @brief		Implementação do objeto string.
 * @class		Strings
 * @details		A classe Strings tem como objetivo normalizar os trabalhos com string, deixando o
 * trabalho de se lidar com codificação encapsulado nela; Dessa forma, o desenvolvedor precisará
 * se preocupar apenas em definir a codificação padrão da aplicação.
 * @extends		Object
 * @author		João Batista Neto
 * @package		rpo.base
 */
class Strings extends \rpo\base\Object {
	/**
	 * Codificação padrão para novas strings
	 * @var string
	 */
	private static $encoding;

	/**
	 * Dados da string
	 * @var stdClass
	 */
	private $storage;

	/**
	 * Constroi a nova string
	 * @param $string string
	 * @throws UnexpectedValueException Se o parâmetro passado não for uma string
	 */
	public function __construct( $string = '' ) {
		if ( is_string( $string ) ) {
			$this->storage = new stdClass();
			$this->storage->encoding = mb_detect_encoding( $string );
			$this->storage->data = $string;

			if (  !is_null( self::$encoding ) ) {
				$this->convertEncoding( self::$encoding );
			} else {
				self::$encoding = $this->storage->encoding;
			}
		} else {
			throw new UnexpectedValueException( 'Esperávamos uma string' );
		}
	}

	/**
	 * Recupera a string
	 * @return string
	 */
	public function __toString() {
		return $this->storage->data;
	}

	/**
	 * Recupera o caracter na posição definida por offset
	 * @param $offset integer
	 * @return string
	 * @throws \InvalidArgumentException Se o argumento não for um inteiro
	 */
	public function charAt( $offset ) {
		$ret = null;

		if ( is_int( $offset ) ) {
			$ret = mb_substr( $this->storage->data , $offset , 1 , $this->getEncoding() );
		} else {
			throw new InvalidArgumentException( sprintf( '%s espera um inteiro, %s foi dado.' , __METHOD__ , gettype( $offset ) ) );
		}

		return $ret;
	}

	/**
	 * Recupera uma matriz com os códigos dos caracteres utilizados para representar
	 * um caracter na posição definida por $offset
	 * @param $offset integer
	 * @return array
	 */
	public function charCodeAt( $offset ) {
		$ret = unpack( 'C*' , $this->charAt( $offset ) );
		$ret = array_combine( range( 0 , sizeof( $ret ) - 1 ) , $ret );

		return $ret;
	}

	/**
	 * Compara uma string com a atual
	 * @param $string Strings
	 * @return integer Retorna < 0 se a string atual for menor que a comparada, 0 se as duas forem iguais e > 0 se a comparada for maior
	 */
	public function compareTo( Strings $string ) {
		return strcmp( $this->storage->data , $string->__toString() );
	}

	/**
	 * Compara uma string com a atual ignorando a caixa
	 * @param $string Strings
	 * @return integer Retorna < 0 se a string atual for menor que a comparada, 0 se as duas forem iguais e > 0 se a comparada for maior
	 */
	public function compareToCaseIgnore( Strings $string ) {
		return strcmp( $this->toLowerCase() , $string->toLowerCase() );
	}

	/**
	 * Concatena uma string com a atual
	 * @param $string Strings
	 * @return Strings
	 */
	public function concat( Strings $string ) {
		$string->convertEncoding( $this->getEncoding() );
		$this->storage->data .= $string;
		$ret = new Strings( $this->storage->data );

		return $ret;
	}

	/**
	 * Converte uma string para uma nova codificação
	 * @param $encoding string
	 * @throws InvalidArgumentException Se a codificação for inválida
	 */
	public function convertEncoding( $encoding ) {
		if ( self::testEncoding( $encoding ) ) {
			$this->storage->data = mb_convert_encoding( $this->storage->data , $encoding , $this->storage->encoding );
			$this->storage->encoding = $encoding;
		} else {
			throw new InvalidArgumentException( 'Codificação inválida.' );
		}
	}

	/**
	 * Compara a string com um determinado objeto
	 * @param $o BaseObject
	 * @return boolean
	 */
	public function equals( BaseObject $o ) {
		return $this->compareTo( new Strings( $o->__toString() ) ) == 0;
	}

	/**
	 * Verifica se a string termina com $string
	 * @param $string Strings
	 * @return boolean
	 */
	public function endsWith( Strings $string ) {
		return $this->substring( $this->length() - $string->length() )->equals( $string );
	}

	/**
	 * Formata uma string
	 * @param $format Strings
	 * @param $args Object
	 */
	public function format( Strings $format , Object $args ) {
		$args = func_get_args();
		$args = array_slice( $args , 1 , sizeof( $args ) - 1 );

		return new Strings( call_user_func_array( 'sprintf' , array_merge( array( $format , $this->storage->data ) , $args ) ) );
	}

	/**
	 * Recupera a codificação padrão das strings
	 * @return string
	 */
	public static function getDefaultEncoding(){
		return self::$encoding;
	}

	/**
	 * Codificação da string
	 * @return string
	 */
	public function getEncoding() {
		return $this->storage->encoding;
	}

	/**
	 * Recupera a posição da primeira ocorrência de $needle
	 * @param $needle string
	 * @param $fromIndex integer
	 * @param $ignoreCase boolean
	 * @return integer Retorna -1 Se o caracter não existir na string
	 */
	public function indexOf( $needle , $fromIndex = 0 , $ignoreCase = false ) {
		if ( $ignoreCase ) {
			$ret = mb_stripos( $this->storage->data , $needle , $fromIndex , $this->getEncoding() );
		} else {
			$ret = mb_strpos( $this->storage->data , $needle , $fromIndex , $this->getEncoding() );
		}

		return is_bool( $ret ) ?  -1 : $ret;
	}

	/**
	 * Verifica se a Strings está vazia
	 * @return boolean
	 * @see Strings::$length
	 */
	public function isEmpty() {
		return $this->length() == 0;
	}

	/**
	 * Recupera a posição da última ocorrência de $needle
	 * @param $needle string
	 * @param $fromIndex integer
	 * @param $ignoreCase boolean
	 * @return integer Retorna -1 Se o caracter não existir na string
	 */
	public function lastIndexOf( $needle , $fromIndex = 0 , $ignoreCase = false ) {
		if ( $ignoreCase ) {
			$ret = mb_strripos( $this->storage->data , $needle , $fromIndex , $this->getEncoding() );
		} else {
			$ret = mb_strrpos( $this->storage->data , $needle , $fromIndex , $this->getEncoding() );
		}

		return is_bool( $ret ) ?  -1 : $ret;
	}

	/**
	 * Recupera o tamanho da Strings
	 * @return integer
	 */
	public function length(){
		return mb_strlen( $this->storage->data , $this->getEncoding() );
	}

	/**
	 * Substitui as ocorrências de $pattern por $replacement
	 * @param $pattern string
	 * @param $replacement string
	 * @return Strings
	 */
	public function replace( $pattern , $replacement ) {
		$ret = mb_ereg_replace( $pattern , $replacement , $this->storage->data );

		if (  !is_bool( $ret ) ) {
			$this->storage->data = $ret;
		}

		return new Strings( $ret );
	}

	/**
	 * Verifica se a string começa com $string
	 * @param $string Strings String que será verificada
	 * @param $fromIndex integer Posição inicial de onde deverá ser verificado
	 * @return boolean
	 */
	public function startsWith( Strings $string , $fromIndex = 0 ) {
		return $this->substring( $fromIndex , $string->length() + $fromIndex )->equals( $string );
	}

	/**
	 * Recupera uma sub-string da string
	 * @param $startIndex integer
	 * @param $lastIndex integer
	 * @return Strings
	 */
	public function substring( $startIndex , $lastIndex = null ) {
		if ( is_null( $lastIndex ) )
			$lastIndex = $this->length;

		$ret = mb_substr( $this->storage->data , $startIndex , $lastIndex - $startIndex , $this->getEncoding() );

		return new Strings( $ret );
	}

	/**
	 * Quebra a string usando $pattern como separador
	 * @param $pattern string
	 * @param $limit integer
	 * @return array
	 */
	public function split( $pattern , $limit = PHP_INT_MAX ) {
		$ret = array();

		foreach ( mb_split( $pattern , $this->storage->data , $limit ) as $string ) {
			$ret[] = new Strings( $string );
		}

		return $ret;
	}

	/**
	 * Retorna uma matriz com os char codes de todos os caracteres da string
	 * @return array
	 */
	public function toCharArray() {
		$ret = unpack( 'C*' , $this->storage->data );

		return array_combine( range( 0 , sizeof( $ret ) - 1 ) , $ret );
	}

	/**
	 * Converte a string para caixa baixa
	 * @return Strings
	 */
	public function toLowerCase() {
		return new Strings( mb_strtolower( $this->storage->data , $this->getEncoding() ) );
	}

	/**
	 * Converte a string para caixa alta
	 * @return Strings
	 */
	public function toUpperCase() {
		return new Strings( mb_strtoupper( $this->storage->data , $this->getEncoding() ) );
	}

	/**
	 * Remove os espaços iniciais da string
	 * @return Strings
	 */
	public function ltrim() {
		$i =  -1;

		while ( $this->storage->data{  ++$i } == ' ' )
			;

		return $this->substring( $i );
	}

	/**
	 * Remove os espaços finais da string
	 * @return Strings
	 */
	public function rtrim() {
		$i = $this->length;

		while ( $this->storage->data{  --$i } == ' ' );

		return $this->substring( 0 , $i + 1 );
	}

	/**
	 * Remove os espaços iniciais e finais da string
	 * @return Strings
	 */
	public function trim() {
		return $this->ltrim()->rtrim();
	}

	/**
	 * Codifica caracteres da string em entidades HTML numéricas
	 * @param $map array
	 */
	public function toNumericEntity( array $map = array( 0x80 , 0xff , 0 , 0xff ) ) {
		return new Strings( mb_encode_numericentity( $this->storage->data , $map , $this->storage->encoding ) );
	}

	/**
	 * Define a codificação padrão para novas strings
	 * @param $encoding string
	 * @throws InvalidArgumentException Se a codificação passada não for válida
	 */
	public static function setDefaultEncoding( $encoding ) {
		if ( self::testEncoding( $encoding ) ) {
			self::$encoding = $encoding;
		} else {
			throw new InvalidArgumentException( 'Codificação inválida.' );
		}
	}

	/**
	 * Verifica se uma codificação é válida
	 * @param $encoding string
	 * @return boolean
	 */
	private static function testEncoding( &$encoding ) {
		$list = mb_list_encodings();

		return in_array( $encoding , $list );
	}
}