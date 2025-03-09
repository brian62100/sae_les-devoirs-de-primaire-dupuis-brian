CREATE DATABASE IF NOT EXISTS projet_sae_maintenance_application;
USE projet_sae_maintenance_application;

CREATE TABLE IF NOT EXISTS eleve (
   user_id INT AUTO_INCREMENT,
   nom VARCHAR(50),
   prenom VARCHAR(50),
   password VARCHAR(255),
   PRIMARY KEY(user_id)
);

CREATE TABLE IF NOT EXISTS question (
   id_question INT AUTO_INCREMENT,
   reponse VARCHAR(50),
   correcte BOOL,
   type_question VARCHAR(50),
   intitule VARCHAR(255),
   PRIMARY KEY(id_question)
);

CREATE TABLE IF NOT EXISTS parents (
   id_parents INT AUTO_INCREMENT,
   nom VARCHAR(50),
   prenom VARCHAR(50),
   password VARCHAR(255),
   PRIMARY KEY(id_parents)
);

CREATE TABLE IF NOT EXISTS professeurs (
   id_prof INT AUTO_INCREMENT,
   nom VARCHAR(50),
   prenom VARCHAR(50),
   password VARCHAR(255),
   PRIMARY KEY(id_prof)
);

CREATE TABLE IF NOT EXISTS faire (
   user_id INT,
   id_question INT,
   PRIMARY KEY(user_id, id_question),
   FOREIGN KEY(user_id) REFERENCES eleve(user_id),
   FOREIGN KEY(id_question) REFERENCES question(id_question)
);

CREATE TABLE IF NOT EXISTS enseignant (
   user_id INT,
   id_prof INT,
   PRIMARY KEY(user_id, id_prof),
   FOREIGN KEY(user_id) REFERENCES eleve(user_id),
   FOREIGN KEY(id_prof) REFERENCES professeurs(id_prof)
);

CREATE TABLE IF NOT EXISTS parenter (
   user_id INT,
   id_parents INT,
   PRIMARY KEY(user_id, id_parents),
   FOREIGN KEY(user_id) REFERENCES eleve(user_id),
   FOREIGN KEY(id_parents) REFERENCES parents(id_parents)
);