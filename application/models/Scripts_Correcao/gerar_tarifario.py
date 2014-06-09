#! /usr/bin/env python
# -*- coding: iso-8859-1 -*-
import xlsxwriter
import sys
import socket

sys.path.append('/var/www/html/python/scoa/')
from connect_mysql import conn

import consultar_portos
import consultar_pais
import funcoes_tarifario
import funcoes_taxas
import funcoes_taxas_locais
import funcoes_proposta
from datetime import datetime


# Obtem o ip do server para formar a url
#s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
#s.connect(("gmail.com",80))
#ip_address = s.getsockname()[0]
#s.close()

ip_address = "189.38.56.122"

#Recebe os parametros via argv
script_php = sys.argv[0]
id_cliente = sys.argv[1]
sentido = sys.argv[2]
tarifario_padrao = sys.argv[3]
formato = sys.argv[4]

# array com os meses do ano
meses = ('JANEIRO','FEVEREIRO','MARCO','ABRIL','MAIO','JUNHO','JULHO','AGOSTO','SETEMBRO','OUTUBRO','NOVEMBRO','DEZEMBRO')

#Obtem a conexao com o banco de dados
db = conn.get_connection()

c = db.cursor()

sql = ("SELECT * "
	   "FROM " 
	   		"FINANCEIRO.tarifarios_pricing "
	   "WHERE "
	   		"ativo = 'S' AND "
	   		"rota_principal = 'S' AND modulo = %s "
	   "ORDER BY "
	   		"id_place_receipt, id_place_delivery, id_via") 

c.execute(sql,sentido)
		
today = datetime.now()

if( formato == 0 ):
	file_name = 'tarifario_' + sentido + '_' + str(today) + '.xlsx'	
else:
	file_name = 'tarifario_' + sentido + '_' + str(today) + '.scoa'

print file_name

file_name = 'tarifario_' + sentido + '_' + str(today) + '.xlsx'

workbook = xlsxwriter.Workbook("/var/www/html/allink/relatorios_temp/" + file_name)
worksheet = workbook.add_worksheet('Freight')

#Coloca os auto filtros nos cabeçarios das colunas
worksheet.autofilter('A5:AB5')

workbook.set_properties({
    'title':    'Tarifario Padrao da Allink',
    'subject':  'Tarifas',
    'author':   'Via SCOA',    
    'company':  'Allink Transportes Internacionais',
    'category': 'Planilha Excel',
    'keywords': 'Tarifario, Tarifa, Frete, Taxas',
    'comments': 'Created with Python and XlsxWriter'})

worksheet.merge_range('A1:D3',worksheet.insert_image('A1','/var/www/html/allink/Imagens/allink.jpg'))

linha = 5

#estilo dos headers
light_blue = workbook.add_format()

light_blue.set_font_color('black')
light_blue.set_bold()
light_blue.set_bg_color('#b9cde5')
light_blue.set_font_size('8')

#cria os estilos que vão ser aplicados na planilha

#estilo dos cabecarios
dark_blue = workbook.add_format()

dark_blue.set_bg_color('#1f497d')
dark_blue.set_font_color('white')
dark_blue.set_bold()
dark_blue.set_font_size('10')

#estilo da primeira linha
first_row_format = workbook.add_format()
first_row_format.set_bold()
first_row_format.set_font_size(12)

#estilo dos sub cabeçarios
sub_header = workbook.add_format()
sub_header.set_font_size(10)
sub_header.set_top()

borda = workbook.add_format()
borda.set_top()

# Se o cliente foi especificado busca o nome do cliente
if(id_cliente != 0 and tarifario_padrao == 'N'):

	db_cliente = conn.get_connection_dict()

	cli = db_cliente.cursor()

	sql_cliente = ("SELECT cnpj, razao FROM CLIENTES.clientes WHERE id_cliente = %s") % (id_cliente)

	cli.execute(sql_cliente)

	rowset = cli.fetchone()

	cliente = str(rowset['cnpj']) + " - " + str(rowset['razao']).decode('iso-8859-1','replace')

else:

	cliente = ""

