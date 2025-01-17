<?php

class grid_relatorio_emprestimo_rtf
{
   var $Db;
   var $Erro;
   var $Ini;
   var $Lookup;
   var $nm_data;
   var $Texto_tag;
   var $Arquivo;
   var $Tit_doc;
   var $sc_proc_grid; 
   var $NM_cmp_hidden = array();

   //---- 
   function grid_relatorio_emprestimo_rtf()
   {
      $this->nm_data   = new nm_data("pt_br");
      $this->Texto_tag = "";
   }

   //---- 
   function monta_rtf()
   {
      $this->inicializa_vars();
      $this->gera_texto_tag();
      $this->grava_arquivo_rtf();
      $this->monta_html();
   }

   //----- 
   function inicializa_vars()
   {
      global $nm_lang;
      $dir_raiz          = strrpos($_SERVER['PHP_SELF'],"/") ;  
      $dir_raiz          = substr($_SERVER['PHP_SELF'], 0, $dir_raiz + 1) ;  
      $this->nm_location = $this->Ini->sc_protocolo . $this->Ini->server . $dir_raiz; 
      $this->Arquivo    = "sc_rtf";
      $this->Arquivo   .= "_" . date("YmdHis") . "_" . rand(0, 1000);
      $this->Arquivo   .= "_grid_relatorio_emprestimo";
      $this->Arquivo   .= ".rtf";
      $this->Tit_doc    = "grid_relatorio_emprestimo.rtf";
   }

