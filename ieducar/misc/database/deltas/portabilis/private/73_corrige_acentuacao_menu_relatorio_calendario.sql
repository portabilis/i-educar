  -- //

  --
  -- Corrige acentuação do menu Relatório > Cadastrais > Calendário do Ano Letivo
  --
  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  UPDATE portal.menu_submenu SET nm_submenu = 'Calendário do Ano Letivo' WHERE cod_menu_submenu = 999228 AND
                                                                               ref_cod_menu_menu = 55;
                        
  UPDATE pmicontrolesis.menu SET tt_menu = 'Calendário do Ano Letivo' WHERE cod_menu = 999228 AND
                                                                            ref_cod_menu_submenu = 999228;

  -- //@UNDO
  
  UPDATE portal.menu_submenu SET nm_submenu = 'Calendario do Ano Letivo' WHERE cod_menu_submenu = 999228 AND
                                                                                 ref_cod_menu_menu = 55;
                          
  UPDATE pmicontrolesis.menu SET tt_menu = 'Calendario do Ano Letivo' WHERE cod_menu = 999228 AND
                                                                              ref_cod_menu_submenu = 999228;
  -- //