if( sentido == "IMP" ):

	sentido_iso = "IMPORTACAO"
	label_pais = "ORIGIN COUNTRY"
	worksheet.set_column('K:K', None, None, {'hidden': 1})

else:

	sentido_iso = "EXPORTACAO"
	label_pais = "DESTINATION COUNTRY"

worksheet.write('E2', cliente + " -> TARIFARIO LCL " + sentido_iso.decode('iso-8859-1','replace') + " - " + str(meses[today.month - 1]) + " " + str(today.year), first_row_format)

#Sub cabeçarios com às unidades de medida
for x in xrange(0,28):
	worksheet.write(linha -2, x, "",borda)
	
worksheet.write(linha - 2, 7, "OFR", sub_header)
worksheet.write(linha - 2, 8, "OFR", sub_header)
worksheet.write(linha - 2, 9, "%", sub_header)
worksheet.write(linha - 2, 10, "WM", sub_header)

worksheet.write(linha - 1, 0, "ORIGIN", dark_blue)
worksheet.write(linha - 1, 1, "LOADING PORT", dark_blue)
worksheet.write(linha - 1, 2, "DISCHARGE PORT", dark_blue)
worksheet.write(linha - 1, 3, "DESTINATION", dark_blue)
worksheet.write(linha - 1, 4, "ADDITIONAL VIA", dark_blue)
worksheet.write(linha - 1, 5, label_pais, dark_blue)
worksheet.write(linha - 1, 6, "CURRENCY", dark_blue)
worksheet.write(linha - 1, 7, "WM", dark_blue)
worksheet.write(linha - 1, 8, "MIN", dark_blue)
worksheet.write(linha - 1, 9, "BUNKER", dark_blue)
worksheet.write(linha - 1, 10, "EFAF", dark_blue)
worksheet.write(linha - 1, 11, "ADDTIONAL OF FREIGHT", dark_blue)
worksheet.write(linha - 1, 12, "SEE SPECIFIC CHARGES", dark_blue)

worksheet.write(linha - 1, 13, "ACEITA CARGA IMO", dark_blue)
worksheet.write(linha - 1, 14, "ACEITA CARGA COLLECT", dark_blue)


worksheet.write(linha - 1, 15, "TT TRUCK", dark_blue)
worksheet.write(linha - 1, 16, "FREQ. TRUCK", dark_blue)
worksheet.write(linha - 1, 17, "TT 1", dark_blue)
worksheet.write(linha - 1, 18, "FREQ. 1", dark_blue)
worksheet.write(linha - 1, 19, "TT 2", dark_blue)
worksheet.write(linha - 1, 20, "FREQ 2", dark_blue)
worksheet.write(linha - 1, 21, "TT ADDTIONAL VIA", dark_blue)
worksheet.write(linha - 1, 22, "FREQ ADDTIONAL VIA", dark_blue)
worksheet.write(linha - 1, 23, "AGENT", dark_blue)
worksheet.write(linha - 1, 24, "REMARKS", dark_blue)
worksheet.write(linha - 1, 25, "CBM = PESO", dark_blue)
worksheet.write(linha - 1, 26, "INICIO", dark_blue)
worksheet.write(linha - 1, 27, "VALIDADE", dark_blue)

# Congela às celulas do cabeçario
worksheet.freeze_panes("A6:AA6")

