  -- //

  -- Cria colunas necessárias para atender o registro 10 do Educacenso
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  CREATE TABLE modules.etapas_educacenso
  (
      id integer NOT NULL,
      nome character varying(255),
      CONSTRAINT etapas_educacenso_pk PRIMARY KEY (id)
    )
    WITH (
      OIDS=FALSE
  );

  CREATE TABLE modules.etapas_curso_educacenso
  (
      etapa_id INTEGER NOT NULL,
      curso_id INTEGER NOT NULL,
      CONSTRAINT etapas_curso_educacenso_pk PRIMARY KEY (etapa_id, curso_id),
      CONSTRAINT etapas_curso_educacenso_etapa_fk FOREIGN KEY (etapa_id)
      REFERENCES modules.etapas_educacenso (id) MATCH SIMPLE,
      CONSTRAINT etapas_curso_educacenso_curso_fk FOREIGN KEY (curso_id)
      REFERENCES pmieducar.curso (cod_curso) MATCH SIMPLE
    )
    WITH (
      OIDS=FALSE
  );


  INSERT INTO etapas_educacenso VALUES (1  ,'Educação Infantil - Creche (0 a 3 anos)');
  INSERT INTO etapas_educacenso VALUES (2  ,'Educação Infantil - Pré-escola (4 a 5 anos)');
  INSERT INTO etapas_educacenso VALUES (3  ,'Ensino Fundamental 8 anos');
  INSERT INTO etapas_educacenso VALUES (4  ,'Ensino Fundamental 9 anos');
  INSERT INTO etapas_educacenso VALUES (5  ,'Ensino médio - Médio');
  INSERT INTO etapas_educacenso VALUES (6  ,'Ensino médio - Integrado');
  INSERT INTO etapas_educacenso VALUES (7  ,'Ensino médio - Normal/Magistério');
  INSERT INTO etapas_educacenso VALUES (8  ,'Ensino médio - Educação profissional');
  INSERT INTO etapas_educacenso VALUES (9  ,'EJA - Ensino fundamental');
  INSERT INTO etapas_educacenso VALUES (10 ,'EJA - Ensino médio');
  INSERT INTO etapas_educacenso VALUES (11 ,'EJA - Ensino fundamental - Projovem (urbano)');

  CREATE TABLE modules.lingua_indigena_educacenso
  (
    id integer NOT NULL,
    lingua character varying(255),
    CONSTRAINT lingua_indigena_educacenso_pk PRIMARY KEY (id)
  )
  WITH (
    OIDS=FALSE
  );

  INSERT INTO lingua_indigena_educacenso VALUES ('30', 'Boróro');

  INSERT INTO lingua_indigena_educacenso VALUES ('244','Umutína');

  INSERT INTO lingua_indigena_educacenso VALUES ('42','Guató');

  INSERT INTO lingua_indigena_educacenso VALUES ('12','Apinayé');

  INSERT INTO lingua_indigena_educacenso VALUES ('251','Canela');

  INSERT INTO lingua_indigena_educacenso VALUES ('252','Kanéla Apaniekra/Canela Apaniekrã');

  INSERT INTO lingua_indigena_educacenso VALUES ('205','Kanéla Rankocamekra/ Canela Ramkokamekrã');

  INSERT INTO lingua_indigena_educacenso VALUES ('253','Gavião Krikatêjê/ Gavião Krinkatejé');

  INSERT INTO lingua_indigena_educacenso VALUES ('254','Gavião Parkatêjê/ Guató Parakatejé/ Gavião do Pará');

  INSERT INTO lingua_indigena_educacenso VALUES ('240','Gavião Pukobiyé/ Gavião Pukobié');

  INSERT INTO lingua_indigena_educacenso VALUES ('255','Krahô/ Crao, Kraô');

  INSERT INTO lingua_indigena_educacenso VALUES ('256','Krao Kanela');

  INSERT INTO lingua_indigena_educacenso VALUES ('223','Kreje/ Krenjé');

  INSERT INTO lingua_indigena_educacenso VALUES ('224','Krikatí/ Krinkatí');

  INSERT INTO lingua_indigena_educacenso VALUES ('257','Kokuiregatêjê/ Kokuiregatejje');

  INSERT INTO lingua_indigena_educacenso VALUES ('258','Timbira');

  INSERT INTO lingua_indigena_educacenso VALUES ('235','Kaingáng');

  INSERT INTO lingua_indigena_educacenso VALUES ('221','Mebengokré (Kayapó)');

  INSERT INTO lingua_indigena_educacenso VALUES ('86','Mentuktíre, Txukahamae (Kayapó)');

  INSERT INTO lingua_indigena_educacenso VALUES ('220','Kayapó');

  INSERT INTO lingua_indigena_educacenso VALUES ('81','Gorotire (Kayapó)');

  INSERT INTO lingua_indigena_educacenso VALUES ('82','Kararaô (Kayapó)');

  INSERT INTO lingua_indigena_educacenso VALUES ('83','Kokraimoro (Kayapó)');

  INSERT INTO lingua_indigena_educacenso VALUES ('84','Kubenkrngkegn (Kayapó)');

  INSERT INTO lingua_indigena_educacenso VALUES ('85','Menkrangnoti (Kayapó)');

  INSERT INTO lingua_indigena_educacenso VALUES ('87','Xikrin (Kayapó)');

  INSERT INTO lingua_indigena_educacenso VALUES ('132','Panará, Krenakarôre/ Kren-akarôre');

  INSERT INTO lingua_indigena_educacenso VALUES ('150','Suyá, Kisêdjê/ Kisedjê');

  INSERT INTO lingua_indigena_educacenso VALUES ('241','Tapayúna');

  INSERT INTO lingua_indigena_educacenso VALUES ('259','Xacriabá/ Xakriabá');

  INSERT INTO lingua_indigena_educacenso VALUES ('5','Xavánte/ Xavante');

  INSERT INTO lingua_indigena_educacenso VALUES ('6','Xerénte');

  INSERT INTO lingua_indigena_educacenso VALUES ('188','Xokléng');

  INSERT INTO lingua_indigena_educacenso VALUES ('260','Jê (não específico)');

  INSERT INTO lingua_indigena_educacenso VALUES ('63','Karajá');

  INSERT INTO lingua_indigena_educacenso VALUES ('50','Javaé');

  INSERT INTO lingua_indigena_educacenso VALUES ('185','Xambioá');

  INSERT INTO lingua_indigena_educacenso VALUES ('92','Krenák');

  INSERT INTO lingua_indigena_educacenso VALUES ('107','Maxakalí');

  INSERT INTO lingua_indigena_educacenso VALUES ('261','Pataxó');

  INSERT INTO lingua_indigena_educacenso VALUES ('262','Pataxó Hã Hã Hãe/ Pataxó Há-Há-Há');

  INSERT INTO lingua_indigena_educacenso VALUES ('129','Ofayé');

  INSERT INTO lingua_indigena_educacenso VALUES ('142','Canoeiros/ Rikbaktsá');

  INSERT INTO lingua_indigena_educacenso VALUES ('192','Fulni-ô/ Yathê');

  INSERT INTO lingua_indigena_educacenso VALUES ('65','Karitiána');

  INSERT INTO lingua_indigena_educacenso VALUES ('23','Awetí');

  INSERT INTO lingua_indigena_educacenso VALUES ('51','Jurúna/ Yudjá');

  INSERT INTO lingua_indigena_educacenso VALUES ('187','Xipáya');

  INSERT INTO lingua_indigena_educacenso VALUES ('106','Mawé');

  INSERT INTO lingua_indigena_educacenso VALUES ('200','Arara do Aripuana/ Arara do Aripuanã');

  INSERT INTO lingua_indigena_educacenso VALUES ('19','Aruá');

  INSERT INTO lingua_indigena_educacenso VALUES ('31','Cinta Larga/ Cinta-Larga');

  INSERT INTO lingua_indigena_educacenso VALUES ('36','Gavião (Ikõro, Digüt), Gavião de Rondônia/ Ikolen');

  INSERT INTO lingua_indigena_educacenso VALUES ('197','Zoró');

  INSERT INTO lingua_indigena_educacenso VALUES ('263','Salamãy');

  INSERT INTO lingua_indigena_educacenso VALUES ('236','Suruí de Rondônia');

  INSERT INTO lingua_indigena_educacenso VALUES ('111','Mondé, Tupí-Mondé');

  INSERT INTO lingua_indigena_educacenso VALUES ('112','Mundurukú');

  INSERT INTO lingua_indigena_educacenso VALUES ('96','Kuruáya');

  INSERT INTO lingua_indigena_educacenso VALUES ('141','Puruborá');

  INSERT INTO lingua_indigena_educacenso VALUES ('66','Arara de Rondônia/ Káro');

  INSERT INTO lingua_indigena_educacenso VALUES ('264','Ramaráma');

  INSERT INTO lingua_indigena_educacenso VALUES ('265','Urucú/ Urucu');

  INSERT INTO lingua_indigena_educacenso VALUES ('198','Akuntsú');

  INSERT INTO lingua_indigena_educacenso VALUES ('99','Makuráp');

  INSERT INTO lingua_indigena_educacenso VALUES ('110','Sakurabiat/ Kampé');

  INSERT INTO lingua_indigena_educacenso VALUES ('171','Tuparí');

  INSERT INTO lingua_indigena_educacenso VALUES ('2','Ajuru/ Wayoro, Ajurú');

  INSERT INTO lingua_indigena_educacenso VALUES ('7','Amanayé');

  INSERT INTO lingua_indigena_educacenso VALUES ('11','Apiaká');

  INSERT INTO lingua_indigena_educacenso VALUES ('17','Araweté');

  INSERT INTO lingua_indigena_educacenso VALUES ('20','Asuriní do Tocantins');

  INSERT INTO lingua_indigena_educacenso VALUES ('237','Parakanã');

  INSERT INTO lingua_indigena_educacenso VALUES ('4','Suruí do Pará/ Suruí do Tocantins/ Aikewara');

  INSERT INTO lingua_indigena_educacenso VALUES ('21','Asuriní do Xingu');

  INSERT INTO lingua_indigena_educacenso VALUES ('22','Ava-Canoeiro/ Avá-Canoeiro, Avá, Canoeiro');

  INSERT INTO lingua_indigena_educacenso VALUES ('37','Guajá');

  INSERT INTO lingua_indigena_educacenso VALUES ('266','Guaraní');

  INSERT INTO lingua_indigena_educacenso VALUES ('38','Guaraní Kaiowá/ Guarani Kayová');

  INSERT INTO lingua_indigena_educacenso VALUES ('39','Guaraní Mbyá');

  INSERT INTO lingua_indigena_educacenso VALUES ('40','Guaraní Nhandéva');

  INSERT INTO lingua_indigena_educacenso VALUES ('52','Kaapor/ Urubu, Kaapór');

  INSERT INTO lingua_indigena_educacenso VALUES ('267','Lingua De Sinais Kaapor/ Língua de Sinais Urubu-Kaapór');

  INSERT INTO lingua_indigena_educacenso VALUES ('59','Kamayurá');

  INSERT INTO lingua_indigena_educacenso VALUES ('199','Amondáwa');

  INSERT INTO lingua_indigena_educacenso VALUES ('208','Diahói/ Diahui');

  INSERT INTO lingua_indigena_educacenso VALUES ('215','Júma/ Juma');

  INSERT INTO lingua_indigena_educacenso VALUES ('219','Karipúna');

  INSERT INTO lingua_indigena_educacenso VALUES ('268','Kawahíb');

  INSERT INTO lingua_indigena_educacenso VALUES ('238','Parintintín');

  INSERT INTO lingua_indigena_educacenso VALUES ('243','Tenharím/ Tenharim');

  INSERT INTO lingua_indigena_educacenso VALUES ('76','Uru-Eu-Wau-Wau/ Uruewawau');

  INSERT INTO lingua_indigena_educacenso VALUES ('80','Kayabí');

  INSERT INTO lingua_indigena_educacenso VALUES ('90','Kokáma');

  INSERT INTO lingua_indigena_educacenso VALUES ('204','Kambéba');

  INSERT INTO lingua_indigena_educacenso VALUES ('126','Lingua Geral Amazônica, Nheengatu');

  INSERT INTO lingua_indigena_educacenso VALUES ('151','Tapirapé');

  INSERT INTO lingua_indigena_educacenso VALUES ('211','Guajajára');

  INSERT INTO lingua_indigena_educacenso VALUES ('155','Tembé');

  INSERT INTO lingua_indigena_educacenso VALUES ('269','Turiwára');

  INSERT INTO lingua_indigena_educacenso VALUES ('183','Wayampí/ Oyampi');

  INSERT INTO lingua_indigena_educacenso VALUES ('186','Xetá');

  INSERT INTO lingua_indigena_educacenso VALUES ('196','Zoé');

  INSERT INTO lingua_indigena_educacenso VALUES ('270','Tupí-Guaraní');

  INSERT INTO lingua_indigena_educacenso VALUES ('250','Tupí, Tupi Antigo');

  INSERT INTO lingua_indigena_educacenso VALUES ('13','Apurinã');

  INSERT INTO lingua_indigena_educacenso VALUES ('60','Ashanínka/ Axanínka');

  INSERT INTO lingua_indigena_educacenso VALUES ('26','Baníwa/ Tapiira Tapuya, Kawa Tapuya');

  INSERT INTO lingua_indigena_educacenso VALUES ('226','Kuripáko');

  INSERT INTO lingua_indigena_educacenso VALUES ('29','Baré');

  INSERT INTO lingua_indigena_educacenso VALUES ('145','Enawenê-Nawê');

  INSERT INTO lingua_indigena_educacenso VALUES ('271','Kaixána/ Kayuisiana');

  INSERT INTO lingua_indigena_educacenso VALUES ('222','Kinikináu, Kinikinawa');

  INSERT INTO lingua_indigena_educacenso VALUES ('272','Machinéri');

  INSERT INTO lingua_indigena_educacenso VALUES ('273','Mawayána');

  INSERT INTO lingua_indigena_educacenso VALUES ('109','Mehináku');

  INSERT INTO lingua_indigena_educacenso VALUES ('131','Palikúr');

  INSERT INTO lingua_indigena_educacenso VALUES ('133','Paresí');

  INSERT INTO lingua_indigena_educacenso VALUES ('152','Tariána');

  INSERT INTO lingua_indigena_educacenso VALUES ('156','Teréna');

  INSERT INTO lingua_indigena_educacenso VALUES ('179','Wapixána');

  INSERT INTO lingua_indigena_educacenso VALUES ('180','Warekéna');

  INSERT INTO lingua_indigena_educacenso VALUES ('182','Wauja/ Waurá');

  INSERT INTO lingua_indigena_educacenso VALUES ('193','Yawalapití');

  INSERT INTO lingua_indigena_educacenso VALUES ('274','Aruák');

  INSERT INTO lingua_indigena_educacenso VALUES ('10','Apalaí');

  INSERT INTO lingua_indigena_educacenso VALUES ('201','Arara do Pará, Arara do Xingu');

  INSERT INTO lingua_indigena_educacenso VALUES ('24','Bakairí');

  INSERT INTO lingua_indigena_educacenso VALUES ('35','Galibí do Oiapoque, Galibí (Karíña)');

  INSERT INTO lingua_indigena_educacenso VALUES ('43','Hixkaryána');

  INSERT INTO lingua_indigena_educacenso VALUES ('173','Ikpeng/ Ikpéng');

  INSERT INTO lingua_indigena_educacenso VALUES ('45','Ingarikó');

  INSERT INTO lingua_indigena_educacenso VALUES ('58','Kalapálo');

  INSERT INTO lingua_indigena_educacenso VALUES ('94','Kuikúro');

  INSERT INTO lingua_indigena_educacenso VALUES ('103','Matipú');

  INSERT INTO lingua_indigena_educacenso VALUES ('116','Nahukwá');

  INSERT INTO lingua_indigena_educacenso VALUES ('275','Naravúte');

  INSERT INTO lingua_indigena_educacenso VALUES ('276','Kaxuyána/ Kahyána, Warikyána');

  INSERT INTO lingua_indigena_educacenso VALUES ('277','Xikuyána/ Sikiyána');

  INSERT INTO lingua_indigena_educacenso VALUES ('100','Makuxí');

  INSERT INTO lingua_indigena_educacenso VALUES ('218','Kapon Patamóna/ Kapon Ptamóna');

  INSERT INTO lingua_indigena_educacenso VALUES ('153','Taulipáng');

  INSERT INTO lingua_indigena_educacenso VALUES ('167','Tiriyó/ Tarona');

  INSERT INTO lingua_indigena_educacenso VALUES ('177','Wái Wái/ Waiwái');

  INSERT INTO lingua_indigena_educacenso VALUES ('176','Waimirí-Atroarí');

  INSERT INTO lingua_indigena_educacenso VALUES ('184','Wayána');

  INSERT INTO lingua_indigena_educacenso VALUES ('108','Yekuána, Mayongong, Makiritáre,');

  INSERT INTO lingua_indigena_educacenso VALUES ('278','Karib');

  INSERT INTO lingua_indigena_educacenso VALUES ('15','Arara do Acre, Shawãdawa');

  INSERT INTO lingua_indigena_educacenso VALUES ('68','Katukína do Acre');

  INSERT INTO lingua_indigena_educacenso VALUES ('77','Kaxararí');

  INSERT INTO lingua_indigena_educacenso VALUES ('78','Kaxinawá');

  INSERT INTO lingua_indigena_educacenso VALUES ('91','Korúbo');

  INSERT INTO lingua_indigena_educacenso VALUES ('279','Kulína Páno');

  INSERT INTO lingua_indigena_educacenso VALUES ('102','Marúbo');

  INSERT INTO lingua_indigena_educacenso VALUES ('104','Matís');

  INSERT INTO lingua_indigena_educacenso VALUES ('105','Matsés');

  INSERT INTO lingua_indigena_educacenso VALUES ('389','Maya');

  INSERT INTO lingua_indigena_educacenso VALUES ('128','Nukiní');

  INSERT INTO lingua_indigena_educacenso VALUES ('140','Poyanáwa');

  INSERT INTO lingua_indigena_educacenso VALUES ('246','Shanenáwa/ Xanenáwa, Xawanawa');

  INSERT INTO lingua_indigena_educacenso VALUES ('49','Yamináwa');

  INSERT INTO lingua_indigena_educacenso VALUES ('194','Yawanawá');

  INSERT INTO lingua_indigena_educacenso VALUES ('280','Pano');

  INSERT INTO lingua_indigena_educacenso VALUES ('14','Arapáso');

  INSERT INTO lingua_indigena_educacenso VALUES ('28','Bará');

  INSERT INTO lingua_indigena_educacenso VALUES ('203','Barasána');

  INSERT INTO lingua_indigena_educacenso VALUES ('33','Desána');

  INSERT INTO lingua_indigena_educacenso VALUES ('64','Karapanã');

  INSERT INTO lingua_indigena_educacenso VALUES ('178','Wanána/ Guanána');

  INSERT INTO lingua_indigena_educacenso VALUES ('93','Kubéo, Kubewa');

  INSERT INTO lingua_indigena_educacenso VALUES ('281','Makúna, Yebá-masã');

  INSERT INTO lingua_indigena_educacenso VALUES ('282','Siriáno/ Suriana, Suriána');

  INSERT INTO lingua_indigena_educacenso VALUES ('234','Tukáno / Miriti-Tapuia');

  INSERT INTO lingua_indigena_educacenso VALUES ('172','Tuyúca / Tuyuca');

  INSERT INTO lingua_indigena_educacenso VALUES ('216','Yurutí, Juriti');

  INSERT INTO lingua_indigena_educacenso VALUES ('138','Piratapúya');

  INSERT INTO lingua_indigena_educacenso VALUES ('283','Arawá');

  INSERT INTO lingua_indigena_educacenso VALUES ('25','Banawá');

  INSERT INTO lingua_indigena_educacenso VALUES ('32','Dení');

  INSERT INTO lingua_indigena_educacenso VALUES ('284','Himarimã/ Hi-merimã, Mirimã, Himarimá');

  INSERT INTO lingua_indigena_educacenso VALUES ('285','Jamamadí-Kanamanti/ Jamamadí');

  INSERT INTO lingua_indigena_educacenso VALUES ('48','Jarawára');

  INSERT INTO lingua_indigena_educacenso VALUES ('95','Kulína Madijá/ Kulina, Kulína Madihá (Madija)');

  INSERT INTO lingua_indigena_educacenso VALUES ('136','Paumarí');

  INSERT INTO lingua_indigena_educacenso VALUES ('148','Zuruwahá, Suruahá (Zuruahá)');

  INSERT INTO lingua_indigena_educacenso VALUES ('61','Kanamarí');

  INSERT INTO lingua_indigena_educacenso VALUES ('69','Katukína');

  INSERT INTO lingua_indigena_educacenso VALUES ('174','Tsohom Djapa/ Tsohondjapá (Tsohom Djapa)');

  INSERT INTO lingua_indigena_educacenso VALUES ('67','Katawixí');

  INSERT INTO lingua_indigena_educacenso VALUES ('34','Dâw');

  INSERT INTO lingua_indigena_educacenso VALUES ('286','Hup, Húpda, Maku, Yuhupde, Yuhúp');

  INSERT INTO lingua_indigena_educacenso VALUES ('115','Nadëb');

  INSERT INTO lingua_indigena_educacenso VALUES ('287','Alaketesú');

  INSERT INTO lingua_indigena_educacenso VALUES ('288','Alantesú');

  INSERT INTO lingua_indigena_educacenso VALUES ('289','Hahaintesú');

  INSERT INTO lingua_indigena_educacenso VALUES ('290','Halotesú');

  INSERT INTO lingua_indigena_educacenso VALUES ('291','Kithaulú');

  INSERT INTO lingua_indigena_educacenso VALUES ('231','Mandúka/ Nambikwára do Campo');

  INSERT INTO lingua_indigena_educacenso VALUES ('292','Sararé');

  INSERT INTO lingua_indigena_educacenso VALUES ('300','Sawentesú');

  INSERT INTO lingua_indigena_educacenso VALUES ('301','Waikisú');

  INSERT INTO lingua_indigena_educacenso VALUES ('302','Wakalitesú');

  INSERT INTO lingua_indigena_educacenso VALUES ('303','Wasusú');

  INSERT INTO lingua_indigena_educacenso VALUES ('228','Lakondê');

  INSERT INTO lingua_indigena_educacenso VALUES ('229','Latundê');

  INSERT INTO lingua_indigena_educacenso VALUES ('120','Negarotê/ Negarote');

  INSERT INTO lingua_indigena_educacenso VALUES ('230','Mamaindê');

  INSERT INTO lingua_indigena_educacenso VALUES ('242','Tawandê');

  INSERT INTO lingua_indigena_educacenso VALUES ('143','Sabanê');

  INSERT INTO lingua_indigena_educacenso VALUES ('304','Nambikwára');

  INSERT INTO lingua_indigena_educacenso VALUES ('225','Kujubím');

  INSERT INTO lingua_indigena_educacenso VALUES ('305','Miguelénho/ Migueleno, Miguelenho');

  INSERT INTO lingua_indigena_educacenso VALUES ('130','Oro Win');

  INSERT INTO lingua_indigena_educacenso VALUES ('168','Torá');

  INSERT INTO lingua_indigena_educacenso VALUES ('175','Urupá');

  INSERT INTO lingua_indigena_educacenso VALUES ('245','Pakaá Nóva/Migueleno, Miguelenho');

  INSERT INTO lingua_indigena_educacenso VALUES ('306','Txapakúra');

  INSERT INTO lingua_indigena_educacenso VALUES ('127','Ninám');

  INSERT INTO lingua_indigena_educacenso VALUES ('146','Sanumá');

  INSERT INTO lingua_indigena_educacenso VALUES ('190','Yanomám/ Yanonmán');

  INSERT INTO lingua_indigena_educacenso VALUES ('191','Yanomámi');

  INSERT INTO lingua_indigena_educacenso VALUES ('307','Bóra');

  INSERT INTO lingua_indigena_educacenso VALUES ('233','Miránha');

  INSERT INTO lingua_indigena_educacenso VALUES ('53','Kadiwéu');

  INSERT INTO lingua_indigena_educacenso VALUES ('308','Guaikurú');

  INSERT INTO lingua_indigena_educacenso VALUES ('113','Múra');

  INSERT INTO lingua_indigena_educacenso VALUES ('137','Pirahã');

  INSERT INTO lingua_indigena_educacenso VALUES ('206','Chamakóko/ Samúko, Chamacoco');

  INSERT INTO lingua_indigena_educacenso VALUES ('207','Chiquitáno/ Chiquito');

  INSERT INTO lingua_indigena_educacenso VALUES ('18','Arikapú/ Jabutí');

  INSERT INTO lingua_indigena_educacenso VALUES ('47','Djeoromitxí/ Jabotí/ Jabutí');

  INSERT INTO lingua_indigena_educacenso VALUES ('309','Witóto');

  INSERT INTO lingua_indigena_educacenso VALUES ('1','Aikaná/ Aikanã');

  INSERT INTO lingua_indigena_educacenso VALUES ('46','Irántxe');

  INSERT INTO lingua_indigena_educacenso VALUES ('114','Mynky/ Mynký, Meky, Menky, Menki');

  INSERT INTO lingua_indigena_educacenso VALUES ('227','Kwazá');

  INSERT INTO lingua_indigena_educacenso VALUES ('62','Kanoé/ Kanoê');

  INSERT INTO lingua_indigena_educacenso VALUES ('157','Tikúna');

  INSERT INTO lingua_indigena_educacenso VALUES ('169','Trumái');

  INSERT INTO lingua_indigena_educacenso VALUES ('135','Galibí Marwórno/ Galibi Marworno');

  INSERT INTO lingua_indigena_educacenso VALUES ('134','Karipúna do Amapá');

  INSERT INTO lingua_indigena_educacenso VALUES ('310','Acona/ Akona');

  INSERT INTO lingua_indigena_educacenso VALUES ('311','Aimoré');

  INSERT INTO lingua_indigena_educacenso VALUES ('312','Anacé');

  INSERT INTO lingua_indigena_educacenso VALUES ('313','Apolima - Arara');

  INSERT INTO lingua_indigena_educacenso VALUES ('314','Arana');

  INSERT INTO lingua_indigena_educacenso VALUES ('315','Arapiun');

  INSERT INTO lingua_indigena_educacenso VALUES ('316','Arikén');

  INSERT INTO lingua_indigena_educacenso VALUES ('317','Arikose');

  INSERT INTO lingua_indigena_educacenso VALUES ('318','Atikum');

  INSERT INTO lingua_indigena_educacenso VALUES ('319','Awi');

  INSERT INTO lingua_indigena_educacenso VALUES ('320','Baenã');

  INSERT INTO lingua_indigena_educacenso VALUES ('321','Borari');

  INSERT INTO lingua_indigena_educacenso VALUES ('322','Botocudo');

  INSERT INTO lingua_indigena_educacenso VALUES ('323','Catokin (Katukína)');

  INSERT INTO lingua_indigena_educacenso VALUES ('324','Charrúa/ Charrua');

  INSERT INTO lingua_indigena_educacenso VALUES ('325','Coiupanka');

  INSERT INTO lingua_indigena_educacenso VALUES ('326','Guara');

  INSERT INTO lingua_indigena_educacenso VALUES ('327','Guarino');

  INSERT INTO lingua_indigena_educacenso VALUES ('328','Guaru');

  INSERT INTO lingua_indigena_educacenso VALUES ('329','Isse');

  INSERT INTO lingua_indigena_educacenso VALUES ('330','Jaricuna');

  INSERT INTO lingua_indigena_educacenso VALUES ('331','Jeripancó/ Jeripankó');

  INSERT INTO lingua_indigena_educacenso VALUES ('332','Kaete');

  INSERT INTO lingua_indigena_educacenso VALUES ('333','Kaimbé');

  INSERT INTO lingua_indigena_educacenso VALUES ('334','Kalabassa');

  INSERT INTO lingua_indigena_educacenso VALUES ('335','Kalankó');

  INSERT INTO lingua_indigena_educacenso VALUES ('336','Kamba/ Kámba');

  INSERT INTO lingua_indigena_educacenso VALUES ('337','Kambiwá');

  INSERT INTO lingua_indigena_educacenso VALUES ('338','Kambiwá Pipipã');

  INSERT INTO lingua_indigena_educacenso VALUES ('339','Kanindé');

  INSERT INTO lingua_indigena_educacenso VALUES ('340','Kantaruré');

  INSERT INTO lingua_indigena_educacenso VALUES ('341','Kapinawá');

  INSERT INTO lingua_indigena_educacenso VALUES ('342','Karapoto/ Karapotó');

  INSERT INTO lingua_indigena_educacenso VALUES ('343','Karijo');

  INSERT INTO lingua_indigena_educacenso VALUES ('344','Kariri/ Karirí');

  INSERT INTO lingua_indigena_educacenso VALUES ('345','Kariri - Xocó/ Karirí-Xocó');

  INSERT INTO lingua_indigena_educacenso VALUES ('346','Kaxixó');

  INSERT INTO lingua_indigena_educacenso VALUES ('347','Kayuisiana -(Kaixána)');

  INSERT INTO lingua_indigena_educacenso VALUES ('348','Kiriri');

  INSERT INTO lingua_indigena_educacenso VALUES ('349','Kueskue');

  INSERT INTO lingua_indigena_educacenso VALUES ('350','Manao/ Manáo');

  INSERT INTO lingua_indigena_educacenso VALUES ('351','Maragua');

  INSERT INTO lingua_indigena_educacenso VALUES ('352','Maytapu');

  INSERT INTO lingua_indigena_educacenso VALUES ('353','Mucurim');

  INSERT INTO lingua_indigena_educacenso VALUES ('354','Nawa/ Náwa');

  INSERT INTO lingua_indigena_educacenso VALUES ('355','Paiaku');

  INSERT INTO lingua_indigena_educacenso VALUES ('356','Pankará');

  INSERT INTO lingua_indigena_educacenso VALUES ('357','Pankararé');

  INSERT INTO lingua_indigena_educacenso VALUES ('358','Pankararú/ Pankarú');

  INSERT INTO lingua_indigena_educacenso VALUES ('359','Pankararú - Kalanko');

  INSERT INTO lingua_indigena_educacenso VALUES ('360','Pankararú - Karuazu');

  INSERT INTO lingua_indigena_educacenso VALUES ('361','Pankaru');

  INSERT INTO lingua_indigena_educacenso VALUES ('362','Patxôhã/ Patxoha');

  INSERT INTO lingua_indigena_educacenso VALUES ('363','Paumelenho');

  INSERT INTO lingua_indigena_educacenso VALUES ('364','Piri-Piri/ Pirí-Pirí');

  INSERT INTO lingua_indigena_educacenso VALUES ('365','Pitaguari/ Pitaguarí');

  INSERT INTO lingua_indigena_educacenso VALUES ('366','Potiguara/ Potiguára');

  INSERT INTO lingua_indigena_educacenso VALUES ('367','Puri/ Purí');

  INSERT INTO lingua_indigena_educacenso VALUES ('368','Sapará/ Sapara');

  INSERT INTO lingua_indigena_educacenso VALUES ('369','Tabajara');

  INSERT INTO lingua_indigena_educacenso VALUES ('370','Tapajós');

  INSERT INTO lingua_indigena_educacenso VALUES ('371','Tapeba');

  INSERT INTO lingua_indigena_educacenso VALUES ('372','Tapiuns/ Tapiun');

  INSERT INTO lingua_indigena_educacenso VALUES ('373','Tapuía/ Tapúya');

  INSERT INTO lingua_indigena_educacenso VALUES ('374','Tingui Botó/ Tinguí-Botó');

  INSERT INTO lingua_indigena_educacenso VALUES ('375','Tremembé');

  INSERT INTO lingua_indigena_educacenso VALUES ('376','Truká');

  INSERT INTO lingua_indigena_educacenso VALUES ('377','Tumbalalá');

  INSERT INTO lingua_indigena_educacenso VALUES ('378','Tupinambá');

  INSERT INTO lingua_indigena_educacenso VALUES ('379','Tupinambaraná');

  INSERT INTO lingua_indigena_educacenso VALUES ('380','Tupiniquim');

  INSERT INTO lingua_indigena_educacenso VALUES ('381','Tuxá');

  INSERT INTO lingua_indigena_educacenso VALUES ('382','Waira');

  INSERT INTO lingua_indigena_educacenso VALUES ('383','Waiána-Apalaí');

  INSERT INTO lingua_indigena_educacenso VALUES ('384','Wajuju/ Wajujú');

  INSERT INTO lingua_indigena_educacenso VALUES ('385','Wassú (Wasusú)');

  INSERT INTO lingua_indigena_educacenso VALUES ('386','Xocó');

  INSERT INTO lingua_indigena_educacenso VALUES ('387','Xucuru/ Xukurú');

  INSERT INTO lingua_indigena_educacenso VALUES ('388','Xucuru - Kariri/ Xukurú-Karirí');

  INSERT INTO lingua_indigena_educacenso VALUES ('999','Outras Linguas Indigenas');

  
  ALTER TABLE pmieducar.escola ADD COLUMN acesso INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN ref_idpes_gestor INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN cargo_gestor INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN local_funcionamento INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN condicao INTEGER DEFAULT '1';

  ALTER TABLE pmieducar.escola ADD COLUMN codigo_inep_escola_compartilhada INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN decreto_criacao CHARACTER VARYING(50);

  ALTER TABLE pmieducar.escola ADD COLUMN area_terreno_total CHARACTER VARYING(10);

  ALTER TABLE pmieducar.escola ADD COLUMN area_construida CHARACTER VARYING(10);

  ALTER TABLE pmieducar.escola ADD COLUMN area_disponivel CHARACTER VARYING(10);

  ALTER TABLE pmieducar.escola ADD COLUMN num_pavimentos INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN tipo_piso INTEGER;
  
  ALTER TABLE pmieducar.escola ADD COLUMN medidor_energia INTEGER;
  
  ALTER TABLE pmieducar.escola ADD COLUMN agua_consumida INTEGER;
  
  ALTER TABLE pmieducar.escola ADD COLUMN agua_rede_publica INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN agua_poco_artesiano INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN agua_cacimba_cisterna_poco INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN agua_fonte_rio INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN agua_inexistente INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN energia_rede_publica INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN energia_gerador INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN energia_outros INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN energia_inexistente INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN esgoto_rede_publica INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN esgoto_fossa INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN esgoto_inexistente INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN lixo_coleta_periodica INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN lixo_queima INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN lixo_joga_outra_area INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN lixo_recicla INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN lixo_enterra INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN lixo_outros INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_sala_diretoria INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_sala_professores INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_sala_secretaria INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_laboratorio_informatica INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_laboratorio_ciencias INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_sala_aee INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_quadra_coberta INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_quadra_descoberta INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_cozinha INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_biblioteca INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_sala_leitura INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_parque_infantil INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_bercario INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_banheiro_fora INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_banheiro_dentro INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_banheiro_infantil INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_banheiro_deficiente INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_banheiro_chuveiro INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_refeitorio INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_dispensa INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_aumoxarifado INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_auditorio INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_patio_coberto INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_patio_descoberto INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_alojamento_aluno INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_alojamento_professor INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_area_verde INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_lavanderia INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_unidade_climatizada INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_quantidade_ambiente_climatizado INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_nenhuma_relacionada INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_numero_salas_existente INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_numero_salas_utilizadas INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN porte_quadra_descoberta INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN porte_quadra_coberta INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN tipo_cobertura_patio INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN total_funcionario INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN atendimento_aee INTEGER DEFAULT 0;

  ALTER TABLE pmieducar.escola ADD COLUMN atividade_complementar INTEGER DEFAULT 0;

  ALTER TABLE pmieducar.escola ADD COLUMN fundamental_ciclo INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN localizacao_diferenciada INTEGER DEFAULT 7;

  ALTER TABLE pmieducar.escola ADD COLUMN didatico_nao_utiliza INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN didatico_quilombola INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN didatico_indigena INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN educacao_indigena INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN lingua_ministrada INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN espaco_brasil_aprendizado INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN abre_final_semana INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN codigo_lingua_indigena INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN proposta_pedagogica INTEGER;

  ALTER TABLE pmieducar.escola ADD CONSTRAINT escola_codigo_indigena_fk FOREIGN KEY (codigo_lingua_indigena)
  REFERENCES modules.lingua_indigena_educacenso (id) MATCH SIMPLE;

  ALTER TABLE pmieducar.escola ADD CONSTRAINT escola_ref_idpes_gestor_fk FOREIGN KEY (ref_idpes_gestor)
  REFERENCES cadastro.pessoa (idpes) MATCH SIMPLE;

  ALTER TABLE pmieducar.curso ADD COLUMN modalidade_curso INTEGER;

  ALTER TABLE pmieducar.escola ADD COLUMN televisoes SMALLINT;

  ALTER TABLE pmieducar.escola ADD COLUMN videocassetes SMALLINT;

  ALTER TABLE pmieducar.escola ADD COLUMN dvds SMALLINT;

  ALTER TABLE pmieducar.escola ADD COLUMN antenas_parabolicas SMALLINT;

  ALTER TABLE pmieducar.escola ADD COLUMN copiadoras SMALLINT;

  ALTER TABLE pmieducar.escola ADD COLUMN retroprojetores SMALLINT;

  ALTER TABLE pmieducar.escola ADD COLUMN impressoras SMALLINT;

  ALTER TABLE pmieducar.escola ADD COLUMN aparelhos_de_som SMALLINT;

  ALTER TABLE pmieducar.escola ADD COLUMN projetores_digitais SMALLINT;

  ALTER TABLE pmieducar.escola ADD COLUMN faxs SMALLINT;

  ALTER TABLE pmieducar.escola ADD COLUMN maquinas_fotograficas SMALLINT;

  ALTER TABLE pmieducar.escola ADD COLUMN computadores SMALLINT;

  ALTER TABLE pmieducar.escola ADD COLUMN computadores_administrativo SMALLINT;

  ALTER TABLE pmieducar.escola ADD COLUMN computadores_alunos SMALLINT;

  ALTER TABLE pmieducar.escola ADD COLUMN acesso_internet SMALLINT;

  ALTER TABLE pmieducar.escola ADD COLUMN banda_larga SMALLINT;

  -- //@UNDO

  ALTER TABLE pmieducar.escola DROP CONSTRAINT escola_ref_idpes_gestor_fk;

  ALTER TABLE pmieducar.escola DROP COLUMN codigo_inep_escola_compartilhada;

  ALTER TABLE pmieducar.escola DROP COLUMN local_funcionamento;

  ALTER TABLE pmieducar.escola DROP COLUMN acesso;

  ALTER TABLE pmieducar.escola DROP COLUMN ref_idpes_gestor;
  
  ALTER TABLE pmieducar.escola DROP COLUMN cargo_gestor;

  ALTER TABLE pmieducar.escola DROP COLUMN condicao;

  ALTER TABLE pmieducar.escola DROP COLUMN decreto_criacao;

  ALTER TABLE pmieducar.escola DROP COLUMN area_terreno_total;

  ALTER TABLE pmieducar.escola DROP COLUMN area_construida;

  ALTER TABLE pmieducar.escola DROP COLUMN area_disponivel;

  ALTER TABLE pmieducar.escola DROP COLUMN num_pavimentos;

  ALTER TABLE pmieducar.escola DROP COLUMN tipo_piso;
  
  ALTER TABLE pmieducar.escola DROP COLUMN medidor_energia;
  
  ALTER TABLE pmieducar.escola DROP COLUMN agua_consumida;
  
  ALTER TABLE pmieducar.escola DROP COLUMN agua_rede_publica;
  
  ALTER TABLE pmieducar.escola DROP COLUMN agua_poco_artesiano;
  
  ALTER TABLE pmieducar.escola DROP COLUMN agua_cacimba_cisterna_poco;
  
  ALTER TABLE pmieducar.escola DROP COLUMN agua_fonte_rio;
  
  ALTER TABLE pmieducar.escola DROP COLUMN agua_inexistente;

  ALTER TABLE pmieducar.escola DROP COLUMN energia_rede_publica;

  ALTER TABLE pmieducar.escola DROP COLUMN energia_gerador;

  ALTER TABLE pmieducar.escola DROP COLUMN energia_outros;

  ALTER TABLE pmieducar.escola DROP COLUMN energia_inexistente;

  ALTER TABLE pmieducar.escola DROP COLUMN esgoto_rede_publica;

  ALTER TABLE pmieducar.escola DROP COLUMN esgoto_fossa;

  ALTER TABLE pmieducar.escola DROP COLUMN esgoto_inexistente;

  ALTER TABLE pmieducar.escola DROP COLUMN lixo_coleta_periodica;

  ALTER TABLE pmieducar.escola DROP COLUMN lixo_queima;

  ALTER TABLE pmieducar.escola DROP COLUMN lixo_joga_outra_area;

  ALTER TABLE pmieducar.escola DROP COLUMN lixo_recicla;

  ALTER TABLE pmieducar.escola DROP COLUMN lixo_enterra;

  ALTER TABLE pmieducar.escola DROP COLUMN lixo_outros;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_sala_diretoria;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_sala_professores;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_sala_secretaria;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_laboratorio_informatica;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_laboratorio_ciencias;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_sala_aee;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_quadra_coberta;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_quadra_descoberta;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_cozinha;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_biblioteca;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_sala_leitura;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_parque_infantil;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_bercario;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_banheiro_fora;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_banheiro_dentro;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_banheiro_infantil;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_banheiro_deficiente;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_banheiro_chuveiro;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_refeitorio;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_dispensa;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_aumoxarifado;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_auditorio;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_patio_coberto;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_patio_descoberto;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_alojamento_aluno;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_alojamento_professor;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_area_verde;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_lavanderia;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_unidade_climatizada;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_quantidade_ambiente_climatizado;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_nenhuma_relacionada;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_numero_salas_existente;

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_numero_salas_utilizadas;

  ALTER TABLE pmieducar.escola DROP COLUMN porte_quadra_descoberta;

  ALTER TABLE pmieducar.escola DROP COLUMN porte_quadra_coberta;

  ALTER TABLE pmieducar.escola DROP COLUMN tipo_cobertura_patio;
  
  ALTER TABLE pmieducar.escola DROP COLUMN total_funcionario;

  ALTER TABLE pmieducar.escola DROP COLUMN atendimento_aee;

  ALTER TABLE pmieducar.escola DROP COLUMN atividade_complementar;

  ALTER TABLE pmieducar.escola DROP COLUMN fundamental_ciclo;

  ALTER TABLE pmieducar.escola DROP COLUMN localizacao_diferenciada;

  ALTER TABLE pmieducar.escola DROP COLUMN didatico_nao_utiliza;

  ALTER TABLE pmieducar.escola DROP COLUMN didatico_quilombola;

  ALTER TABLE pmieducar.escola DROP COLUMN didatico_indigena;

  ALTER TABLE pmieducar.escola DROP COLUMN educacao_indigena;

  ALTER TABLE pmieducar.escola DROP COLUMN lingua_ministrada;

  ALTER TABLE pmieducar.escola DROP COLUMN espaco_brasil_aprendizado;

  ALTER TABLE pmieducar.escola DROP COLUMN abre_final_semana;

  ALTER TABLE pmieducar.escola DROP COLUMN codigo_lingua_indigena;

  ALTER TABLE pmieducar.escola DROP COLUMN proposta_pedagogica;

  DROP TABLE modules.lingua_indigena_educacenso;

  DROP TABLE modules.etapas_curso_educacenso;

  DROP TABLE modules.etapas_educacenso;

  ALTER TABLE pmieducar.curso DROP COLUMN modalidade_curso;

  ALTER TABLE pmieducar.escola DROP COLUMN televisoes;

  ALTER TABLE pmieducar.escola DROP COLUMN videocassetes;

  ALTER TABLE pmieducar.escola DROP COLUMN dvds;

  ALTER TABLE pmieducar.escola DROP COLUMN antenas_parabolicas;

  ALTER TABLE pmieducar.escola DROP COLUMN copiadoras;

  ALTER TABLE pmieducar.escola DROP COLUMN retroprojetores;

  ALTER TABLE pmieducar.escola DROP COLUMN impressoras;

  ALTER TABLE pmieducar.escola DROP COLUMN aparelhos_de_som;

  ALTER TABLE pmieducar.escola DROP COLUMN projetores_digitais;

  ALTER TABLE pmieducar.escola DROP COLUMN faxs;

  ALTER TABLE pmieducar.escola DROP COLUMN maquinas_fotograficas;

  ALTER TABLE pmieducar.escola DROP COLUMN computadores;

  ALTER TABLE pmieducar.escola DROP COLUMN computadores_administrativo;

  ALTER TABLE pmieducar.escola DROP COLUMN computadores_alunos;

  ALTER TABLE pmieducar.escola DROP COLUMN acesso_internet;

  ALTER TABLE pmieducar.escola DROP COLUMN banda_larga;  

  -- //