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
 * @brief		Interface gráfica de usuário
 * @details		Toda a base dos objetos de interface de usuário concentram-se nesse pacote.
 * @package		rpo.gui
 */
namespace rpo\gui;

use \UnexpectedValueException;
use rpo\base\Strings;
use rpo\gui\composite\Component;

/**
 * Objeto base para todos os objetos de interface de usuário
 * @author	João Batista Neto
 * @class	UIObject
 * @extends	Component
 */
abstract class UIObject extends \rpo\gui\composite\Component {
	/**
	 * Altura do objeto
	 * @var	integer
	 */
	private $height;

	/**
	 * ID do objeto
	 * @var	Strings
	 */
	private $id;

	/**
	 * Nome do objeto
	 * @var	Strings
	 */
	private $name;

	/**
	 * Classe CSS primária do objeto
	 * @var	Strings
	 */
	private $primaryStyle;

	/**
	 * Define se o objeto é apenas leitura
	 * @var	boolean
	 */
	private $readOnly = false;

	/**
	 * Classe CSS do objeto
	 * @var	Strings
	 */
	private $style;

	/**
	 * Título do objeto
	 * @var	Strings
	 */
	private $title;

	/**
	 * Visibilidade do objeto
	 * @var	boolean
	 */
	private $visible = true;

	/**
	 * Largura do objeto
	 * @var	integer
	 */
	private $width;

	/**
	 * Constroi o objeto de interface de usuário
	 */
	public function __construct(){
		parent::__construct();

		$this->id = new Strings( uniqid( 'id' ) );
		$this->name = new Strings();
		$this->style = new Strings();
		$this->title = new Strings();
	}

	/**
	 * Adiciona uma nova classe CSS ao objeto
	 * @param $style Strings Nome da classe CSS
	 */
	public function addStyleName( Strings $style ){
		if ( $this->style->isEmpty() ){
			$this->setStyleName( $style );
		} else {
			$this->style->format( new Strings( ' %s' ) , $style );
		}
	}

	/**
	 * Recupera a altura do objeto
	 * @return integer
	 */
	public function getHeight(){
		return (int) $this->height;
	}

	/**
	 * Recupera o ID do objeto
	 * @return Strings
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Recupera o nome do objeto
	 * @return Strings
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * Recupera a classe CSS primária do objeto
	 * @return Strings
	 */
	public function getPrimaryStyle(){
		return $this->primaryStyle;
	}

	/**
	 * Recupera todas as classes CSS relacionadas ao objeto
	 * @return Strings
	 */
	public function getStyleName(){
		return $this->style;
	}

	/**
	 * Recupera o título do objeto
	 * @return Strings
	 */
	public function getTitle(){
		return $this->title;
	}

	/**
	 * Recupera a largura do objeto
	 * @return integer
	 */
	public function getWidth(){
		return (int) $this->width;
	}

	/**
	 * Verifica se o objeto é somente leitura
	 * @return boolean
	 */
	public function isReadOnly(){
		return (bool) $this->readOnly;
	}

	/**
	 * Verifica se o objeto é visível
	 * @return boolean
	 */
	public function isVisible(){
		return (bool) $this->visible;
	}

	/**
	 * Define a altura do objeto
	 * @param $height integer
	 * @throws UnexpectedValueException
	 */
	public function setHeight( $height ){
		if ( is_integer( $height ) ){
			$this->height = $height;
		} else {
			throw new UnexpectedValueException( 'height precisa ser um inteiro' );;
		}
	}

	/**
	 * Define o ID do objeto
	 * @param $id Strings
	 */
	public function setId( Strings $id ){
		$this->id = $id;
	}

	/**
	 * Define o nome do objeto
	 * @param $name Strings
	 */
	public function setName( Strings $name ){
		$this->name = $name;
	}

	/**
	 * Define se o objeto é apenas leitura ou não
	 * @param $readOnly boolean
	 * @throws UnexpectedValueException Se readonly não for um boolean
	 */
	public function setReadOnly( $readOnly = true ){
		if ( is_bool( $readOnly ) ){
			$this->readOnly = $readOnly;
		} else {
			throw new UnexpectedValueException( 'readonly precisa ser um boolean' );
		}
	}

	/**
	 * Define o nome da classe CSS do objeto
	 * @param $style Strings
	 */
	public function setStyleName( Strings $style ){
		$this->primaryStyle = $style;
		$this->style = $style;
	}

	/**
	 * Define o título do objeto
	 * @param $title Strings
	 */
	public function setTitle( Strings $title ){
		$this->title = $title;
	}

	/**
	 * Define a visibilidade do objeto
	 * @param $visible boolean
	 * @throws UnexpectedValueException Se visible não for um boolean
	 */
	public function setVisible( $visible = true ){
		if ( is_bool( $visible ) ){
			$this->visible = $visible;
		} else {
			throw new UnexpectedValueException( 'visible precisa ser um boolean' );
		}
	}

	/**
	 * Define a largura do objeto
	 * @param $width integer
	 * @throws UnexpectedValueException Se a largura informada não for um inteiro
	 */
	public function setWidth( $width ){
		if ( is_integer( $width ) ){
			$this->width = $width;
		} else {
			throw new UnexpectedValueException( 'width precisa ser um inteiro' );
		}
	}
}