for row in c:

	origem = consultar_portos.obter_porto(row[1],sentido,"origem")
	embarque = consultar_portos.obter_porto(row[4],sentido,"embarque")
	desembarque = consultar_portos.obter_porto(row[7],sentido,"desembarque")
	destino = consultar_portos.obter_porto(row[16],sentido,"destino")

	#define a via adicional se houver
	via_adicional = ""
	tt_truck = ""
	tt1 = ""
	tt2 = ""
	tt3 = ""
	freq_truck = ""
	freq_tt1 = ""
	freq_tt2 = ""
	freq_tt3 = ""
	agente = ""
	aceita_imo = ""
	aceita_cc = ""

	if(row[30] == "N"):
		aceita_cc = "N�O"
	else:
		aceita_cc = "SIM / SOB CONSULTA"	

	if(row[39] == "N"):
		aceita_imo = "N�O"		
	else:
		aceita_imo = "SIM / SOB CONSULTA"

	if(sentido == "IMP"):
						
		if( row[13] != 0 ):

			via_adicional = consultar_portos.obter_porto(row[13],sentido,"embarque")
			
			tt_truck = str(row[14])
			tt1 = str(row[2])
			tt2 = str(row[5])			
			tt3 = str(row[14])
			freq_truck = funcoes_tarifario.buscar_frequencia(row[15])
			freq_tt1 = funcoes_tarifario.buscar_frequencia(row[3])
			freq_tt2 = funcoes_tarifario.buscar_frequencia(row[6])
			freq_tt3 = funcoes_tarifario.buscar_frequencia(row[15])
			agente = funcoes_tarifario.buscar_agente(row[18])

		else:	
			
			tt_truck = str(row[8])
			tt1 = str(row[2])
			tt2 = str(row[5])		
			freq_truck = funcoes_tarifario.buscar_frequencia(row[9])			
			freq_tt1 = funcoes_tarifario.buscar_frequencia(row[3])				
			freq_tt2 = funcoes_tarifario.buscar_frequencia(row[6])			
			agente =  funcoes_tarifario.buscar_agente(row[18])

	else:
				
		if( row[13] != 0 ):
			
			via_adicional = consultar_portos.obter_porto(row[13],sentido,"destino")
			
			tt_truck = str(row[2])
			tt1 = str(row[5]) 
			tt2 = str(row[10])	
			tt3 = str(row[14])	
			freq_truck = funcoes_tarifario.buscar_frequencia(row[3])	
			freq_tt1 = funcoes_tarifario.buscar_frequencia(row[6])
			freq_tt2 = funcoes_tarifario.buscar_frequencia(row[11])
			freq_tt3 = funcoes_tarifario.buscar_frequencia(row[15])
			agente = funcoes_tarifario.buscar_agente(row[17])

		else :
			
			tt_truck = str(row[2])
			tt1 = str(row[5]) 
			tt2 = str(row[8])
			freq_truck = funcoes_tarifario.buscar_frequencia(row[3])					
			freq_tt1 = funcoes_tarifario.buscar_frequencia(row[6])
			freq_tt2 = funcoes_tarifario.buscar_frequencia(row[9])			
			agente = funcoes_tarifario.buscar_agente(row[17])


	if(tt3 == 0):	
		tt3 = ""

	#obtem o pais da rota 
	pais = consultar_pais.consultar_pais(row[12]) 

	#busca as taxas do tarifario
	taxas_tarifario = funcoes_tarifario.buscar_taxas_tarifario(row[0])

	taxas_formatadas = funcoes_tarifario.formatar_taxas_tarifario(taxas_tarifario)

	# pega o valor do frete + bunker + efaf
	frete = "0.00"
	frete_min = "0.00"
	moeda_frete = ""	
	bunker = ""
	efaf = ""

	for taxa_tarifario in taxas_tarifario:

		if( taxa_tarifario[3] == 10 ):

			frete = str("%.2f") % (taxa_tarifario[6])
			frete_min = str("%.2f") % (taxa_tarifario[7])
			moeda_frete = str(funcoes_taxas.sigla_moeda(taxa_tarifario[4]))
			inicio_frete = taxa_tarifario[17]
			validade_frete = taxa_tarifario[18]

		if( taxa_tarifario[3] == 13 ):

			#bunker = ( str(funcoes_taxas.sigla_moeda(taxa_tarifario[4])) + " %.2f" ) % (taxa_tarifario[6])
			bunker = ( "%.2f" ) % (taxa_tarifario[6])

		if( taxa_tarifario[3] == 1060 ):	

			efaf = ( str(funcoes_taxas.sigla_moeda(taxa_tarifario[4])) + " %.2f" ) % (taxa_tarifario[6])
	
	remarks_url = "http://"+str(ip_address)+"/Clientes/tarifario/aditional_information.php?key=" + str(row[0]) 
	charges_url = "http://"+str(ip_address)+"/Clientes/tarifario/standard_charges.php?key=" + str(row[0])	
	
	# se foi informado o id do cliente, então busca às propostas do cliente
	if(tarifario_padrao == "N" and id_cliente != 0 ):
		
		ponteiro = funcoes_proposta.buscar_proposta_cliente(id_cliente,row[0])
				
		proposta = ponteiro.fetchone()

		if( proposta != None ):

			# se encontrou a proposta então busca às taxas da proposta			
			ponteiro_taxas = funcoes_proposta.buscar_taxas_proposta(proposta['id_item_proposta'])

			taxas_formatadas = funcoes_proposta.formatar_taxas_proposta(ponteiro_taxas)

			charges_url = "http://"+str(ip_address)+"/Clientes/tarifario/specific_charges.php?key=" + str(proposta['id_item_proposta'])

			bunker = ""
			efaf = ""

			for taxa_proposta in ponteiro_taxas:
				
				if( taxa_proposta['id_taxa_adicional'] == 10 ):

					frete = str("%.2f") % (taxa_proposta['valor'])
					frete_min = str("%.2f") % (taxa_proposta['valor_minimo'])
					moeda_frete = str(funcoes_taxas.sigla_moeda(taxa_proposta['id_moeda']))
					inicio_frete = proposta['data_inicial']
					validade_frete = proposta['validade']

				if( taxa_proposta['id_taxa_adicional'] == 13 ):

					#bunker = ( "%.2f " + str(funcoes_taxas.sigla_moeda(taxa_proposta['id_moeda'])) ) % (taxa_proposta['valor'])
					bunker = ( "%.2f" ) % (taxa_proposta['valor'])

				if( taxa_proposta['id_taxa_adicional'] == 1060 ):	

					efaf = ( str(funcoes_taxas.sigla_moeda(taxa_proposta['id_moeda'])) + " %.2f" ) % (taxa_proposta['valor'])
	
	worksheet.write(linha, 0, origem.decode('iso-8859-1','replace'), light_blue)
	worksheet.write(linha, 1, embarque.decode('iso-8859-1','replace'), light_blue)
	worksheet.write(linha, 2, desembarque.decode('iso-8859-1','replace'), light_blue)
	worksheet.write(linha, 3, destino.decode('iso-8859-1','replace'), light_blue)
	worksheet.write(linha, 4, via_adicional.decode('utf-8','ignore'), light_blue)
	worksheet.write(linha, 5, pais.decode('iso-8859-1','replace'), light_blue)
	worksheet.write(linha, 6, moeda_frete, light_blue)
	worksheet.write(linha, 7, frete, light_blue)
	worksheet.write(linha, 8, frete_min, light_blue)
	worksheet.write(linha, 9, bunker, light_blue)
	worksheet.write(linha, 10, efaf, light_blue)
	worksheet.write(linha, 11, taxas_formatadas, light_blue)
	worksheet.write_url(linha, 12, charges_url, light_blue, "SEE SPECIFIC CHARGES", "Taxas especificas da rota")
	worksheet.write(linha, 13, aceita_imo.decode('iso-8859-1','replace'), light_blue)
	worksheet.write(linha, 14, aceita_cc.decode('iso-8859-1','replace'), light_blue)
	worksheet.write(linha, 15, tt_truck, light_blue)
	worksheet.write(linha, 16, freq_truck, light_blue)
	worksheet.write(linha, 17, tt1, light_blue)
	worksheet.write(linha, 18, freq_tt1, light_blue)
	worksheet.write(linha, 19, tt2, light_blue)
	worksheet.write(linha, 20, freq_tt2, light_blue)
	worksheet.write(linha, 21, tt3, light_blue) # tt via adicional
	worksheet.write(linha, 22, freq_tt3, light_blue) # freq via adicional
	worksheet.write(linha, 23, agente, light_blue)
	worksheet.write_url(linha, 24, remarks_url, light_blue, "SEE SPECIFIC INFORMATION", "Veja as informacoes especificas das rota")
	worksheet.write(linha, 25, str(row[38]) + " = " + str(row[37]), light_blue)
	worksheet.write(linha, 26, str(inicio_frete.day) + "/" + str(inicio_frete.month) + "/" + str(inicio_frete.year), light_blue)
	worksheet.write(linha, 27, str(validade_frete.day) + "/" + str(validade_frete.month) + "/" + str(validade_frete.year), light_blue)
		
	linha +=1