   //----- 
   function gera_texto_tag()
   {
     global $nm_lang;
      global
             $nm_nada, $nm_lang;

      $_SESSION['scriptcase']['sc_sql_ult_conexao'] = ''; 
      $this->sc_proc_grid = false; 
      $nm_raiz_img  = ""; 
      if (isset($_SESSION['scriptcase']['sc_apl_conf']['grid_relatorio_emprestimo']['field_display']) && !empty($_SESSION['scriptcase']['sc_apl_conf']['grid_relatorio_emprestimo']['field_display']))
      {
          foreach ($_SESSION['scriptcase']['sc_apl_conf']['grid_relatorio_emprestimo']['field_display'] as $NM_cada_field => $NM_cada_opc)
          {
              $this->NM_cmp_hidden[$NM_cada_field] = $NM_cada_opc;
          }
      }
      if (isset($_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['usr_cmp_sel']) && !empty($_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['usr_cmp_sel']))
      {
          foreach ($_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['usr_cmp_sel'] as $NM_cada_field => $NM_cada_opc)
          {
              $this->NM_cmp_hidden[$NM_cada_field] = $NM_cada_opc;
          }
      }
      if (isset($_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['php_cmp_sel']) && !empty($_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['php_cmp_sel']))
      {
          foreach ($_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['php_cmp_sel'] as $NM_cada_field => $NM_cada_opc)
          {
              $this->NM_cmp_hidden[$NM_cada_field] = $NM_cada_opc;
          }
      }
      if (isset($_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['campos_busca']) && !empty($_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['campos_busca']))
      { 
          $Busca_temp = $_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['campos_busca'];
          if ($_SESSION['scriptcase']['charset'] != "UTF-8")
          {
              $Busca_temp = NM_conv_charset($Busca_temp, $_SESSION['scriptcase']['charset'], "UTF-8");
          }
          $this->e_data_entrega = $Busca_temp['e_data_entrega']; 
          $tmp_pos = strpos($this->e_data_entrega, "##@@");
          if ($tmp_pos !== false)
          {
              $this->e_data_entrega = substr($this->e_data_entrega, 0, $tmp_pos);
          }
          $this->e_data_entrega_2 = $Busca_temp['e_data_entrega_input_2']; 
          $this->e_data_devolucao = $Busca_temp['e_data_devolucao']; 
          $tmp_pos = strpos($this->e_data_devolucao, "##@@");
          if ($tmp_pos !== false)
          {
              $this->e_data_devolucao = substr($this->e_data_devolucao, 0, $tmp_pos);
          }
          $this->e_data_devolucao_2 = $Busca_temp['e_data_devolucao_input_2']; 
          $this->e_status = $Busca_temp['e_status']; 
          $tmp_pos = strpos($this->e_status, "##@@");
          if ($tmp_pos !== false)
          {
              $this->e_status = substr($this->e_status, 0, $tmp_pos);
          }
      } 
      $this->nm_field_dinamico = array();
      $this->nm_order_dinamico = array();
      $this->sc_where_orig   = $_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['where_orig'];
      $this->sc_where_atual  = $_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['where_pesq'];
      $this->sc_where_filtro = $_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['where_pesq_filtro'];
      if (isset($_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['rtf_name']))
      {
          $this->Arquivo = $_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['rtf_name'];
          $this->Tit_doc = $_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['rtf_name'];
          unset($_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['rtf_name']);
      }
      if (in_array(strtolower($this->Ini->nm_tpbanco), $this->Ini->nm_bases_sybase))
      { 
          $nmgp_select = "SELECT str_replace (convert(char(10),e.data_entrega,102), '.', '-') + ' ' + convert(char(8),e.data_entrega,20) as e_data_entrega, str_replace (convert(char(10),e.data_devolucao,102), '.', '-') + ' ' + convert(char(8),e.data_devolucao,20) as e_data_devolucao, e.status as e_status, CASE
       WHEN es.materia = 'paradidatico' THEN es.titulo
       ELSE es.materia
   END as sc_field_1, concat(a.nome_aluno,' ',a.matricula) as sc_field_0 from " . $this->Ini->nm_tabela; 
      } 
      elseif (in_array(strtolower($this->Ini->nm_tpbanco), $this->Ini->nm_bases_mysql))
      { 
          $nmgp_select = "SELECT e.data_entrega as e_data_entrega, e.data_devolucao as e_data_devolucao, e.status as e_status, CASE
       WHEN es.materia = 'paradidatico' THEN es.titulo
       ELSE es.materia
   END as sc_field_1, concat(a.nome_aluno,' ',a.matricula) as sc_field_0 from " . $this->Ini->nm_tabela; 
      } 
      elseif (in_array(strtolower($this->Ini->nm_tpbanco), $this->Ini->nm_bases_mssql))
      { 
       $nmgp_select = "SELECT convert(char(23),e.data_entrega,121) as e_data_entrega, convert(char(23),e.data_devolucao,121) as e_data_devolucao, e.status as e_status, CASE
       WHEN es.materia = 'paradidatico' THEN es.titulo
       ELSE es.materia
   END as sc_field_1, concat(a.nome_aluno,' ',a.matricula) as sc_field_0 from " . $this->Ini->nm_tabela; 
      } 
      elseif (in_array(strtolower($this->Ini->nm_tpbanco), $this->Ini->nm_bases_oracle))
      { 
          $nmgp_select = "SELECT e.data_entrega as e_data_entrega, e.data_devolucao as e_data_devolucao, e.status as e_status, CASE
       WHEN es.materia = 'paradidatico' THEN es.titulo
       ELSE es.materia
   END as sc_field_1, concat(a.nome_aluno,' ',a.matricula) as sc_field_0 from " . $this->Ini->nm_tabela; 
      } 
      elseif (in_array(strtolower($this->Ini->nm_tpbanco), $this->Ini->nm_bases_informix))
      { 
          $nmgp_select = "SELECT EXTEND(e.data_entrega, YEAR TO DAY) as e_data_entrega, EXTEND(e.data_devolucao, YEAR TO DAY) as e_data_devolucao, e.status as e_status, CASE
       WHEN es.materia = 'paradidatico' THEN es.titulo
       ELSE es.materia
   END as sc_field_1, concat(a.nome_aluno,' ',a.matricula) as sc_field_0 from " . $this->Ini->nm_tabela; 
      } 
      else 
      { 
          $nmgp_select = "SELECT e.data_entrega as e_data_entrega, e.data_devolucao as e_data_devolucao, e.status as e_status, CASE
       WHEN es.materia = 'paradidatico' THEN es.titulo
       ELSE es.materia
   END as sc_field_1, concat(a.nome_aluno,' ',a.matricula) as sc_field_0 from " . $this->Ini->nm_tabela; 
      } 
      $nmgp_select .= " " . $_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['where_pesq'];
      if (isset($_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['where_resumo']) && !empty($_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['where_resumo'])) 
      { 
          if (empty($_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['where_pesq'])) 
          { 
              $nmgp_select .= " where " . $_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['where_resumo']; 
          } 
          else
          { 
              $nmgp_select .= " and (" . $_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['where_resumo'] . ")"; 
          } 
      } 
      $nmgp_order_by = $_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['order_grid'];
      $nmgp_select .= $nmgp_order_by; 
      $_SESSION['scriptcase']['sc_sql_ult_comando'] = $nmgp_select;
      $rs = $this->Db->Execute($nmgp_select);
      if ($rs === false && !$rs->EOF && $GLOBALS["NM_ERRO_IBASE"] != 1)
      {
         $this->Erro->mensagem(__FILE__, __LINE__, "banco", $this->Ini->Nm_lang['lang_errm_dber'], $this->Db->ErrorMsg());
         exit;
      }

      $this->Texto_tag .= "<table>\r\n";
      $this->Texto_tag .= "<tr>\r\n";
      foreach ($_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['field_order'] as $Cada_col)
      { 
          $SC_Label = (isset($this->New_label['e_data_entrega'])) ? $this->New_label['e_data_entrega'] : "Data Entrega"; 
          if ($Cada_col == "e_data_entrega" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              if (!NM_is_utf8($SC_Label))
              {
                  $SC_Label = sc_convert_encoding($SC_Label, "UTF-8", $_SESSION['scriptcase']['charset']);
              }
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['e_data_devolucao'])) ? $this->New_label['e_data_devolucao'] : "Data Devolucao"; 
          if ($Cada_col == "e_data_devolucao" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              if (!NM_is_utf8($SC_Label))
              {
                  $SC_Label = sc_convert_encoding($SC_Label, "UTF-8", $_SESSION['scriptcase']['charset']);
              }
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['e_status'])) ? $this->New_label['e_status'] : "Status"; 
          if ($Cada_col == "e_status" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              if (!NM_is_utf8($SC_Label))
              {
                  $SC_Label = sc_convert_encoding($SC_Label, "UTF-8", $_SESSION['scriptcase']['charset']);
              }
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['sc_field_1'])) ? $this->New_label['sc_field_1'] : "Mat�ria/T�tulo"; 
          if ($Cada_col == "sc_field_1" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              if (!NM_is_utf8($SC_Label))
              {
                  $SC_Label = sc_convert_encoding($SC_Label, "UTF-8", $_SESSION['scriptcase']['charset']);
              }
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
      } 
      $this->Texto_tag .= "</tr>\r\n";
      while (!$rs->EOF)
      {
         $this->Texto_tag .= "<tr>\r\n";
         $this->e_data_entrega = $rs->fields[0] ;  
         $this->e_data_devolucao = $rs->fields[1] ;  
         $this->e_status = $rs->fields[2] ;  
         $this->sc_field_1 = $rs->fields[3] ;  
         $this->sc_field_0 = $rs->fields[4] ;  
         $this->sc_proc_grid = true; 
         foreach ($_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['field_order'] as $Cada_col)
         { 
            if (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off")
            { 
                $NM_func_exp = "NM_export_" . $Cada_col;
                $this->$NM_func_exp();
            } 
         } 
         $this->Texto_tag .= "</tr>\r\n";
         $rs->MoveNext();
      }
      $this->Texto_tag .= "</table>\r\n";

      $rs->Close();
   }
   //----- e_data_entrega
   function NM_export_e_data_entrega()
   {
         $conteudo_x = $this->e_data_entrega;
         nm_conv_limpa_dado($conteudo_x, "YYYY-MM-DD");
         if (is_numeric($conteudo_x) && $conteudo_x > 0) 
         { 
             $this->nm_data->SetaData($this->e_data_entrega, "YYYY-MM-DD");
             $this->e_data_entrega = $this->nm_data->FormataSaida($this->nm_data->FormatRegion("DT", "ddmmaaaa"));
         } 
         if (!NM_is_utf8($this->e_data_entrega))
         {
             $this->e_data_entrega = sc_convert_encoding($this->e_data_entrega, "UTF-8", $_SESSION['scriptcase']['charset']);
         }
         $this->e_data_entrega = str_replace('<', '&lt;', $this->e_data_entrega);
         $this->e_data_entrega = str_replace('>', '&gt;', $this->e_data_entrega);
         $this->Texto_tag .= "<td>" . $this->e_data_entrega . "</td>\r\n";
   }
   //----- e_data_devolucao
   function NM_export_e_data_devolucao()
   {
         $conteudo_x = $this->e_data_devolucao;
         nm_conv_limpa_dado($conteudo_x, "YYYY-MM-DD");
         if (is_numeric($conteudo_x) && $conteudo_x > 0) 
         { 
             $this->nm_data->SetaData($this->e_data_devolucao, "YYYY-MM-DD");
             $this->e_data_devolucao = $this->nm_data->FormataSaida($this->nm_data->FormatRegion("DT", "ddmmaaaa"));
         } 
         if (!NM_is_utf8($this->e_data_devolucao))
         {
             $this->e_data_devolucao = sc_convert_encoding($this->e_data_devolucao, "UTF-8", $_SESSION['scriptcase']['charset']);
         }
         $this->e_data_devolucao = str_replace('<', '&lt;', $this->e_data_devolucao);
         $this->e_data_devolucao = str_replace('>', '&gt;', $this->e_data_devolucao);
         $this->Texto_tag .= "<td>" . $this->e_data_devolucao . "</td>\r\n";
   }
   //----- e_status
   function NM_export_e_status()
   {
         $this->e_status = html_entity_decode($this->e_status, ENT_COMPAT, $_SESSION['scriptcase']['charset']);
         $this->e_status = strip_tags($this->e_status);
         if (!NM_is_utf8($this->e_status))
         {
             $this->e_status = sc_convert_encoding($this->e_status, "UTF-8", $_SESSION['scriptcase']['charset']);
         }
         $this->e_status = str_replace('<', '&lt;', $this->e_status);
         $this->e_status = str_replace('>', '&gt;', $this->e_status);
         $this->Texto_tag .= "<td>" . $this->e_status . "</td>\r\n";
   }
   //----- sc_field_1
   function NM_export_sc_field_1()
   {
         $this->sc_field_1 = html_entity_decode($this->sc_field_1, ENT_COMPAT, $_SESSION['scriptcase']['charset']);
         $this->sc_field_1 = strip_tags($this->sc_field_1);
         if (!NM_is_utf8($this->sc_field_1))
         {
             $this->sc_field_1 = sc_convert_encoding($this->sc_field_1, "UTF-8", $_SESSION['scriptcase']['charset']);
         }
         $this->sc_field_1 = str_replace('<', '&lt;', $this->sc_field_1);
         $this->sc_field_1 = str_replace('>', '&gt;', $this->sc_field_1);
         $this->Texto_tag .= "<td>" . $this->sc_field_1 . "</td>\r\n";
   }

   //----- 
   function grava_arquivo_rtf()
   {
      global $nm_lang, $doc_wrap;
      $rtf_f = fopen($this->Ini->root . $this->Ini->path_imag_temp . "/" . $this->Arquivo, "w");
      require_once($this->Ini->path_third      . "/rtf_new/document_generator/cl_xml2driver.php"); 
      $text_ok  =  "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n"; 
      $text_ok .=  "<DOC config_file=\"" . $this->Ini->path_third . "/rtf_new/doc_config.inc\" >\r\n"; 
      $text_ok .=  $this->Texto_tag; 
      $text_ok .=  "</DOC>\r\n"; 
      $xml = new nDOCGEN($text_ok,"RTF"); 
      fwrite($rtf_f, $xml->get_result_file());
      fclose($rtf_f);
   }

   function nm_conv_data_db($dt_in, $form_in, $form_out)
   {
       $dt_out = $dt_in;
       if (strtoupper($form_in) == "DB_FORMAT")
       {
           if ($dt_out == "null" || $dt_out == "")
           {
               $dt_out = "";
               return $dt_out;
           }
           $form_in = "AAAA-MM-DD";
       }
       if (strtoupper($form_out) == "DB_FORMAT")
       {
           if (empty($dt_out))
           {
               $dt_out = "null";
               return $dt_out;
           }
           $form_out = "AAAA-MM-DD";
       }
       nm_conv_form_data($dt_out, $form_in, $form_out);
       return $dt_out;
   }
   //---- 
   function monta_html()
   {
      global $nm_url_saida, $nm_lang;
      include($this->Ini->path_btn . $this->Ini->Str_btn_grid);
      unset($_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['rtf_file']);
      if (is_file($this->Ini->root . $this->Ini->path_imag_temp . "/" . $this->Arquivo))
      {
          $_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo']['rtf_file'] = $this->Ini->root . $this->Ini->path_imag_temp . "/" . $this->Arquivo;
      }
      $path_doc_md5 = md5($this->Ini->path_imag_temp . "/" . $this->Arquivo);
      $_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo'][$path_doc_md5][0] = $this->Ini->path_imag_temp . "/" . $this->Arquivo;
      $_SESSION['sc_session'][$this->Ini->sc_page]['grid_relatorio_emprestimo'][$path_doc_md5][1] = $this->Tit_doc;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
            "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<HTML<?php echo $_SESSION['scriptcase']['reg_conf']['html_dir'] ?>>
<HEAD>
 <TITLE><?php echo $this->Ini->Nm_lang['lang_othr_grid_titl'] ?> -  :: RTF</TITLE>
 <META http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['scriptcase']['charset_html'] ?>" />
<?php
if ($_SESSION['scriptcase']['proc_mobile'])
{
?>
  <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
<?php
}
?>
  <META http-equiv="Expires" content="Fri, Jan 01 1900 00:00:00 GMT"/>
  <META http-equiv="Last-Modified" content="<?php echo gmdate("D, d M Y H:i:s"); ?> GMT"/>
  <META http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate"/>
  <META http-equiv="Cache-Control" content="post-check=0, pre-check=0"/>
  <META http-equiv="Pragma" content="no-cache"/>
  <link rel="stylesheet" type="text/css" href="../_lib/css/<?php echo $this->Ini->str_schema_all ?>_export.css" /> 
  <link rel="stylesheet" type="text/css" href="../_lib/css/<?php echo $this->Ini->str_schema_all ?>_export<?php echo $_SESSION['scriptcase']['reg_conf']['css_dir'] ?>.css" /> 
  <link rel="stylesheet" type="text/css" href="../_lib/buttons/<?php echo $this->Ini->Str_btn_css ?>" /> 
</HEAD>
<BODY class="scExportPage">
<?php echo $this->Ini->Ajax_result_set ?>
<table style="border-collapse: collapse; border-width: 0; height: 100%; width: 100%"><tr><td style="padding: 0; text-align: center; vertical-align: middle">
 <table class="scExportTable" align="center">
  <tr>
   <td class="scExportTitle" style="height: 25px">RTF</td>
  </tr>
  <tr>
   <td class="scExportLine" style="width: 100%">
    <table style="border-collapse: collapse; border-width: 0; width: 100%"><tr><td class="scExportLineFont" style="padding: 3px 0 0 0" id="idMessage">
    <?php echo $this->Ini->Nm_lang['lang_othr_file_msge'] ?>
    </td><td class="scExportLineFont" style="text-align:right; padding: 3px 0 0 0">
     <?php echo nmButtonOutput($this->arr_buttons, "bexportview", "document.Fview.submit()", "document.Fview.submit()", "idBtnView", "", "", "", "", "", "", $this->Ini->path_botoes, "", "", "", "", "", "only_text", "text_right", "", "", "", "", "", "");
 ?>
     <?php echo nmButtonOutput($this->arr_buttons, "bdownload", "document.Fdown.submit()", "document.Fdown.submit()", "idBtnDown", "", "", "", "", "", "", $this->Ini->path_botoes, "", "", "", "", "", "only_text", "text_right", "", "", "", "", "", "");
 ?>
     <?php echo nmButtonOutput($this->arr_buttons, "bvoltar", "document.F0.submit()", "document.F0.submit()", "idBtnBack", "", "", "", "", "", "", $this->Ini->path_botoes, "", "", "", "", "", "only_text", "text_right", "", "", "", "", "", "");
 ?>
    </td></tr></table>
   </td>
  </tr>
 </table>
</td></tr></table>
<form name="Fview" method="get" action="<?php echo $this->Ini->path_imag_temp . "/" . $this->Arquivo ?>" target="_blank" style="display: none"> 
</form>
<form name="Fdown" method="get" action="grid_relatorio_emprestimo_download.php" target="_blank" style="display: none"> 
<input type="hidden" name="script_case_init" value="<?php echo NM_encode_input($this->Ini->sc_page); ?>"> 
<input type="hidden" name="nm_tit_doc" value="grid_relatorio_emprestimo"> 
<input type="hidden" name="nm_name_doc" value="<?php echo $path_doc_md5 ?>"> 
</form>
<FORM name="F0" method=post action="./"> 
<INPUT type="hidden" name="script_case_init" value="<?php echo NM_encode_input($this->Ini->sc_page); ?>"> 
<INPUT type="hidden" name="script_case_session" value="<?php echo NM_encode_input(session_id()); ?>"> 
<INPUT type="hidden" name="nmgp_opcao" value="volta_grid"> 
</FORM> 
</BODY>
</HTML>
<?php
   }
   function nm_gera_mask(&$nm_campo, $nm_mask)
   { 
      $trab_campo = $nm_campo;
      $trab_mask  = $nm_mask;
      $tam_campo  = strlen($nm_campo);
      $trab_saida = "";
      $mask_num = false;
      for ($x=0; $x < strlen($trab_mask); $x++)
      {
          if (substr($trab_mask, $x, 1) == "#")
          {
              $mask_num = true;
              break;
          }
      }
      if ($mask_num )
      {
          $ver_duas = explode(";", $trab_mask);
          if (isset($ver_duas[1]) && !empty($ver_duas[1]))
          {
              $cont1 = count(explode("#", $ver_duas[0])) - 1;
              $cont2 = count(explode("#", $ver_duas[1])) - 1;
              if ($cont2 >= $tam_campo)
              {
                  $trab_mask = $ver_duas[1];
              }
              else
              {
                  $trab_mask = $ver_duas[0];
              }
          }
          $tam_mask = strlen($trab_mask);
          $xdados = 0;
          for ($x=0; $x < $tam_mask; $x++)
          {
              if (substr($trab_mask, $x, 1) == "#" && $xdados < $tam_campo)
              {
                  $trab_saida .= substr($trab_campo, $xdados, 1);
                  $xdados++;
              }
              elseif ($xdados < $tam_campo)
              {
                  $trab_saida .= substr($trab_mask, $x, 1);
              }
          }
          if ($xdados < $tam_campo)
          {
              $trab_saida .= substr($trab_campo, $xdados);
          }
          $nm_campo = $trab_saida;
          return;
      }
      for ($ix = strlen($trab_mask); $ix > 0; $ix--)
      {
           $char_mask = substr($trab_mask, $ix - 1, 1);
           if ($char_mask != "x" && $char_mask != "z")
           {
               $trab_saida = $char_mask . $trab_saida;
           }
           else
           {
               if ($tam_campo != 0)
               {
                   $trab_saida = substr($trab_campo, $tam_campo - 1, 1) . $trab_saida;
                   $tam_campo--;
               }
               else
               {
                   $trab_saida = "0" . $trab_saida;
               }
           }
      }
      if ($tam_campo != 0)
      {
          $trab_saida = substr($trab_campo, 0, $tam_campo) . $trab_saida;
          $trab_mask  = str_repeat("z", $tam_campo) . $trab_mask;
      }
   
      $iz = 0; 
      for ($ix = 0; $ix < strlen($trab_mask); $ix++)
      {
           $char_mask = substr($trab_mask, $ix, 1);
           if ($char_mask != "x" && $char_mask != "z")
           {
               if ($char_mask == "." || $char_mask == ",")
               {
                   $trab_saida = substr($trab_saida, 0, $iz) . substr($trab_saida, $iz + 1);
               }
               else
               {
                   $iz++;
               }
           }
           elseif ($char_mask == "x" || substr($trab_saida, $iz, 1) != "0")
           {
               $ix = strlen($trab_mask) + 1;
           }
           else
           {
               $trab_saida = substr($trab_saida, 0, $iz) . substr($trab_saida, $iz + 1);
           }
      }
      $nm_campo = $trab_saida;
   } 
}

?>
