<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/pmieducar/geral.inc.php';

/**
 * clsPmieducarEscola class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsPmieducarAluno
{
  var $cod_aluno;
  var $ref_cod_religiao;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_idpes;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $caminho_foto;
  var $analfabeto;
  var $nm_pai;
  var $nm_mae;
  var $tipo_responsavel;
  var $recursos_prova_inep;
  var $recebe_escolarizacao_em_outro_espaco;
  var $justificativa_falta_documentacao = false;
  var $url_laudo_medico;
  var $url_documento;
  var $codigo_sistema;
  var $veiculo_transporte_escolar = false;
  var $autorizado_um;
  var $parentesco_um;
  var $autorizado_dois;
  var $parentesco_dois;
  var $autorizado_tres;
  var $parentesco_tres;
  var $autorizado_quatro;
  var $parentesco_quatro;
  var $autorizado_cinco;
  var $parentesco_cinco;
  /**
   * Armazena o total de resultados obtidos na última chamada ao método lista().
   * @var int
   */
  var $_total;

  /**
   * Nome do schema.
   * @var string
   */
  var $_schema;

  /**
   * Nome da tabela.
   * @var string
   */
  var $_tabela;

  /**
   * Lista separada por vírgula, com os campos que devem ser selecionados na
   * próxima chamado ao método lista().
   * @var string
   */
  var $_campos_lista;

  /**
   * Lista com todos os campos da tabela separados por vírgula, padrão para
   * seleção no método lista.
   * @var string
   */
  var $_todos_campos;

  /**
   * Valor que define a quantidade de registros a ser retornada pelo método lista().
   * @var int
   */
  var $_limite_quantidade;

  /**
   * Define o valor de offset no retorno dos registros no método lista().
   * @var int
   */
  var $_limite_offset;

  /**
   * Define o campo para ser usado como padrão de ordenação no método lista().
   * @var string
   */
  var $_campo_order_by;

  /**
   * Construtor.
   */
  function __construct(
    $cod_aluno = NULL,
    $ref_cod_aluno_beneficio = NULL,
    $ref_cod_religiao = NULL,
    $ref_usuario_exc = NULL,
    $ref_usuario_cad = NULL,
    $ref_idpes = NULL,
    $data_cadastro = NULL,
    $data_exclusao = NULL,
    $ativo = NULL,
    $caminho_foto = NULL,
    $analfabeto = NULL,
    $nm_pai = NULL,
    $nm_mae = NULL,
    $tipo_responsavel = NULL,
    $aluno_estado_id = NULL,
    $autorizado_um = NULL,
    $parentesco_um = NULL,
    $autorizado_dois = NULL,
    $parentesco_dois = NULL,
    $autorizado_tres = NULL,
    $parentesco_tres = NULL,
    $autorizado_quatro = NULL,
    $parentesco_quatro = NULL,
    $autorizado_cinco = NULL,
    $parentesco_cinco = NULL)
  {
    $db = new clsBanco();
    $this->_schema = 'pmieducar.';
    $this->_tabela = $this->_schema . 'aluno a';

    $this->_campos_lista = $this->_todos_campos = 'a.cod_aluno, a.ref_cod_religiao, a.ref_usuario_exc,
        a.ref_usuario_cad, a.ref_idpes, a.data_cadastro, a.data_exclusao, a.ativo, a.caminho_foto, a.analfabeto, a.nm_pai, a.nm_mae,tipo_responsavel, a.aluno_estado_id, a.recursos_prova_inep, a.recebe_escolarizacao_em_outro_espaco,
        a.justificativa_falta_documentacao, a.url_laudo_medico::text, a.codigo_sistema, a.veiculo_transporte_escolar, a.parentesco_um, a.autorizado_um, a.parentesco_dois, a.autorizado_dois,
        a.parentesco_tres, a.autorizado_tres, a.parentesco_quatro, a.autorizado_quatro, a.parentesco_cinco, a.autorizado_cinco, a.url_documento::text';

    if (is_numeric($ref_usuario_exc)) {
      if (class_exists('clsPmieducarUsuario')) {
        $tmp_obj = new clsPmieducarUsuario($ref_usuario_exc);

        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_usuario_exc = $ref_usuario_exc;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_usuario_exc = $ref_usuario_exc;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_exc}'")) {
          $this->ref_usuario_exc = $ref_usuario_exc;
        }
      }
    }

    if (is_numeric($ref_usuario_cad)) {
      if (class_exists('clsPmieducarUsuario')) {
        $tmp_obj = new clsPmieducarUsuario($ref_usuario_cad);

        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_usuario_cad = $ref_usuario_cad;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_usuario_cad = $ref_usuario_cad;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_cad}'")) {
          $this->ref_usuario_cad = $ref_usuario_cad;
        }
      }
    }

    if (is_numeric($ref_idpes)) {
      if ($db->CampoUnico("SELECT 1 FROM cadastro.fisica WHERE idpes = '{$ref_idpes}'")) {
        $this->ref_idpes = $ref_idpes;
      }
    }

    if (is_numeric($cod_aluno)) {
      $this->cod_aluno = $cod_aluno;
    }

    if (is_numeric($ref_cod_religiao)  || $ref_cod_aluno_beneficio == "NULL") {
      $this->ref_cod_religiao = $ref_cod_religiao;
    }

    if (is_string($data_cadastro)) {
      $this->data_cadastro = $data_cadastro;
    }

    if (is_string($data_exclusao)) {
      $this->data_exclusao = $data_exclusao;
    }

    if (is_numeric($ativo)) {
      $this->ativo = $ativo;
    }

    if (is_string($caminho_foto)) {
      $this->caminho_foto = $caminho_foto;
    }

    if (is_numeric($analfabeto)) {
      $this->analfabeto = $analfabeto;
    }

    if (is_string($caminho_foto)) {
      $this->caminho_foto = $caminho_foto;
    }

    if (is_string($nm_pai)) {
      $this->nm_pai = $nm_pai;
    }

    if (is_string($nm_mae)) {
      $this->nm_mae = $nm_mae;
    }

    if (is_string($tipo_responsavel)) {
      $this->tipo_responsavel = $tipo_responsavel;
    }

    if (is_string($autorizado_um)) {
      $this->autorizado_um = $autorizado_um;
    }

    if (is_string($parentesco_um)) {
      $this->parentesco_um = $parentesco_um;
    }

    if (is_string($autorizado_dois)) {
      $this->autorizado_dois = $autorizado_dois;
    }

    if (is_string($parentesco_dois)) {
      $this->parentesco_dois = $parentesco_dois;
    }

    if (is_string($autorizado_tres)) {
      $this->autorizado_tres = $autorizado_tres;
    }

    if (is_string($parentesco_tres)) {
      $this->parentesco_tres = $parentesco_tres;
    }

    if (is_string($autorizado_quatro)) {
      $this->autorizado_quatro = $autorizado_quatro;
    }

    if (is_string($parentesco_quatro)) {
      $this->parentesco_quatro = $parentesco_quatro;
    }

    if (is_string($autorizado_cinco)) {
      $this->autorizado_cinco = $autorizado_cinco;
    }

    if (is_string($parentesco_cinco)) {
      $this->parentesco_cinco = $parentesco_cinco;
    }

    $this->aluno_estado_id = $aluno_estado_id;
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if (is_numeric($this->ref_idpes)) {
      $db = new clsBanco();

      $campos  = '';
      $valores = '';
      $gruda   = '';


      if (is_numeric($this->ref_cod_religiao)) {
        $campos  .= "{$gruda}ref_cod_religiao";
        $valores .= "{$gruda}'{$this->ref_cod_religiao}'";
        $gruda = ', ';
      }

      if (is_numeric($this->ref_usuario_cad)) {
        $campos  .= "{$gruda}ref_usuario_cad";
        $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
        $gruda = ', ';
      }

      if (is_numeric($this->ref_idpes)) {
        $campos  .= "{$gruda}ref_idpes";
        $valores .= "{$gruda}'{$this->ref_idpes}'";
        $gruda = ', ';
      }

      if (is_numeric($this->analfabeto)) {
        $campos  .= "{$gruda}analfabeto";
        $valores .= "{$gruda}'{$this->analfabeto}'";
        $gruda = ', ';
      }

      $campos  .= "{$gruda}data_cadastro";
      $valores .= "{$gruda}NOW()";
      $gruda = ', ';

      $campos  .= "{$gruda}ativo";
      $valores .= "{$gruda}'1'";
      $gruda = ', ';

      if (is_string($this->caminho_foto)) {
        $campos  .= "{$gruda}caminho_foto";
        $valores .= "{$gruda}'{$this->caminho_foto}'";
        $gruda = ', ';
      }

      if (is_string($this->nm_pai) && $this->nm_pai != "NULL") {
        $campos  .= "{$gruda}nm_pai";
        $valores .= "{$gruda}'{$this->nm_pai}'";
        $gruda = ', ';
      }

      if (is_string($this->nm_mae) && $this->nm_mae != "NULL") {
        $campos  .= "{$gruda}nm_mae";
        $valores .= "{$gruda}'{$this->nm_mae}'";
        $gruda = ', ';
      }

      if (is_string($this->tipo_responsavel ) && sizeof($this->tipo_responsavel) <= 1) {
        $campos  .= "{$gruda}tipo_responsavel";
        $valores .= "{$gruda}'{$this->tipo_responsavel}'";
        $gruda = ', ';
      }

      if ($this->aluno_estado_id) {
        $campos  .= "{$gruda}aluno_estado_id";
        $valores .= "{$gruda}'{$this->aluno_estado_id}'";
        $gruda = ', ';
      }

      if (is_string($this->recursos_prova_inep)) {
        $campos  .= "{$gruda}recursos_prova_inep";
        $valores .= "{$gruda}'{$this->recursos_prova_inep}'";
        $gruda = ', ';
      }

      if (is_numeric($this->recebe_escolarizacao_em_outro_espaco)) {
        $campos  .= "{$gruda}recebe_escolarizacao_em_outro_espaco";
        $valores .= "{$gruda}'{$this->recebe_escolarizacao_em_outro_espaco}'";
        $gruda = ', ';
      }

      if (is_numeric($this->justificativa_falta_documentacao)) {
        $campos  .= "{$gruda}justificativa_falta_documentacao";
        $valores .= "{$gruda}'{$this->justificativa_falta_documentacao}'";
        $gruda = ', ';
      }

      if (is_string($this->url_documento) && $this->url_documento != '') {
        $campos  .= "{$gruda}url_documento";
        $valores .= "{$gruda}'{$this->url_documento}'";
        $gruda = ', ';
      }

      if (is_string($this->url_laudo_medico) && $this->url_laudo_medico != '') {
        $campos  .= "{$gruda}url_laudo_medico";
        $valores .= "{$gruda}'{$this->url_laudo_medico}'";
        $gruda = ', ';
      }

      if (is_string($this->codigo_sistema)) {
        $campos  .= "{$gruda}codigo_sistema";
        $valores .= "{$gruda}'{$this->codigo_sistema}'";
        $gruda = ', ';
      }

      if (is_string($this->veiculo_transporte_escolar)) {
        $campos .= "{$gruda}veiculo_transporte_escolar";
        $valores .= "{$gruda}'{{$this->veiculo_transporte_escolar}}'";
        $gruda = ', ';
      }

      if (is_string($this->autorizado_um) && $this->autorizado_um != "NULL") {
        $campos  .= "{$gruda}autorizado_um";
        $valores .= "{$gruda}'{$this->autorizado_um}'";
        $gruda = ', ';
      }

      if (is_string($this->parentesco_um) && $this->parentesco_um != "NULL") {
        $campos  .= "{$gruda}parentesco_um";
        $valores .= "{$gruda}'{$this->parentesco_um}'";
        $gruda = ', ';
      }

      if (is_string($this->autorizado_dois) && $this->autorizado_dois != "NULL") {
        $campos  .= "{$gruda}autorizado_dois";
        $valores .= "{$gruda}'{$this->autorizado_dois}'";
        $gruda = ', ';
      }

      if (is_string($this->parentesco_dois) && $this->parentesco_dois != "NULL") {
        $campos  .= "{$gruda}parentesco_dois";
        $valores .= "{$gruda}'{$this->parentesco_dois}'";
        $gruda = ', ';
      }

      if (is_string($this->autorizado_tres) && $this->autorizado_tres != "NULL") {
        $campos  .= "{$gruda}autorizado_tres";
        $valores .= "{$gruda}'{$this->autorizado_tres}'";
        $gruda = ', ';
      }

      if (is_string($this->parentesco_tres) && $this->parentesco_tres != "NULL") {
        $campos  .= "{$gruda}parentesco_tres";
        $valores .= "{$gruda}'{$this->parentesco_tres}'";
        $gruda = ', ';
      }

      if (is_string($this->autorizado_quatro) && $this->autorizado_quatro != "NULL") {
        $campos  .= "{$gruda}autorizado_quatro";
        $valores .= "{$gruda}'{$this->autorizado_quatro}'";
        $gruda = ', ';
      }

      if (is_string($this->parentesco_quatro) && $this->parentesco_quatro != "NULL") {
        $campos  .= "{$gruda}parentesco_quatro";
        $valores .= "{$gruda}'{$this->parentesco_quatro}'";
        $gruda = ', ';
      }

      if (is_string($this->autorizado_cinco) && $this->autorizado_cinco != "NULL") {
        $campos  .= "{$gruda}autorizado_cinco";
        $valores .= "{$gruda}'{$this->autorizado_cinco}'";
        $gruda = ', ';
      }

      if (is_string($this->parentesco_cinco) && $this->parentesco_cinco != "NULL") {
        $campos  .= "{$gruda}parentesco_cinco";
        $valores .= "{$gruda}'{$this->parentesco_cinco}'";
        $gruda = ', ';
      }

      $db->Consulta("INSERT INTO pmieducar.aluno ($campos) VALUES ($valores)");
      return $db->InsertId("pmieducar.aluno_cod_aluno_seq");
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    if (is_numeric($this->cod_aluno)) {
      $db  = new clsBanco();
      $set = '';
      $gruda = '';

      if (is_numeric($this->ref_cod_religiao) || $this->ref_cod_religiao == "NULL") {
        $set .= "{$gruda}ref_cod_religiao = {$this->ref_cod_religiao}";
        $gruda = ', ';
      }

      if (is_numeric($this->ref_usuario_exc)) {
        $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
        $gruda = ', ';
      }

      if (is_numeric($this->ref_usuario_cad)) {
        $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
        $gruda = ', ';
      }

      if (is_numeric($this->ref_idpes)) {
        $set .= "{$gruda}ref_idpes = '{$this->ref_idpes}'";
        $gruda = ', ';
      }

      if (is_string($this->data_cadastro)) {
        $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
        $gruda = ', ';
      }

      $set .= "{$gruda}data_exclusao = NOW()";
      $gruda = ', ';

      if (is_numeric($this->ativo)) {
        $set .= "{$gruda}ativo = '{$this->ativo}'";
        $gruda = ', ';
      }

      if (is_string($this->caminho_foto) &&  $this->caminho_foto != "NULL") {
        $set .= "{$gruda}caminho_foto = '{$this->caminho_foto}'";
        $gruda = ', ';
      }
      elseif ($this->caminho_foto == "NULL"){
        $set .= "{$gruda}caminho_foto = {$this->caminho_foto}";
        $gruda = ', ';
      }

      if (is_numeric($this->analfabeto)) {
        $set .= "{$gruda}analfabeto = '{$this->analfabeto}'";
        $gruda = ', ';
      }

      if (is_string($this->nm_pai) && $this->nm_pai != "NULL") {
        $set .= "{$gruda}nm_pai = '{$this->nm_pai}'";
        $gruda = ', ';
      }
      elseif ($this->nm_pai == "NULL") {
        $set .= "{$gruda}nm_pai = NULL";
        $gruda = ', ';
      }

      if (is_string($this->nm_mae) && $this->nm_mae != "NULL") {
        $set .= "{$gruda}nm_mae = '{$this->nm_mae}'";
        $gruda = ', ';
      }
      elseif ($this->nm_mae == "NULL") {
        $set .= "{$gruda}nm_mae = NULL";
        $gruda = ', ';
      }

      if (is_string($this->tipo_responsavel) && sizeof($this->tipo_responsavel) <= 1) {
        $set .= "{$gruda}tipo_responsavel = '{$this->tipo_responsavel}'";
        $gruda = ', ';
      }
      elseif ($this->tipo_responsavel == '') {
        $set .= "{$gruda}tipo_responsavel = NULL";
        $gruda = ', ';
      }

      if ($this->aluno_estado_id) {
        $set .= "{$gruda}aluno_estado_id = '{$this->aluno_estado_id}'";
        $gruda = ', ';
      }
      else {
        $set .= "{$gruda}aluno_estado_id = NULL";
        $gruda = ', ';
      }

      if (is_string($this->recursos_prova_inep)) {
        $set .= "{$gruda}recursos_prova_inep = '{$this->recursos_prova_inep}'";
        $gruda = ', ';
      }

      if (is_numeric($this->recebe_escolarizacao_em_outro_espaco)) {
        $set .= "{$gruda}recebe_escolarizacao_em_outro_espaco = '{$this->recebe_escolarizacao_em_outro_espaco}'";
        $gruda = ', ';
      }

      if (is_numeric($this->justificativa_falta_documentacao)) {
        $set .= "{$gruda}justificativa_falta_documentacao = '{$this->justificativa_falta_documentacao}'";
        $gruda = ', ';
      } elseif ($this->justificativa_falta_documentacao !== false) {
        $set .= "{$gruda}justificativa_falta_documentacao = null";
        $gruda = ', ';
      }

      if (is_string($this->url_documento) && $this->url_documento != '') {
        $set .= "{$gruda}url_documento = '{$this->url_documento}'";
        $gruda = ', ';
      }

      if (is_string($this->url_laudo_medico) && $this->url_laudo_medico != '') {
        $set .= "{$gruda}url_laudo_medico = '{$this->url_laudo_medico}'";
        $gruda = ', ';
      }

      if (is_string($this->codigo_sistema)) {
        $set .= "{$gruda}codigo_sistema = '{$this->codigo_sistema}'";
        $gruda = ', ';
      }

      if (is_string($this->veiculo_transporte_escolar)) {
        $set .= "{$gruda}veiculo_transporte_escolar = '{{$this->veiculo_transporte_escolar}}'";
        $gruda = ', ';
      } elseif ($this->veiculo_transporte_escolar !== false) {
        $set .= "{$gruda}veiculo_transporte_escolar = NULL";
        $gruda = ', ';
      }

      if (is_string($this->autorizado_um) && $this->autorizado_um != "NULL") {
        $set .= "{$gruda}autorizado_um = '{$this->autorizado_um}'";
        $gruda = ', ';
      }
      elseif ($this->autorizado_um == "NULL") {
        $set .= "{$gruda}autorizado_um = NULL";
        $gruda = ', ';
      }

      if (is_string($this->parentesco_um) && $this->parentesco_um != "NULL") {
        $set .= "{$gruda}parentesco_um = '{$this->parentesco_um}'";
        $gruda = ', ';
      }
      elseif ($this->parentesco_um == "NULL") {
        $set .= "{$gruda}parentesco_um = NULL";
        $gruda = ', ';
      }

      if (is_string($this->autorizado_dois) && $this->autorizado_dois != "NULL") {
        $set .= "{$gruda}autorizado_dois = '{$this->autorizado_dois}'";
        $gruda = ', ';
      }
      elseif ($this->autorizado_dois == "NULL") {
        $set .= "{$gruda}autorizado_dois = NULL";
        $gruda = ', ';
      }

      if (is_string($this->parentesco_dois) && $this->parentesco_dois != "NULL") {
        $set .= "{$gruda}parentesco_dois = '{$this->parentesco_dois}'";
        $gruda = ', ';
      }
      elseif ($this->parentesco_dois == "NULL") {
        $set .= "{$gruda}parentesco_dois = NULL";
        $gruda = ', ';
      }

      if (is_string($this->autorizado_tres) && $this->autorizado_tres != "NULL") {
        $set .= "{$gruda}autorizado_tres = '{$this->autorizado_tres}'";
        $gruda = ', ';
      }
      elseif ($this->autorizado_tres == "NULL") {
        $set .= "{$gruda}autorizado_tres = NULL";
        $gruda = ', ';
      }

      if (is_string($this->parentesco_tres) && $this->parentesco_tres != "NULL") {
        $set .= "{$gruda}parentesco_tres = '{$this->parentesco_tres}'";
        $gruda = ', ';
      }
      elseif ($this->parentesco_tres == "NULL") {
        $set .= "{$gruda}parentesco_tres = NULL";
        $gruda = ', ';
      }

      if (is_string($this->autorizado_quatro) && $this->autorizado_quatro != "NULL") {
        $set .= "{$gruda}autorizado_quatro = '{$this->autorizado_quatro}'";
        $gruda = ', ';
      }
      elseif ($this->autorizado_quatro == "NULL") {
        $set .= "{$gruda}autorizado_quatro = NULL";
        $gruda = ', ';
      }

      if (is_string($this->parentesco_quatro) && $this->parentesco_quatro != "NULL") {
        $set .= "{$gruda}parentesco_quatro = '{$this->parentesco_quatro}'";
        $gruda = ', ';
      }
      elseif ($this->parentesco_quatro == "NULL") {
        $set .= "{$gruda}parentesco_quatro = NULL";
        $gruda = ', ';
      }

      if (is_string($this->autorizado_cinco) && $this->autorizado_cinco != "NULL") {
        $set .= "{$gruda}autorizado_cinco = '{$this->autorizado_cinco}'";
        $gruda = ', ';
      }
      elseif ($this->autorizado_cinco == "NULL") {
        $set .= "{$gruda}autorizado_cinco = NULL";
        $gruda = ', ';
      }

      if (is_string($this->parentesco_cinco) && $this->parentesco_cinco != "NULL") {
        $set .= "{$gruda}parentesco_cinco = '{$this->parentesco_cinco}'";
        $gruda = ', ';
      }
      elseif ($this->parentesco_cinco == "NULL") {
        $set .= "{$gruda}parentesco_cinco = NULL";
        $gruda = ', ';
      }

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_aluno = '{$this->cod_aluno}'" );
        return TRUE;
      }
    }

    return FALSE;
  }

    /**
     * Retorna uma lista de registros filtrados de acordo com os parâmetros.
     *
     * @return array
     */
    public function lista(
        $int_cod_aluno = null,
        $int_ref_cod_aluno_beneficio = null,
        $int_ref_cod_religiao = null,
        $int_ref_usuario_exc = null,
        $int_ref_usuario_cad = null,
        $int_ref_idpes = null,
        $date_data_cadastro_ini = null,
        $date_data_cadastro_fim = null,
        $date_data_exclusao_ini = null,
        $date_data_exclusao_fim = null,
        $int_ativo = null,
        $str_caminho_foto = null,
        $str_nome_aluno = null,
        $str_nome_responsavel = null,
        $int_cpf_responsavel = null,
        $int_analfabeto = null,
        $str_nm_pai = null,
        $str_nm_mae = null,
        $int_ref_cod_escola = null,
        $str_tipo_responsavel = null,
        $str_autorizado_um = null,
        $str_parentesco_um = null,
        $str_autorizado_dois = null,
        $str_parentesco_dois = null,
        $str_autorizado_tres = null,
        $str_parentesco_tres = null,
        $str_autorizado_quatro = null,
        $str_parentesco_quatro = null,
        $str_autorizado_cinco = null,
        $str_parentesco_cinco = null
    ) {
        $filtros = '';
        $this->resetCamposLista();

        $this->_campos_lista .= ', pessoa.nome AS nome_aluno, fisica.nome_social';

        $sql = "
            SELECT
                {$this->_campos_lista}
            FROM
                {$this->_tabela}
            INNER JOIN cadastro.pessoa ON pessoa.idpes = a.ref_idpes
            INNER JOIN cadastro.fisica ON fisica.idpes = a.ref_idpes
        ";

        $whereAnd = ' WHERE ';

        if (is_numeric($int_cod_aluno)) {
            $filtros .= "{$whereAnd} a.cod_aluno = '{$int_cod_aluno}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_religiao)) {
            $filtros .= "{$whereAnd} a.ref_cod_religiao = '{$int_ref_cod_religiao}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_usuario_exc)) {
            $filtros .= "{$whereAnd} a.ref_usuario_exc = '{$int_ref_usuario_exc}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_usuario_cad)) {
            $filtros .= "{$whereAnd} a.ref_usuario_cad = '{$int_ref_usuario_cad}'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_idpes)) {
            $filtros .= "{$whereAnd} a.ref_idpes = '{$int_ref_idpes}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_cadastro_ini)) {
            $filtros .= "{$whereAnd} a.data_cadastro >= '{$date_data_cadastro_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_cadastro_fim)) {
            $filtros .= "{$whereAnd} a.data_cadastro <= '{$date_data_cadastro_fim}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_exclusao_ini)) {
            $filtros .= "{$whereAnd} a.data_exclusao >= '{$date_data_exclusao_ini}'";
            $whereAnd = ' AND ';
        }

        if (is_string($date_data_exclusao_fim)) {
            $filtros .= "{$whereAnd} a.data_exclusao <= '{$date_data_exclusao_fim}'";
            $whereAnd = ' AND ';
        }

        if ($int_ativo) {
            $filtros .= "{$whereAnd} a.ativo = '1'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_caminho_foto)) {
            $filtros .= "{$whereAnd} a.caminho_foto LIKE '%{$str_caminho_foto}%'";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_analfabeto)) {
            $filtros .= "{$whereAnd} a.analfabeto = '{$int_analfabeto}'";
            $whereAnd = ' AND ';
        }

        if (is_string($str_nome_aluno)) {
            $filtros .= "{$whereAnd} translate(upper(coalesce(fisica.nome_social, '') || pessoa.nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$str_nome_aluno}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')";
            $whereAnd = ' AND ';
        }

        if (is_string($str_nome_responsavel)  || is_numeric($int_cpf_responsavel)) {
            $and_resp = '';

            if (is_string($str_nome_responsavel)) {
                $and_nome_pai_mae  = "OR translate(upper(aluno.nm_pai),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%$str_nome_responsavel%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') AND (aluno.tipo_responsavel = 'p')";

                $and_nome_pai_mae .= "OR translate(upper(aluno.nm_mae),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%$str_nome_responsavel%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') AND (aluno.tipo_responsavel = 'm')";

                $and_nome_resp     = "
          (translate(upper(pai_mae.nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%$str_nome_responsavel%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')) AND (aluno.tipo_responsavel = 'm') AND pai_mae.idpes = fisica_aluno.idpes_mae
          OR
          (translate(upper(pai_mae.nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%$str_nome_responsavel%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')) AND (aluno.tipo_responsavel = 'm') AND pai_mae.idpes = fisica_aluno.idpes_mae";

                $and_resp = ' AND ';
            }

            if (is_numeric($int_cpf_responsavel)) {
                $and_cpf_pai_mae = "and fisica_resp.cpf LIKE '$int_cpf_responsavel'";
            }

            $filtros .= "
        AND (EXISTS(
          SELECT
            1
          FROM
            cadastro.fisica fisica_resp,
            cadastro.fisica,
            cadastro.pessoa,
            cadastro.pessoa responsavel
          WHERE
            fisica.idpes_responsavel = fisica_resp.idpes
            AND pessoa.idpes = fisica.idpes
            AND responsavel.idpes = fisica.idpes_responsavel
            $and_cpf_pai_mae
            and aluno.ref_idpes = pessoa.idpes
          )
          $and_nome_pai_mae
          OR EXISTS (
            SELECT
              1
            FROM
              cadastro.fisica AS fisica_aluno,
              cadastro.pessoa As pai_mae,
              cadastro.fisica AS fisica_pai_mae
            WHERE
              fisica_aluno.idpes = aluno.ref_idpes
              AND (
                $and_nome_resp
                $and_resp
                (
                  fisica_pai_mae.idpes = fisica_aluno.idpes_pai
                  OR fisica_pai_mae.idpes = fisica_aluno.idpes_mae
                )
              AND fisica_pai_mae.cpf LIKE '$int_cpf_responsavel'
              )
          )
        )";

            $whereAnd = ' AND ';
        }

        if (is_string($str_nm_pai)) {
            $filtros .= "{$whereAnd} translate(upper(nm_pai),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') nm_pai LIKE translate(upper('%{$str_nm_pai}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')";
            $whereAnd = ' AND ';
        }

        if (is_string($str_nm_mae)) {
            $filtros .= "{$whereAnd} translate(upper(nm_mae),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$str_nm_mae}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')";
            $whereAnd = ' AND ';
        }

        if (is_numeric($int_ref_cod_escola)) {
            $filtros .= "{$whereAnd} cod_aluno IN ( SELECT ref_cod_aluno FROM pmieducar.matricula WHERE ref_ref_cod_escola = '{$int_ref_cod_escola}' AND ultima_matricula = 1 )";
            $whereAnd = ' AND ';
        }

        if (is_numeric($str_tipo_responsavel)) {
            $filtros .= "{$whereAnd} tipo_responsavel = '{$str_tipo_responsavel}'";
            $whereAnd = ' AND ';
        }

        $db = new clsBanco();
        $countCampos = count(explode(',', $this->_campos_lista));
        $resultado = [];

        if (!$this->getOrderby()) {
            $this->setOrderby('coalesce(fisica.nome_social, pessoa.nome)');
        }

        $sql .= $filtros . $this->getOrderby() . $this->getLimite();
        $this->_total = $db->CampoUnico("
            SELECT
                COUNT(0)
            FROM
                {$this->_tabela}
            INNER JOIN cadastro.pessoa ON pessoa.idpes = a.ref_idpes
            INNER JOIN cadastro.fisica ON fisica.idpes = a.ref_idpes
            {$filtros}
        ");

        $db->Consulta($sql);

        if ($countCampos > 1) {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $tupla['_total'] = $this->_total;
                $resultado[] = $tupla;
            }
        } else {
            while ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $resultado[] = $tupla[$this->_campos_lista];
            }
        }

        if (count($resultado)) {
            return $resultado;
        }

        return false;
    }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   */
  function lista2($int_cod_aluno = NULL, $int_ref_cod_aluno_beneficio = NULL,
    $int_ref_cod_religiao = NULL, $int_ref_usuario_exc = NULL,
    $int_ref_usuario_cad = NULL, $int_ref_idpes = NULL, $date_data_cadastro_ini = NULL,
    $date_data_cadastro_fim = NULL, $date_data_exclusao_ini = NULL,
    $date_data_exclusao_fim = NULL, $int_ativo = NULL, $str_caminho_foto = NULL,
    $str_nome_aluno = NULL, $str_nome_responsavel = NULL, $int_cpf_responsavel = NULL,
    $int_analfabeto = NULL, $str_nm_pai = NULL, $str_nm_mae = NULL,
    $int_ref_cod_escola = NULL, $str_tipo_responsavel = NULL, $data_nascimento = NULL,
    $str_nm_pai2 = NULL, $str_nm_mae2 = NULL, $str_nm_responsavel2 = NULL, $cod_inep = NULL,
    $aluno_estado_id = NULL, $ano = NULL, $ref_cod_instituicao = NULL, $ref_cod_escola = NULL,
    $ref_cod_curso = NULL, $ref_cod_serie = NULL, $idsetorbai = NULL,
    $autorizado_um = NULL,
    $parentesco_um = NULL,
    $autorizado_dois = NULL,
    $parentesco_dois = NULL,
    $autorizado_tres = NULL,
    $parentesco_tres = NULL,
    $autorizado_quatro = NULL,
    $parentesco_quatro = NULL,
    $autorizado_cinco = NULL,
    $parentesco_cinco = NULL)
  {
    $filtra_baseado_matricula = is_numeric($ano) || is_numeric($ref_cod_instituicao) || is_numeric($ref_cod_escola) || is_numeric($ref_cod_curso) || is_numeric($ref_cod_serie);// || is_numeric($periodo);

    $filtros = '';
    $this->resetCamposLista();

    $this->_campos_lista .= ', pessoa.nome AS nome_aluno, fisica.nome_social, COALESCE(nome_social, nome) AS ordem_aluno';

    if($filtra_baseado_matricula)
      $sql = "SELECT distinct {$this->_campos_lista} FROM {$this->_tabela} INNER JOIN pmieducar.matricula m ON (m.ref_cod_aluno = a.cod_aluno) ";
    else
      $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";

    $sql .= '
             LEFT JOIN cadastro.pessoa ON pessoa.idpes = a.ref_idpes
             LEFT JOIN cadastro.fisica ON fisica.idpes = a.ref_idpes';
    if($idsetorbai)
      $sql .= '
        INNER JOIN cadastro.endereco_pessoa ep ON (a.ref_idpes = ep.idpes)
        INNER JOIN public.bairro b ON (ep.idbai = b.idbai)
        INNER JOIN public.setor_bai sb ON (sb.idsetorbai = b.idsetorbai)
      ';

    $whereAnd = ' WHERE ';

    if(is_numeric($int_cod_aluno)) {
      $filtros .= "{$whereAnd} cod_aluno = {$int_cod_aluno}";
      $whereAnd = ' AND ';
    }

    if(is_numeric($idsetorbai)) {
      $filtros .= "{$whereAnd} sb.idsetorbai = {$idsetorbai}";
      $whereAnd = ' AND ';
    }

    if(is_numeric($int_ref_cod_religiao)) {
      $filtros .= "{$whereAnd} ref_cod_religiao = '{$int_ref_cod_religiao}'";
      $whereAnd = ' AND ';
    }

    if(is_numeric($int_ref_usuario_exc)) {
      $filtros .= "{$whereAnd} ref_usuario_exc = '{$int_ref_usuario_exc}'";
      $whereAnd = ' AND ';
    }

    if(is_string($aluno_estado_id)) {
      $filtros .= "{$whereAnd} a.aluno_estado_id LIKE '%{$aluno_estado_id}%'";
      $whereAnd = ' AND ';
    }

    if(is_numeric($int_ref_usuario_cad)) {
      $filtros .= "{$whereAnd} ref_usuario_cad = '{$int_ref_usuario_cad}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_idpes)) {
      $filtros .= "{$whereAnd} ref_idpes = '{$int_ref_idpes}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_cadastro_ini)) {
      $filtros .= "{$whereAnd} data_cadastro >= '{$date_data_cadastro_ini}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_cadastro_fim)) {
      $filtros .= "{$whereAnd} data_cadastro <= '{$date_data_cadastro_fim}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_exclusao_ini)) {
      $filtros .= "{$whereAnd} data_exclusao >= '{$date_data_exclusao_ini}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_exclusao_fim)) {
      $filtros .= "{$whereAnd} data_exclusao <= '{$date_data_exclusao_fim}'";
      $whereAnd = ' AND ';
    }

    if ($int_ativo) {
      $filtros .= "{$whereAnd} a.ativo = '1'";
      $whereAnd = ' AND ';
    }

    if (is_string($str_caminho_foto)) {
      $filtros .= "{$whereAnd} caminho_foto LIKE '%{$str_caminho_foto}%'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_analfabeto)) {
      $filtros .= "{$whereAnd} analfabeto = '{$int_analfabeto}'";
      $whereAnd = ' AND ';
    }

    if (is_string($str_nome_aluno)) {
      $str_nome_aluno = pg_escape_string($str_nome_aluno);

      $filtros .= "{$whereAnd} EXISTS (
                     SELECT
                       1
                     FROM
                       cadastro.pessoa
                       inner join cadastro.fisica ON (fisica.idpes = pessoa.idpes)
                     WHERE
                       cadastro.pessoa.idpes = ref_idpes
                       AND translate(upper(coalesce(nome_social, '') || nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$str_nome_aluno}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')
                   )";

      $whereAnd = ' AND ';
    }

    if (is_string($str_nome_responsavel) || is_numeric($int_cpf_responsavel)) {
      $and_resp = '';

      if (is_string($str_nome_responsavel)) {
        $and_nome_pai_mae  = "OR translate(upper(aluno.nm_pai),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%$str_nome_responsavel%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') AND (aluno.tipo_responsavel = 'p')";

        $and_nome_pai_mae .= "OR translate(upper(aluno.nm_mae),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%$str_nome_responsavel%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') AND (aluno.tipo_responsavel = 'm')";

        $and_nome_resp     = "
          (translate(upper(pai_mae.nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%$str_nome_responsavel%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')) AND (aluno.tipo_responsavel = 'm') AND pai_mae.idpes = fisica_aluno.idpes_mae
          OR
          (translate(upper(pai_mae.nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%$str_nome_responsavel%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')) AND (aluno.tipo_responsavel = 'p') AND pai_mae.idpes = fisica_aluno.idpes_pai";

        $and_resp = 'AND';
      }

      if (is_numeric($int_cpf_responsavel)) {
        $and_cpf_pai_mae = "and fisica_resp.cpf LIKE '$int_cpf_responsavel'";
      }

      $filtros .= "
        AND (EXISTS(
          SELECT
            1
          FROM
            cadastro.fisica fisica_resp,
            cadastro.fisica,
            cadastro.pessoa,
            cadastro.pessoa responsavel
          WHERE
            fisica.idpes_responsavel = fisica_resp.idpes
            AND pessoa.idpes = fisica.idpes
            AND responsavel.idpes = fisica.idpes_responsavel
            $and_cpf_pai_mae
            and aluno.ref_idpes = pessoa.idpes)
          $and_nome_pai_mae
          OR EXISTS (
            SELECT
              1
            FROM
              cadastro.fisica AS fisica_aluno,
              cadastro.pessoa AS pai_mae,
              cadastro.fisica AS fisica_pai_mae
            WHERE
              fisica_aluno.idpes = aluno.ref_idpes
            AND (
              $and_nome_resp
              $and_resp
              (
                fisica_pai_mae.idpes = fisica_aluno.idpes_pai
                OR fisica_pai_mae.idpes = fisica_aluno.idpes_mae
              )
              AND fisica_pai_mae.cpf LIKE '$int_cpf_responsavel'
            )
          )
        )";

      $whereAnd = ' AND ';
    }

    if (is_string($str_nm_pai)) {
      $filtros .= "{$whereAnd} translate(upper(nm_pai),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') nm_pai LIKE translate(upper('%{$str_nm_pai}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')";
      $whereAnd = ' AND ';
    }

    if (is_string($str_nm_mae)) {
      $filtros .= "{$whereAnd} translate(upper(nm_mae),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE translate(upper('%{$str_nm_mae}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_cod_escola)) {
      $filtros .= "{$whereAnd} cod_aluno IN ( SELECT ref_cod_aluno FROM pmieducar.matricula WHERE ref_ref_cod_escola = '{$int_ref_cod_escola}' AND ultima_matricula = 1)";
      $whereAnd = ' AND ';
    }

    if (is_numeric($str_tipo_responsavel)) {
      $filtros .= "{$whereAnd} tipo_responsavel = '{$str_tipo_responsavel}'";
      $whereAnd = ' AND ';
    }

    if (!empty($data_nascimento)) {
      $filtros .= "{$whereAnd} EXISTS (SELECT 1 FROM cadastro.fisica f WHERE f.idpes = ref_idpes AND TO_CHAR(data_nasc,'DD/MM/YYYY') = '{$data_nascimento}')";
      $whereAnd = ' AND ';
    }

    if (!empty($cod_inep) && is_numeric($cod_inep)) {
      $filtros .= "{$whereAnd} cod_aluno IN( SELECT cod_aluno FROM modules.educacenso_cod_aluno WHERE cod_aluno_inep = {$cod_inep})";
      $whereAnd = ' AND ';
    }

    if($filtra_baseado_matricula){
      $filtros .= "{$whereAnd} m.aprovado = 3 AND m.ativo = 1 ";
      $whereAnd = ' AND ';
    }

    if(is_numeric($ano)){
      $filtros .= "{$whereAnd} m.ano = {$ano}";
      $whereAnd = ' AND ';
    }

    if(is_numeric($ref_cod_escola)){
      $filtros .= "{$whereAnd} m.ref_ref_cod_escola = {$ref_cod_escola}";
      $whereAnd = ' AND ';
    }

    if(is_numeric($ref_cod_serie)){
      $filtros .= "{$whereAnd} m.ref_ref_cod_serie = {$ref_cod_serie}";
      $whereAnd = ' AND ';
    }

    if(is_numeric($ref_cod_curso)){
      $filtros .= "{$whereAnd} m.ref_cod_curso = {$ref_cod_curso}";
      $whereAnd = ' AND ';
    }
    /*
    if(is_numeric($periodo)){
      $filtros .= "{$whereAnd} EXISTS(
                      select 1

                FROM pmieducar.matricula_turma mt,
                     pmieducar.turma
                     WHERE mt.ref_cod_matricula = m.cod_matricula and
                     mt.ativo = 1 and
                     turma.cod_turma = mt.ref_cod_turma
                     AND turma_turno_id = {$periodo}
                     LIMIT 1) ";
      $whereAnd = ' AND ';

    }*/

    if (!empty($str_nm_pai2) || !empty($str_nm_mae2) || !empty($str_nm_responsavel2)) {
      $complemento_letf_outer = '';
      $complemento_where      = '';
      $and_where              = '';

      if (!empty($str_nm_pai2)) {
        $str_nm_pai2 = addslashes($str_nm_pai2);

        $complemento_sql   .= ' LEFT OUTER JOIN cadastro.pessoa AS pessoa_pai ON (pessoa_pai.idpes = f.idpes_pai)';
        $complemento_where .= "{$and_where} (translate(upper(nm_pai),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE (translate(upper('%{$str_nm_pai2}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')) OR translate(upper(pessoa_pai.nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE (translate(upper('%{$str_nm_pai2}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')))";
        $and_where          = ' AND ';
      }

      if (!empty($str_nm_mae2)) {
        $str_nm_mae2 = addslashes($str_nm_mae2);

        $complemento_sql   .= ' LEFT OUTER JOIN cadastro.pessoa AS pessoa_mae ON (pessoa_mae.idpes = f.idpes_mae)';
        $complemento_where .= "{$and_where} (translate(upper(nm_mae),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE (translate(upper('%{$str_nm_mae2}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')) OR translate(upper(pessoa_mae.nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE (translate(upper('%{$str_nm_mae2}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')))";
        $and_where          = ' AND ';
      }

      if (!empty($str_nm_responsavel2)) {
        $str_nm_responsavel2 = addslashes($str_nm_responsavel2);

        $complemento_sql .= " LEFT OUTER JOIN cadastro.pessoa AS pessoa_responsavel ON (pessoa_responsavel.idpes = f.idpes_responsavel)";
        $complemento_where .= "{$and_where} (translate(upper(pessoa_responsavel.nome),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN') LIKE (translate(upper('%{$str_nm_responsavel2}%'),'ÅÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜÇÝÑ','AAAAAAEEEEIIIIOOOOOUUUUCYN')))";
        $and_where = " AND ";
      }

      $filtros .= "
        {$whereAnd} EXISTS
          (SELECT 1 FROM cadastro.fisica f
             {$complemento_sql}
           WHERE
              f.idpes = ref_idpes
              AND ({$complemento_where}))";

      $whereAnd = ' AND ';
    }

    $db = new clsBanco();
    $countCampos = count(explode(',', $this->_campos_lista));
    $resultado = array();

    if (!$this->getOrderby()) {
      $this->setOrderby('ordem_aluno');
    }

    $sql .= $filtros . $this->getOrderby() . $this->getLimite();

    if($filtra_baseado_matricula)
      $sqlCount = "SELECT COUNT(DISTINCT cod_aluno) FROM {$this->_tabela} INNER JOIN pmieducar.matricula m ON (m.ref_cod_aluno = a.cod_aluno) ";
    else
      $sqlCount =  "SELECT COUNT(0) FROM {$this->_tabela} ";

    if($idsetorbai)
      $sqlCount .= '
        INNER JOIN cadastro.endereco_pessoa ep ON (a.ref_idpes = ep.idpes)
        INNER JOIN public.bairro b ON (ep.idbai = b.idbai)
        INNER JOIN public.setor_bai sb ON (sb.idsetorbai = b.idsetorbai)
      ';

    $sqlCount .= $filtros;

    $this->_total = $db->CampoUnico($sqlCount);

    $db->Consulta($sql);

    if ($countCampos > 1) {
      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();
        $tupla["_total"] = $this->_total;
        $resultado[] = $tupla;
      }
    }
    else {
      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();
        $resultado[] = $tupla[$this->_campos_lista];
      }
    }
    if (count($resultado)) {
      return $resultado;
    }
    return false;
  }

  /**
   * Retorna um array com os dados de um registro
   * @return array
   */
  function detalhe()
  {
    if (is_numeric($this->cod_aluno)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_aluno = '{$this->cod_aluno}'");
      $db->ProximoRegistro();
      return $db->Tupla();
    }
    elseif (is_numeric($this->ref_idpes)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_idpes = '{$this->ref_idpes}'");
      $db->ProximoRegistro();
      return $db->Tupla();
    }
    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro.
   * @return array
   */
  function existe()
  {
    if (is_numeric($this->cod_aluno)) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_aluno = '{$this->cod_aluno}'");
      $db->ProximoRegistro();
      return $db->Tupla();
    }
    elseif (is_numeric($this->ref_idpes)) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_idpes = '{$this->ref_idpes}'");
      $db->ProximoRegistro();
      return $db->Tupla();
    }

    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro.
   * @return array
   */
  function existePessoa()
  {
    if (is_numeric($this->ref_idpes)) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_idpes = '{$this->ref_idpes}'");
      $db->ProximoRegistro();
      return $db->Tupla();
    }

    return FALSE;
  }

  function verificaInep($cod_aluno = NULL)
  {
    if (is_numeric($cod_aluno)) {
      $db = new clsBanco();
      $sql = "SELECT cod_aluno_inep
                FROM {$this->_tabela}
               INNER JOIN modules.educacenso_cod_aluno eca ON (eca.cod_aluno = a.cod_aluno)
               WHERE a.cod_aluno = $cod_aluno";
      $db->Consulta($sql);
      $db->ProximoRegistro();
      return $db->Tupla();
    }
  }

  function getResponsavelAluno()
  {
    if ($this->cod_aluno) {
      $registro = $this->detalhe();

      $registro['nome_responsavel'] = null;

      if ($registro['tipo_responsavel'] == 'p'  ||
         (!$registro['nome_responsavel'] && $registro['tipo_responsavel'] == NULL)) {
        $obj_fisica= new clsFisica($registro['ref_idpes']);
        $det_fisica_aluno = $obj_fisica->detalhe();

        if ($det_fisica_aluno['idpes_pai']) {
          $obj_ref_idpes = new clsPessoa_($det_fisica_aluno['idpes_pai']);
          $det_ref_idpes = $obj_ref_idpes->detalhe();

          $obj_fisica= new clsFisica($det_fisica_aluno['idpes_pai']);
          $det_fisica = $obj_fisica->detalhe();

          $registro['nome_responsavel'] = $det_ref_idpes['nome'];

          if ($det_fisica['cpf']) {
            $registro['cpf_responsavel'] = int2CPF($det_fisica['cpf']);
          }
        }
      }

      if ($registro['tipo_responsavel'] == 'm' ||
         ($registro['nome_responsavel'] == null && $registro['tipo_responsavel'] == NULL)) {
        if (!$det_fisica_aluno) {
          $obj_fisica= new clsFisica($registro['ref_idpes']);
          $det_fisica_aluno = $obj_fisica->detalhe();
        }

        if ($det_fisica_aluno['idpes_mae'] ) {
          $obj_ref_idpes = new clsPessoa_( $det_fisica_aluno['idpes_mae'] );
          $det_ref_idpes = $obj_ref_idpes->detalhe();

          $obj_fisica= new clsFisica($det_fisica_aluno['idpes_mae']);
          $det_fisica = $obj_fisica->detalhe();

          $registro['nome_responsavel'] = $det_ref_idpes['nome'];

          if ($det_fisica['cpf']) {
            $registro['cpf_responsavel'] = int2CPF($det_fisica['cpf']);
          }
        }
      }

      if ($registro['tipo_responsavel'] == 'r' ||
         ($registro['nome_responsavel'] == null && $registro['tipo_responsavel'] == NULL)) {
        if (!$det_fisica_aluno) {
          $obj_fisica= new clsFisica($registro['ref_idpes']);
          $det_fisica_aluno = $obj_fisica->detalhe();
        }

        if ($det_fisica_aluno['idpes_responsavel']) {
          $obj_ref_idpes = new clsPessoa_($det_fisica_aluno['idpes_responsavel']);
          $obj_fisica = new clsFisica($det_fisica_aluno['idpes_responsavel']);

          $det_ref_idpes = $obj_ref_idpes->detalhe();
          $det_fisica = $obj_fisica->detalhe();

          $registro['nome_responsavel'] = $det_ref_idpes['nome'];

          if ($det_fisica['cpf']) {
            $registro['cpf_responsavel'] = int2CPF($det_fisica['cpf']);
          }
        }
      }

      if ($registro['tipo_responsavel'] == 'a') {
        if (!$det_fisica_aluno) {
          $obj_fisica= new clsFisica($registro['ref_idpes']);
          $det_fisica_aluno = $obj_fisica->detalhe();
        }

        if ($det_fisica_aluno['idpes_mae'] && $det_fisica_aluno['idpes_pai']) {

          $obj_mae = new clsPessoa_($det_fisica_aluno['idpes_mae']);
          $det_mae = $obj_mae->detalhe();

          $obj_pai = new clsPessoa_($det_fisica_aluno['idpes_pai']);
          $det_pai = $obj_pai->detalhe();

          $registro['nome_responsavel'] = $det_pai['nome'] . ", " . $det_mae['nome'];

          if ($det_fisica['cpf']) {
            $registro['cpf_responsavel'] = int2CPF($det_fisica['cpf']);
          }
        }
      }

      if (!$registro['nome_responsavel']) {
        if ($registro['tipo_responsavel'] != NULL) {
          if ($registro['tipo_responsavel'] == 'p') {
            $registro['nome_responsavel'] = $registro['nm_pai'];
          }
          else {
            $registro['nome_responsavel'] = $registro['nm_mae'];
          }
        }
        else {
          if ($registro['nm_pai']) {
            $registro['nome_responsavel'] = $registro['nm_pai'];
          }
          else {
            $registro['nome_responsavel'] = $registro['nm_mae'];
          }
        }
      }

      return $registro;
    }

    return FALSE;
  }

  /**
   * Exclui um registro.
   * @return bool
   */
  function excluir()
  {
    if (is_numeric($this->cod_aluno) && is_numeric($this->ref_usuario_exc)) {
      $this->ativo = 0;
      return $this->edita();
    }
    return FALSE;
  }

  /**
   * Define quais campos da tabela serão selecionados no método Lista().
   */
  function setCamposLista($str_campos)
  {
    $this->_campos_lista = $str_campos;
  }

  /**
   * Define que o método Lista() deverpa retornar todos os campos da tabela.
   */
  function resetCamposLista()
  {
    $this->_campos_lista = $this->_todos_campos;
  }

  /**
   * Define limites de retorno para o método Lista().
   */
  function setLimite($intLimiteQtd, $intLimiteOffset = NULL)
  {
    $this->_limite_quantidade = $intLimiteQtd;
    $this->_limite_offset = $intLimiteOffset;
  }

  /**
   * Retorna a string com o trecho da query responsável pelo limite de
   * registros retornados/afetados.
   *
   * @return string
   */
  function getLimite()
  {
    if (is_numeric($this->_limite_quantidade)) {
      $retorno = " LIMIT {$this->_limite_quantidade}";
      if (is_numeric($this->_limite_offset)) {
        $retorno .= " OFFSET {$this->_limite_offset} ";
      }
      return $retorno;
    }
    return '';
  }

  /**
   * Define o campo para ser utilizado como ordenação no método Lista().
   */
  function setOrderby($strNomeCampo)
  {
    if (is_string($strNomeCampo) && $strNomeCampo ) {
      $this->_campo_order_by = $strNomeCampo;
    }
  }

  /**
   * Retorna a string com o trecho da query responsável pela Ordenação dos
   * registros.
   *
   * @return string
   */
  function getOrderby()
  {
    if (is_string($this->_campo_order_by)) {
      return " ORDER BY {$this->_campo_order_by} ";
    }
    return '';
  }
}