#Esconde às linhas de grade da planinha
worksheet.hide_gridlines(2)

#Inicio da planilha de taxas locais
worksheet_taxas = workbook.add_worksheet('Local Charges')

format_bold = workbook.add_format()
format_bold.set_bold()
format_bold.set_font_size(8)

green = workbook.add_format()
green.set_font_size(8)
green.set_bold()
green.set_bg_color('#98FB98')
green.set_align('center')
green.set_border(1)

port_title = workbook.add_format()
port_title.set_font_size(10)
port_title.set_bold()
port_title.set_border(1)
port_title.set_align('center')
port_title.set_align('vcenter')

std_text = workbook.add_format()
std_text.set_font_size(8)
std_text.set_bold()
std_text.set_border(1)
std_text.set_align('center')
std_text.set_align('vcenter')

# obtem as taxas locais dos portos
taxas_locais = funcoes_taxas_locais.obter_taxas_locais(id_cliente,sentido)

worksheet_taxas.write("B1","Atualizado em: " + str(today.day) + " " + meses[today.month - 1] + " de " + str(today.year), format_bold)

worksheet_taxas.merge_range("B2:Q2","LCL LOCAL CHARGES FOR SHIPMENTS",green)

worksheet_taxas.merge_range("B3:E3","PORT", port_title)
worksheet_taxas.merge_range("F3:O3", "CHARGES", port_title)
worksheet_taxas.merge_range("P3:Q3", "VALIDADE", port_title)

