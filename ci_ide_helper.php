<?php  die('This file is not really here!');

/**
 * ------------- DO NOT UPLOAD THIS FILE TO LIVE SERVER ---------------------
 *
 * Implements code completion for CodeIgniter in Sublime Text 2 Code Intel
 * Code Intel indexes all class constructs, so if this file is in the project it will be loaded.
 * --------------------------------------------------------------------------
 */

/**
 * @property CI_DB_active_record $db              This is the platform-independent base Active Record implementation class.
 * @property CI_DB_forge $dbforge                 Database Utility Class
 * @property CI_Benchmark $benchmark              This class enables you to mark points and calculate the time difference between them.<br />  Memory consumption can also be displayed.
 * @property CI_Calendar $calendar                This class enables the creation of calendars
 * @property CI_Cart $cart                        Shopping Cart Class
 * @property CI_Config $config                    This class contains functions that enable config files to be managed
 * @property CI_Controller $controller            This class object is the super class that every library in.<br />CodeIgniter will be assigned to.
 * @property CI_Email $email                      Permits email to be sent using Mail, Sendmail, or SMTP.
 * @property CI_Encrypt $encrypt                  Provides two-way keyed encoding using XOR Hashing and Mcrypt
 * @property CI_Exceptions $exceptions            Exceptions Class
 * @property CI_Form_validation $form_validation  Form Validation Class
 * @property CI_Ftp $ftp                          FTP Class
 * @property CI_Hooks $hooks                      Provides a mechanism to extend the base system without hacking.
 * @property CI_Image_lib $image_lib              Image Manipulation class
 * @property CI_Input $input                      Pre-processes global input data for security
 * @property CI_Lang $lang                        Language Class
 * @property CI_Loader $load                      Loads views and files
 * @property CI_Log $log                          Logging Class
 * @property CI_Model $model                      CodeIgniter Model Class
 * @property CI_Output $output                    Responsible for sending final output to browser
 * @property CI_Pagination $pagination            Pagination Class
 * @property CI_Parser $parser                    Parses pseudo-variables contained in the specified template view,<br />replacing them with the data in the second param
 * @property CI_Profiler $profiler                This class enables you to display benchmark, query, and other data<br />in order to help with debugging and optimization.
 * @property CI_Router $router                    Parses URIs and determines routing
 * @property CI_Session $session                  Session Class
 * @property CI_Sha1 $sha1                        Provides 160 bit hashing using The Secure Hash Algorithm
 * @property CI_Table $table                      HTML table generation<br />Lets you create tables manually or from database result objects, or arrays.
 * @property CI_Trackback $trackback              Trackback Sending/Receiving Class
 * @property CI_Typography $typography            Typography Class
 * @property CI_Unit_test $unit_test              Simple testing class
 * @property CI_Upload $upload                    File Uploading Class
 * @property CI_URI $uri                          Parses URIs and determines routing
 * @property CI_User_agent $user_agent            Identifies the platform, browser, robot, or mobile devise of the browsing agent
 * @property CI_Validation $validation            //dead
 * @property CI_Xmlrpc $xmlrpc                    XML-RPC request handler class
 * @property CI_Xmlrpcs $xmlrpcs                  XML-RPC server class
 * @property CI_Zip $zip                          Zip Compression Class
 * @property CI_Javascript $javascript            Javascript Class
 * @property CI_Jquery $jquery                    Jquery Class
 * @property CI_Utf8 $utf8                        Provides support for UTF-8 environments
 * @property CI_Security $security                Security Class, xss, csrf, etc...
 * ==============START USER DEFINED MODELS===============
 * @property Test_acordo_taxas_locais_model $test_acordo_taxas_locais_model    User Defined model
 * @property Test_taxa_acordo_model $test_taxa_acordo_model    User Defined model
 * @property Test_porto_taxas $test_porto_taxas    User Defined model
 * @property Test_busca_acordo_cliente $test_busca_acordo_cliente    User Defined model
 * @property Test_gera_numero_acordo $test_gera_numero_acordo    User Defined model
 * @property Test_buscador_taxas_locais $test_buscador_taxas_locais    User Defined model
 * @property Test_portos_acordos_model $test_portos_acordos_model    User Defined model
 * @property Test_acordos_taxas_facade $test_acordos_taxas_facade    User Defined model
 * @property Test_portos_acordos_entity $test_portos_acordos_entity    User Defined model
 * @property Test_clientes_acordos_taxas_model $test_clientes_acordos_taxas_model    User Defined model
 * @property Test_compara_taxas $test_compara_taxas    User Defined model
 * @property Test_cidade $test_cidade    User Defined model
 * @property Test_cliente $test_cliente    User Defined model
 * @property Test_sessao $test_sessao    User Defined model
 * @property Test_array_conversor $test_array_conversor    User Defined model
 * @property Test_proposta_model $test_proposta_model    User Defined model
 * @property Busca_Proposta_ExistenteTest $Busca_Proposta_ExistenteTest    User Defined model
 * @property Test_tarifario_exportacao_model $test_tarifario_exportacao_model    User Defined model
 * @property Test_tarifario_importacao_model $test_tarifario_importacao_model    User Defined model
 * @property Cliente_acordo_entity $cliente_acordo_entity    User Defined model
 * @property Acordo_taxas_entity $acordo_taxas_entity    User Defined model
 * @property Acordos_taxas_facade $acordos_taxas_facade    User Defined model
 * @property Verifica_acordos_cadastrados $verifica_acordos_cadastrados    User Defined model
 * @property Clientes_acordos_taxas_model $clientes_acordos_taxas_model    User Defined model
 * @property Gera_numero_acordo $gera_numero_acordo    User Defined model
 * @property Acordo_taxas_locais_model $acordo_taxas_locais_model    User Defined model
 * @property Buscador_taxas_locais $buscador_taxas_locais    User Defined model
 * @property Portos_acordos_model $portos_acordos_model    User Defined model
 * @property Conversor_taxas $conversor_taxas    User Defined model
 * @property Portos_taxas $portos_taxas    User Defined model
 * @property Portos_acordos_entity $portos_acordos_entity    User Defined model
 * @property Taxa_acordo_model $taxa_acordo_model    User Defined model
 * @property Busca_acordo_taxas_locais_cliente $busca_acordo_taxas_locais_cliente    User Defined model
 * @property Acordo_factory $acordo_factory    User Defined model
 * @property Vencimento_search_acordo $vencimento_search_acordo    User Defined model
 * @property Numero_search_acordo $numero_search_acordo    User Defined model
 * @property Cliente_search_acordo $cliente_search_acordo    User Defined model
 * @property Search_driver $search_driver    User Defined model
 * @property Database_operations $database_operations    User Defined model
 * @property Entity $Entity    User Defined model
 * @property Taxa_local_model $taxa_local_model    User Defined model
 * @property Unidade_model $unidade_model    User Defined model
 * @property Unidade $unidade    User Defined model
 * @property Compara_taxas $compara_taxas    User Defined model
 * @property Serializa_taxas $serializa_taxas    User Defined model
 * @property Taxa_tarifario_model $taxa_tarifario_model    User Defined model
 * @property Taxa_adicional $taxa_adicional    User Defined model
 * @property Taxa_model $taxa_model    User Defined model
 * @property Moeda_model $moeda_model    User Defined model
 * @property Item_proposta_taxa_model $item_proposta_taxa_model    User Defined model
 * @property Taxa_local $taxa_local    User Defined model
 * @property Frete $frete    User Defined model
 * @property Taxa $taxa    User Defined model
 * @property Moeda $moeda    User Defined model
 * @property Cnpj $cnpj    User Defined model
 * @property Define_classificacao $define_classificacao    User Defined model
 * @property Cliente_model $cliente_model    User Defined model
 * @property Cidade $cidade    User Defined model
 * @property Cliente $cliente    User Defined model
 * @property Contato_model $contato_model    User Defined model
 * @property Contato $contato    User Defined model
 * @property Concorrente $concorrente    User Defined model
 * @property Conversor $conversor    User Defined model
 * @property Sessao $sessao    User Defined model
 * @property Array_conversor $array_conversor    User Defined model
 * @property Proposta_especial $proposta_especial    User Defined model
 * @property Proposta_nac $proposta_nac    User Defined model
 * @property Proposta_factory $proposta_factory    User Defined model
 * @property Proposta_model $proposta_model    User Defined model
 * @property Proposta_cotacao $proposta_cotacao    User Defined model
 * @property Item_proposta $item_proposta    User Defined model
 * @property Proposta_spot $proposta_spot    User Defined model
 * @property Propostas_factory $propostas_factory    User Defined model
 * @property Proposta_tarifario $proposta_tarifario    User Defined model
 * @property Item_proposta_model $item_proposta_model    User Defined model
 * @property Status_item $status_item    User Defined model
 * @property Proposta $proposta    User Defined model
 * @property Busca_proposta_existente $busca_proposta_existente    User Defined model
 * @property Cliente_busca $cliente_busca    User Defined model
 * @property Finder $finder    User Defined model
 * @property Destino_busca $destino_busca    User Defined model
 * @property Origem_busca $origem_busca    User Defined model
 * @property Busca $busca    User Defined model
 * @property Search_factory $search_factory    User Defined model
 * @property Periodo_busca $periodo_busca    User Defined model
 * @property Numero_busca $numero_busca    User Defined model
 * @property Proposta_factory $proposta_factory    User Defined model
 * @property Relatorio $relatorio    User Defined model
 * @property Relatorio_desbloqueios $relatorio_desbloqueios    User Defined model
 * @property Relatorio_adapter $relatorio_adapter    User Defined model
 * @property Layout_relatorio_desbloqueio $layout_relatorio_desbloqueio    User Defined model
 * @property Layout $layout    User Defined model
 * @property Solicitacao_periodo_entity $solicitacao_periodo_entity    User Defined model
 * @property Autorizacao_taxa $autorizacao_taxa    User Defined model
 * @property Solicitacao $solicitacao    User Defined model
 * @property Solicitacao_taxa $solicitacao_taxa    User Defined model
 * @property Solicitacao_periodo $solicitacao_periodo    User Defined model
 * @property Autorizacao_periodo $autorizacao_periodo    User Defined model
 * @property Solicitacao_entity $solicitacao_entity    User Defined model
 * @property Solicitacao_taxa_entity $solicitacao_taxa_entity    User Defined model
 * @property Autorizacao $autorizacao    User Defined model
 * @property Gestor $gestor    User Defined model
 * @property Customer $customer    User Defined model
 * @property Gestor_filial $gestor_filial    User Defined model
 * @property Filial_model $filial_model    User Defined model
 * @property Filial $filial    User Defined model
 * @property Usuario $usuario    User Defined model
 * @property Vendedor $vendedor    User Defined model
 * @property Pricing $pricing    User Defined model
 * @property Usuario_model $usuario_model    User Defined model
 * @property Clientes_model $clientes_model    User Defined model
 * @property Ceps_model $ceps_model    User Defined model
 * @property Blogmodel $blogmodel    User Defined model
 * @property Formato_html $formato_html    User Defined model
 * @property Formato_csv $formato_csv    User Defined model
 * @property Formato_excel $formato_excel    User Defined model
 * @property Formato_pdf $formato_pdf    User Defined model
 * @property Formato $formato    User Defined model
 * @property Email_model $email_model    User Defined model
 * @property Email $email    User Defined model
 * @property Envio $envio    User Defined model
 * @property Tarifario_importacao_model $tarifario_importacao_model    User Defined model
 * @property Interface_tarifario_model $interface_tarifario_model    User Defined model
 * @property Tarifario_exportacao $tarifario_exportacao    User Defined model
 * @property Tarifario_exportacao_model $tarifario_exportacao_model    User Defined model
 * @property Porto_exportacao_model $porto_exportacao_model    User Defined model
 * @property Porto $porto    User Defined model
 * @property Tarifario $tarifario    User Defined model
 * @property Porto_importacao_model $porto_importacao_model    User Defined model
 * @property Tarifario_importacao $tarifario_importacao    User Defined model
 * @property Rota $rota    User Defined model
 * @property Interface_porto $interface_porto    User Defined model
 * @property Concrete_importacao_factory $concrete_importacao_factory    User Defined model
 * @property Concrete_exportacao_factory $concrete_exportacao_factory    User Defined model
 * @property Sentido_factory $sentido_factory    User Defined model
 * @property Concrete_factory $concrete_factory    User Defined model
 * @property Factory $factory    User Defined model
 * @property Tarifario_facade $tarifario_facade    User Defined model
 */
