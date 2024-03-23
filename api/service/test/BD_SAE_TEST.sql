DROP TABLE IF EXISTS Utilisateur, CategorieFestival, Festival, EquipeOrganisatrice, Spectacle, SpectacleOrganisateur, SpectacleDeFestival, CategorieSpectacle, Intervenant, MetierIntervenant, Scene, Taille, Grij, Jour, CauseSpectacleNonPlace, SpectaclesJour, SpectacleScenes, ClesApi, Favoris;
CREATE TABLE Utilisateur (
                             idUtilisateur INT PRIMARY KEY AUTO_INCREMENT,
                             prenom VARCHAR(30) NOT NULL,
                             nom VARCHAR(35) NOT NULL,
                             mail VARCHAR(50) NOT NULL UNIQUE,
                             login VARCHAR(35) NOT NULL UNIQUE,
                             mdp VARCHAR(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE CategorieFestival (
                                   idCategorie INT(11) NOT NULL AUTO_INCREMENT,
                                   nom VARCHAR(35) NULL,
                                   PRIMARY KEY (idCategorie)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Festival (
                          idFestival INT(11) NOT NULL AUTO_INCREMENT,
                          categorie INT(11) NOT NULL,
                          titre VARCHAR(35) NULL,
                          description VARCHAR(1000) NULL,
                          dateDebut DATE NOT NULL,
                          dateFin DATE NOT NULL,
                          illustration VARCHAR(50) NULL,
                          PRIMARY KEY (idFestival)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE Festival
    ADD FOREIGN KEY (categorie) REFERENCES CategorieFestival(idCategorie);

CREATE TABLE EquipeOrganisatrice (
                                     idUtilisateur INT(11) NOT NULL,
                                     idFestival INT(11) NOT NULL,
                                     responsable BOOLEAN DEFAULT FALSE,
                                     PRIMARY KEY (idUtilisateur, idFestival)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE EquipeOrganisatrice
    ADD FOREIGN KEY (idUtilisateur) REFERENCES Utilisateur(idUtilisateur);
ALTER TABLE EquipeOrganisatrice
    ADD FOREIGN KEY (idFestival) REFERENCES Festival(idFestival);

CREATE TABLE Spectacle (
                           idSpectacle INT(11) NOT NULL AUTO_INCREMENT,
                           titre VARCHAR(50) NOT NULL,
                           description VARCHAR(1000) NULL,
                           duree TIME NOT NULL,
                           illustration VARCHAR(50) NULL,
                           categorie INT(11),
                           tailleSceneRequise INT(11),
                           PRIMARY KEY (idSpectacle)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE SpectacleOrganisateur (
                                       idUtilisateur INT(11) NOT NULL,
                                       idSpectacle INT(11) NOT NULL,
                                       PRIMARY KEY (idUtilisateur, idSpectacle)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE SpectacleOrganisateur
    ADD FOREIGN KEY (idUtilisateur) REFERENCES Utilisateur(idUtilisateur);
ALTER TABLE SpectacleOrganisateur
    ADD FOREIGN KEY (idSpectacle) REFERENCES Spectacle(idSpectacle);

CREATE TABLE SpectacleDeFestival (
                                     idSpectacle INT(11) NOT NULL,
                                     idFestival INT(11) NOT NULL,
                                     PRIMARY KEY (idSpectacle, idFestival)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE SpectacleDeFestival
    ADD FOREIGN KEY (idSpectacle) REFERENCES Spectacle(idSpectacle);
ALTER TABLE SpectacleDeFestival
    ADD FOREIGN KEY (idFestival) REFERENCES Festival(idFestival);

CREATE TABLE CategorieSpectacle (
                                    idCategorie INT(11) NOT NULL AUTO_INCREMENT,
                                    nomCategorie VARCHAR(35) NOT NULL,
                                    PRIMARY KEY (idCategorie)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE Spectacle
    ADD FOREIGN KEY (categorie) REFERENCES CategorieSpectacle(idCategorie);

CREATE TABLE Intervenant (
                             idIntervenant INT(11) NOT NULL AUTO_INCREMENT,
                             idSpectacle INT(11) NOT NULL,
                             nom VARCHAR(35) NOT NULL,
                             prenom VARCHAR(35) NOT NULL,
                             mail VARCHAR(50) NOT NULL,
                             surScene INT(1) NOT NULL,
                             typeIntervenant INT(11) NOT NULL,
                             PRIMARY KEY (idIntervenant)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE MetierIntervenant (
                                   idMetierIntervenant INT(11) NOT NULL AUTO_INCREMENT,
                                   metier VARCHAR(50) NOT NULL,
                                   PRIMARY KEY (idMetierIntervenant)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE Intervenant
    ADD FOREIGN KEY (typeIntervenant) REFERENCES MetierIntervenant(idMetierIntervenant);

CREATE TABLE Scene (
                       idScene INT(11) NOT NULL AUTO_INCREMENT,
                       taille INT(11) NOT NULL,
                       nombreSpectateurs INT(6) NULL,
                       longitude NUMERIC(8,5) NULL,
                       latitude NUMERIC(8,5) NULL,
                       nom VARCHAR(35) NULL,
                       PRIMARY KEY (idScene)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Taille (
                        idTaille INT(11) NOT NULL AUTO_INCREMENT,
                        nom VARCHAR(35) NULL,
                        PRIMARY KEY (idTaille)
);


ALTER TABLE Scene
    ADD FOREIGN KEY (taille) REFERENCES Taille(idTaille);
ALTER TABLE Spectacle
    ADD FOREIGN KEY (tailleSceneRequise) REFERENCES Taille(idTaille);

CREATE TABLE Grij (
                      idGrij INT(11) NOT NULL AUTO_INCREMENT,
                      heureDebut TIME NULL,
                      heureFin TIME NULL,
                      tempsEntreSpectacle TIME NULL,
                      PRIMARY KEY (idGrij),
                      FOREIGN KEY (idGrij) REFERENCES Festival(idFestival)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Jour (
                      idJour INT(11) NOT NULL AUTO_INCREMENT,
                      idGrij INT(11) NOT NULL,
                      dateDuJour DATE NOT NULL,
                      PRIMARY KEY (idJour),
                      FOREIGN KEY (idGrij) REFERENCES Grij(idGrij)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE CauseSpectacleNonPlace (
                                        idCause INT(11) NOT NULL AUTO_INCREMENT,
                                        intitule VARCHAR(35) NULL,
                                        PRIMARY KEY (idCause)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE SpectaclesJour (
                                idFestival INT(11) NOT NULL,
                                idJour INT(11) NULL,
                                idSpectacle INT(11) NOT NULL,
                                ordre INT(3) NOT NULL DEFAULT 0,
                                place TINYINT NOT NULL DEFAULT 0,
                                heureDebut TIME NULL,
                                heureFin TIME NULL,
                                idCauseNonPlace INT(11) NULL,
                                PRIMARY KEY (idFestival, idSpectacle),
                                FOREIGN KEY (idJour) REFERENCES Jour(idJour),
                                FOREIGN KEY (idSpectacle) REFERENCES Spectacle(idSpectacle),
                                FOREIGN KEY (idFestival) REFERENCES Festival(idFestival),
                                FOREIGN KEY (idCauseNonPlace) REFERENCES CauseSpectacleNonPlace(idCause)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE SpectacleScenes (
                                 idFestival INT(11) NOT NULL,
                                 idSpectacle INT(11) NOT NULL,
                                 idScene INT(11) NOT NULL,
                                 PRIMARY KEY (idFestival, idSpectacle, idScene),
                                 FOREIGN KEY (idScene) REFERENCES Scene(idScene),
                                 FOREIGN KEY (idSpectacle) REFERENCES Spectacle(idSpectacle),
                                 FOREIGN KEY (idFestival) REFERENCES Festival(idFestival)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ClesApi (
                         cleApi CHAR(32) NOT NULL,
                         idUtilisateur INT(11) NOT NULL,
                         PRIMARY KEY (idUtilisateur),
                         FOREIGN KEY (idUtilisateur) REFERENCES Utilisateur(idUtilisateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Favoris (
                         idUtilisateur INT(11) NOT NULL,
                         idFestival INT(11) NOT NULL,
                         PRIMARY KEY (idUtilisateur, idFestival),
                         FOREIGN KEY (idUtilisateur) REFERENCES Utilisateur(idUtilisateur),
                         FOREIGN KEY (idFestival) REFERENCES Festival(idFestival)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données insérées
INSERT INTO CategorieFestival (nom)
VALUES
    ('Musique'),
    ('Théatre'),
    ('Cirque'),
    ('Danse'),
    ('Projection de film');

INSERT INTO CategorieSpectacle (nomCategorie)
VALUES
    ('Concert'),
    ('Piece de theatre'),
    ('Cirque'),
    ('Danse'),
    ('Projection de film');


INSERT INTO Taille (nom)
VALUES
    ('Petite'),
    ('Moyenne'),
    ('Grande');

INSERT INTO CauseSpectacleNonPlace (intitule)
VALUES
    ('Durée trop longue'),
    ('Plus de jour disponible'),
    ('Pas de scène adéquate');

INSERT INTO MetierIntervenant (metier)
VALUES
    ('Acteur'),
    ('Danseur'),
    ('Chanteur'),
    ('Régisseur'),
    ('Comédien'),
    ('Maquilleur'),
    ('Habilleur'),
    ('Scènographe'),
    ('Eclairagiste');

INSERT INTO Scene (taille, nom, nombreSpectateurs, longitude, latitude)
VALUES (1, 'scene1', 30, 12.12121, 12.12121),
       (1, 'scene2', 33, 12.12121, 12.12121),
       (2, 'scene3', 120, 12.12121, 12.12121),
       (3, 'scene4', 500, 12.12121, 12.12121),
       (3, 'scene5', 503, 12.12121, 12.12121);

INSERT INTO Festival (categorie, titre, description, dateDebut, dateFin, illustration)
VALUES (1, 'Festival de musique', 'Festival de musique', '2020-06-01', '2020-06-06', 'musique.jpg'),
       (2, 'Festival de théatre', 'Festival de théatre', '2020-06-02', '2020-06-07', 'theatre.jpg'),
       (3, 'Festival de cirque', 'Festival de cirque', '2020-06-03', '2020-06-08', 'cirque.jpg'),
       (4, 'Festival de danse', 'Festival de danse', '2020-06-04', '2020-06-09', 'danse.jpg'),
       (5, 'Festival de projection de film', 'Festival de projection de film', '2020-06-05', '2020-06-10', 'film.jpg');

INSERT INTO Utilisateur (prenom, nom, mail, login, mdp)
VALUES ('Jean', 'Dupont', 'jean.dupont@gmail.com', 'jean', 'jean'),
       ('Pierre', 'Durand', 'pierre.durand@gmail.com', 'pierre', 'pierre'),
       ('Paul', 'Martin', 'paul.martin@gmail.com', 'paul', 'paul'),
       ('Jacques', 'Petit', 'jacques.petit@gmail.com', 'jacques', 'jacques');

INSERT INTO EquipeOrganisatrice (idUtilisateur, idFestival, responsable)
VALUES (1, 1, TRUE),
       (2, 2, TRUE),
       (3, 3, TRUE),
       (4, 4, TRUE),
       (1, 5, TRUE),
       (2, 5, FALSE),
       (3, 5, FALSE),
       (4, 5, FALSE);

INSERT INTO Favoris (idUtilisateur, idFestival)
VALUES (1, 1),
       (1, 4),
       (1, 5),
       (2, 2),
       (2, 5),
       (3, 3),
       (3, 5),
       (4, 4),
       (4, 5);