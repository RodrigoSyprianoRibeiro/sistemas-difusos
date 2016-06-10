CREATE SCHEMA sistemasdifusos DEFAULT CHARACTER SET utf8 ;

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "-03:00";

CREATE TABLE IF NOT EXISTS unidade_medida (
  id int(5) NOT NULL AUTO_INCREMENT,
  nome varchar(200) NOT NULL,
  sigla varchar(20) NOT NULL
  CONSTRAINT pk_unidade_medida PRIMARY KEY (id)
) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO unidade_medida (nome, sigla) VALUES
('Centímetro','CM'),
('Metro','M'),
('Quilômetro','KM'),
('Quilômetro por Hora','KM/H'),
('Centímetro Quadrado','CM²'),
('Metro Quadrado','M²'),
('Centímetro Cúbico','CM³'),
('Metro Cúbico','M³'),
('Litro','L'),
('Quilograma','KG'),
('Tonelada','T'),
('Hertz','Hz'),
('Whatt','W'),
('Ampére','A'),
('Volt','V'),
('Newton','N'),
('Hora','H'),
('Minuto','Min'),
('Segundo','S'),
('Celsius','Cº'),
('Ângulo','º'),
('Porcentagem','%');

CREATE TABLE IF NOT EXISTS projeto (
  id INT(5) NOT NULL AUTO_INCREMENT,
  nome VARCHAR(200) NOT NULL,
  descricao TEXT(1000) NOT NULL,
  ativo tinyint(1) NOT NULL DEFAULT '1',
  CONSTRAINT pk_projeto PRIMARY KEY (id)
) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS variavel (
  id INT(5) NOT NULL AUTO_INCREMENT,
  id_projeto INT(5) NOT NULL,
  nome VARCHAR(200) NOT NULL,
  inicio_universo DECIMAL(9,2) NOT NULL,
  fim_universo DECIMAL(9,2) NOT NULL,
  id_unidade_medida INT(5) NOT NULL,
  objetiva tinyint(1) NOT NULL DEFAULT '1',
  ativo tinyint(1) NOT NULL DEFAULT '1',
  CONSTRAINT pk_variavel PRIMARY KEY (id),
  CONSTRAINT fk_variavel_projeto FOREIGN KEY (id_projeto) REFERENCES projeto(id),
  CONSTRAINT fk_variavel_unidade_medida FOREIGN KEY (id_unidade_medida) REFERENCES unidade_medida(id)
) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS termo (
  id INT(5) NOT NULL AUTO_INCREMENT,
  id_variavel INT(5) NOT NULL,
  nome VARCHAR(200) NOT NULL,
  inicio_suporte DECIMAL(9,2) NOT NULL,
  fim_suporte DECIMAL(9,2) NOT NULL,
  inicio_nucleo DECIMAL(9,2) NOT NULL,
  fim_nucleo DECIMAL(9,2) NOT NULL,
  ativo tinyint(1) NOT NULL DEFAULT '1',
  CONSTRAINT pk_termo PRIMARY KEY (id),
  CONSTRAINT fk_termo_variavel FOREIGN KEY (id_variavel) REFERENCES variavel(id)
) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS regra (
  id INT(5) NOT NULL AUTO_INCREMENT,
  id_projeto INT(5) NOT NULL,
  operador VARCHAR(4) NULL DEFAULT NULL,
  id_termo_consequente INT(5) NOT NULL,
  CONSTRAINT pk_regra PRIMARY KEY (id),
  CONSTRAINT fk_regra_projeto FOREIGN KEY (id_projeto) REFERENCES projeto(id),
  CONSTRAINT fk_regra_termo FOREIGN KEY (id_termo_consequente) REFERENCES termo(id)
) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS regra_termo_antecedente (
  id_regra INT(5) NOT NULL,
  id_termo_antecedente INT(5) NOT NULL,
  CONSTRAINT pk_regra_termo_antecedente PRIMARY KEY (id_regra, id_termo_antecedente),
  CONSTRAINT fk_regra_termo_antecedente_regra FOREIGN KEY (id_regra) REFERENCES regra(id),
  CONSTRAINT fk_regra_termo_antecedente_termo FOREIGN KEY (id_termo_antecedente) REFERENCES termo(id)
) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;