#primeira linha da planilha de taxas
first_line = 3

hoje = datetime.today()

for key, value in taxas_locais.items():
			
	worksheet_taxas.merge_range(first_line, 1, first_line + value.rowcount, 4, key, std_text)

	label_taxa = ""

	for taxas_porto in value:

		validade_padrao = "30/" + str(hoje.month) + "/" + str(hoje.year)

		if(id_cliente != 0):

			taxa_acordo = funcoes_taxas_locais.busca_taxa_acordo_cliente(id_cliente,taxas_porto[15],sentido,taxas_porto[3])

			if(taxa_acordo != None):

				validade_padrao = str(taxa_acordo['validade'].day) + "/" + str(taxa_acordo['validade'].month) + "/" + str(taxa_acordo['validade'].year)				
				unidade = funcoes_taxas.sigla_unidade(taxa_acordo['id_unidade'])
				moeda = funcoes_taxas.sigla_moeda(taxa_acordo['id_moeda'])

				label_taxa += (taxa_acordo['taxa_adicional'].decode('iso-8859-1','replace') + " " + moeda + " %.2f " + unidade + " | MIN. %.2f | MAX. %.2f\n") % (taxa_acordo['valor'],taxa_acordo['valor_minimo'],taxa_acordo['valor_maximo']) 
			
			else:

				label_taxa += (taxas_porto[2].decode('iso-8859-1','replace') + " " + taxas_porto[7] + " %.2f " + taxas_porto[10].decode('iso-8859-1','replace') + " | MIN. %.2f | MAX. %.2f\n") % (taxas_porto[4],taxas_porto[5],taxas_porto[6]) 
									
		else:	

			label_taxa += (taxas_porto[2].decode('iso-8859-1','replace') + " " + taxas_porto[7] + " %.2f " + taxas_porto[10].decode('iso-8859-1','replace') + " | MIN. %.2f | MAX. %.2f\n") % (taxas_porto[4],taxas_porto[5],taxas_porto[6]) 

	worksheet_taxas.merge_range(first_line, 5, first_line + value.rowcount, 14, label_taxa, std_text)

	worksheet_taxas.merge_range(first_line, 15, first_line + value.rowcount, 16, validade_padrao, std_text)	

	first_line += (value.rowcount + 1)

# adiciona a planina de importante information
#worksheet_information = workbook.add_worksheet('Important Information')

workbook.close()

print file_name