class CI_Controller{}

class MY_Controller extends CI_Controller {};

/**
 * @property CI_DB_active_record $db              This is the platform-independent base Active Record implementation class.
 * @property CI_DB_forge $dbforge                 Database Utility Class
 * @property CI_Benchmark $benchmark              This class enables you to mark points and calculate the time difference between them.<br />  Memory consumption can also be displayed.
 * @property CI_Calendar $calendar                This class enables the creation of calendars
 * @property CI_Cart $cart                        Shopping Cart Class
 * @property CI_Config $config                    This class contains functions that enable config files to be managed
 * @property CI_Controller $controller            This class object is the super class that every library in.<br />CodeIgniter will be assigned to.
 * @property CI_Email $email                      Permits email to be sent using Mail, Sendmail, or SMTP.
 * @property CI_Encrypt $encrypt                  Provides two-way keyed encoding using XOR Hashing and Mcrypt
 * @property CI_Exceptions $exceptions            Exceptions Class
 * @property CI_Form_validation $form_validation  Form Validation Class
 * @property CI_Ftp $ftp                          FTP Class
 * @property CI_Hooks $hooks                      Provides a mechanism to extend the base system without hacking.
 * @property CI_Image_lib $image_lib              Image Manipulation class
 * @property CI_Input $input                      Pre-processes global input data for security
 * @property CI_Lang $lang                        Language Class
 * @property CI_Loader $load                      Loads views and files
 * @property CI_Log $log                          Logging Class
 * @property CI_Model $model                      CodeIgniter Model Class
 * @property CI_Output $output                    Responsible for sending final output to browser
 * @property CI_Pagination $pagination            Pagination Class
 * @property CI_Parser $parser                    Parses pseudo-variables contained in the specified template view,<br />replacing them with the data in the second param
 * @property CI_Profiler $profiler                This class enables you to display benchmark, query, and other data<br />in order to help with debugging and optimization.
 * @property CI_Router $router                    Parses URIs and determines routing
 * @property CI_Session $session                  Session Class
 * @property CI_Sha1 $sha1                        Provides 160 bit hashing using The Secure Hash Algorithm
 * @property CI_Table $table                      HTML table generation<br />Lets you create tables manually or from database result objects, or arrays.
 * @property CI_Trackback $trackback              Trackback Sending/Receiving Class
 * @property CI_Typography $typography            Typography Class
 * @property CI_Unit_test $unit_test              Simple testing class
 * @property CI_Upload $upload                    File Uploading Class
 * @property CI_URI $uri                          Parses URIs and determines routing
 * @property CI_User_agent $user_agent            Identifies the platform, browser, robot, or mobile devise of the browsing agent
 * @property CI_Validation $validation            //dead
 * @property CI_Xmlrpc $xmlrpc                    XML-RPC request handler class
 * @property CI_Xmlrpcs $xmlrpcs                  XML-RPC server class
 * @property CI_Zip $zip                          Zip Compression Class
 * @property CI_Javascript $javascript            Javascript Class
 * @property CI_Jquery $jquery                    Jquery Class
 * @property CI_Utf8 $utf8                        Provides support for UTF-8 environments
 * @property CI_Security $security                Security Class, xss, csrf, etc...
 * ==============START USER DEFINED MODELS===============
 * @property Test_acordo_taxas_locais_model $test_acordo_taxas_locais_model    User Defined model
 * @property Test_taxa_acordo_model $test_taxa_acordo_model    User Defined model
 * @property Test_porto_taxas $test_porto_taxas    User Defined model
 * @property Test_busca_acordo_cliente $test_busca_acordo_cliente    User Defined model
 * @property Test_gera_numero_acordo $test_gera_numero_acordo    User Defined model
 * @property Test_buscador_taxas_locais $test_buscador_taxas_locais    User Defined model
 * @property Test_portos_acordos_model $test_portos_acordos_model    User Defined model
 * @property Test_acordos_taxas_facade $test_acordos_taxas_facade    User Defined model
 * @property Test_portos_acordos_entity $test_portos_acordos_entity    User Defined model
 * @property Test_clientes_acordos_taxas_model $test_clientes_acordos_taxas_model    User Defined model
 * @property Test_compara_taxas $test_compara_taxas    User Defined model
 * @property Test_cidade $test_cidade    User Defined model
 * @property Test_cliente $test_cliente    User Defined model
 * @property Test_sessao $test_sessao    User Defined model
 * @property Test_array_conversor $test_array_conversor    User Defined model
 * @property Test_proposta_model $test_proposta_model    User Defined model
 * @property Busca_Proposta_ExistenteTest $Busca_Proposta_ExistenteTest    User Defined model
 * @property Test_tarifario_exportacao_model $test_tarifario_exportacao_model    User Defined model
 * @property Test_tarifario_importacao_model $test_tarifario_importacao_model    User Defined model
 * @property Cliente_acordo_entity $cliente_acordo_entity    User Defined model
 * @property Acordo_taxas_entity $acordo_taxas_entity    User Defined model
 * @property Acordos_taxas_facade $acordos_taxas_facade    User Defined model
 * @property Verifica_acordos_cadastrados $verifica_acordos_cadastrados    User Defined model
 * @property Clientes_acordos_taxas_model $clientes_acordos_taxas_model    User Defined model
 * @property Gera_numero_acordo $gera_numero_acordo    User Defined model
 * @property Acordo_taxas_locais_model $acordo_taxas_locais_model    User Defined model
 * @property Buscador_taxas_locais $buscador_taxas_locais    User Defined model
 * @property Portos_acordos_model $portos_acordos_model    User Defined model
 * @property Conversor_taxas $conversor_taxas    User Defined model
 * @property Portos_taxas $portos_taxas    User Defined model
 * @property Portos_acordos_entity $portos_acordos_entity    User Defined model
 * @property Taxa_acordo_model $taxa_acordo_model    User Defined model
 * @property Busca_acordo_taxas_locais_cliente $busca_acordo_taxas_locais_cliente    User Defined model
 * @property Acordo_factory $acordo_factory    User Defined model
 * @property Vencimento_search_acordo $vencimento_search_acordo    User Defined model
 * @property Numero_search_acordo $numero_search_acordo    User Defined model
 * @property Cliente_search_acordo $cliente_search_acordo    User Defined model
 * @property Search_driver $search_driver    User Defined model
 * @property Database_operations $database_operations    User Defined model
 * @property Entity $Entity    User Defined model
 * @property Taxa_local_model $taxa_local_model    User Defined model
 * @property Unidade_model $unidade_model    User Defined model
 * @property Unidade $unidade    User Defined model
 * @property Compara_taxas $compara_taxas    User Defined model
 * @property Serializa_taxas $serializa_taxas    User Defined model
 * @property Taxa_tarifario_model $taxa_tarifario_model    User Defined model
 * @property Taxa_adicional $taxa_adicional    User Defined model
 * @property Taxa_model $taxa_model    User Defined model
 * @property Moeda_model $moeda_model    User Defined model
 * @property Item_proposta_taxa_model $item_proposta_taxa_model    User Defined model
 * @property Taxa_local $taxa_local    User Defined model
 * @property Frete $frete    User Defined model
 * @property Taxa $taxa    User Defined model
 * @property Moeda $moeda    User Defined model
 * @property Cnpj $cnpj    User Defined model
 * @property Define_classificacao $define_classificacao    User Defined model
 * @property Cliente_model $cliente_model    User Defined model
 * @property Cidade $cidade    User Defined model
 * @property Cliente $cliente    User Defined model
 * @property Contato_model $contato_model    User Defined model
 * @property Contato $contato    User Defined model
 * @property Concorrente $concorrente    User Defined model
 * @property Conversor $conversor    User Defined model
 * @property Sessao $sessao    User Defined model
 * @property Array_conversor $array_conversor    User Defined model
 * @property Proposta_especial $proposta_especial    User Defined model
 * @property Proposta_nac $proposta_nac    User Defined model
 * @property Proposta_factory $proposta_factory    User Defined model
 * @property Proposta_model $proposta_model    User Defined model
 * @property Proposta_cotacao $proposta_cotacao    User Defined model
 * @property Item_proposta $item_proposta    User Defined model
 * @property Proposta_spot $proposta_spot    User Defined model
 * @property Propostas_factory $propostas_factory    User Defined model
 * @property Proposta_tarifario $proposta_tarifario    User Defined model
 * @property Item_proposta_model $item_proposta_model    User Defined model
 * @property Status_item $status_item    User Defined model
 * @property Proposta $proposta    User Defined model
 * @property Busca_proposta_existente $busca_proposta_existente    User Defined model
 * @property Cliente_busca $cliente_busca    User Defined model
 * @property Finder $finder    User Defined model
 * @property Destino_busca $destino_busca    User Defined model
 * @property Origem_busca $origem_busca    User Defined model
 * @property Busca $busca    User Defined model
 * @property Search_factory $search_factory    User Defined model
 * @property Periodo_busca $periodo_busca    User Defined model
 * @property Numero_busca $numero_busca    User Defined model
 * @property Proposta_factory $proposta_factory    User Defined model
 * @property Relatorio $relatorio    User Defined model
 * @property Relatorio_desbloqueios $relatorio_desbloqueios    User Defined model
 * @property Relatorio_adapter $relatorio_adapter    User Defined model
 * @property Layout_relatorio_desbloqueio $layout_relatorio_desbloqueio    User Defined model
 * @property Layout $layout    User Defined model
 * @property Solicitacao_periodo_entity $solicitacao_periodo_entity    User Defined model
 * @property Autorizacao_taxa $autorizacao_taxa    User Defined model
 * @property Solicitacao $solicitacao    User Defined model
 * @property Solicitacao_taxa $solicitacao_taxa    User Defined model
 * @property Solicitacao_periodo $solicitacao_periodo    User Defined model
 * @property Autorizacao_periodo $autorizacao_periodo    User Defined model
 * @property Solicitacao_entity $solicitacao_entity    User Defined model
 * @property Solicitacao_taxa_entity $solicitacao_taxa_entity    User Defined model
 * @property Autorizacao $autorizacao    User Defined model
 * @property Gestor $gestor    User Defined model
 * @property Customer $customer    User Defined model
 * @property Gestor_filial $gestor_filial    User Defined model
 * @property Filial_model $filial_model    User Defined model
 * @property Filial $filial    User Defined model
 * @property Usuario $usuario    User Defined model
 * @property Vendedor $vendedor    User Defined model
 * @property Pricing $pricing    User Defined model
 * @property Usuario_model $usuario_model    User Defined model
 * @property Clientes_model $clientes_model    User Defined model
 * @property Ceps_model $ceps_model    User Defined model
 * @property Blogmodel $blogmodel    User Defined model
 * @property Formato_html $formato_html    User Defined model
 * @property Formato_csv $formato_csv    User Defined model
 * @property Formato_excel $formato_excel    User Defined model
 * @property Formato_pdf $formato_pdf    User Defined model
 * @property Formato $formato    User Defined model
 * @property Email_model $email_model    User Defined model
 * @property Email $email    User Defined model
 * @property Envio $envio    User Defined model
 * @property Tarifario_importacao_model $tarifario_importacao_model    User Defined model
 * @property Interface_tarifario_model $interface_tarifario_model    User Defined model
 * @property Tarifario_exportacao $tarifario_exportacao    User Defined model
 * @property Tarifario_exportacao_model $tarifario_exportacao_model    User Defined model
 * @property Porto_exportacao_model $porto_exportacao_model    User Defined model
 * @property Porto $porto    User Defined model
 * @property Tarifario $tarifario    User Defined model
 * @property Porto_importacao_model $porto_importacao_model    User Defined model
 * @property Tarifario_importacao $tarifario_importacao    User Defined model
 * @property Rota $rota    User Defined model
 * @property Interface_porto $interface_porto    User Defined model
 * @property Concrete_importacao_factory $concrete_importacao_factory    User Defined model
 * @property Concrete_exportacao_factory $concrete_exportacao_factory    User Defined model
 * @property Sentido_factory $sentido_factory    User Defined model
 * @property Concrete_factory $concrete_factory    User Defined model
 * @property Factory $factory    User Defined model
 * @property Tarifario_facade $tarifario_facade    User Defined model
 */
class CI_Model{}
