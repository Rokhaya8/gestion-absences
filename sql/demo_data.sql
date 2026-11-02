-- Données de démonstration (entièrement fictives)
-- À importer après schema.sql
--
-- Comptes :
--   Administrateur : admin / admin123
--   Étudiants (connexion par numéro Apogée) : 20250001 / etudiant123
--                                             20250002 / etudiant123

USE `gestion_absences`;

INSERT INTO `administrateurs` (`username`, `password_hash`, `nom`, `prenom`, `email`) VALUES
('admin', '$2y$10$nfiNWnpbWYLyrNWBlq5JjORBFS2vMZGtDCQEST72PrP6bfoD462nG', 'Admin', 'Demo', 'admin@demo.ma');

INSERT INTO `filieres` (`id`, `code`, `nom`, `description`) VALUES
(1, 'GI',   'Génie Informatique',             'Développement logiciel et systèmes d''information'),
(2, 'RSSP', 'Réseaux et Systèmes',            'Réseaux, systèmes et sécurité'),
(3, 'GIL',  'Génie Industriel et Logistique', 'Gestion industrielle et logistique');

INSERT INTO `responsables` (`id`, `nom`, `prenom`, `email`, `telephone`) VALUES
(1, 'Idrissi', 'Karim', 'k.idrissi@demo.ma', '0600000001'),
(2, 'Tazi',    'Nadia', 'n.tazi@demo.ma',    '0600000002');

INSERT INTO `modules` (`id`, `code`, `nom`, `semestre`, `filiere_id`, `responsable_id`) VALUES
(1, 'GI-S1-01',   'Algorithmique',        'S1', 1, 1),
(2, 'GI-S1-02',   'Bases de données',     'S1', 1, 1),
(3, 'GI-S2-01',   'Programmation Web',    'S2', 1, 2),
(4, 'RSSP-S1-01', 'Réseaux fondamentaux', 'S1', 2, 2);

INSERT INTO `etudiants` (`id`, `num_apogee`, `nom`, `prenom`, `email`, `password_hash`, `filiere_id`, `photo`) VALUES
(1, '20250001', 'Alaoui',  'Yassine', 'yassine.alaoui@demo.ma', '$2y$10$xvSyrMtmi1h.vqTaHSjXNu4ZMcuYd8PRKB3YdVnwHx7hKfKPC5kg6', 1, NULL),
(2, '20250002', 'Bennani', 'Salma',   'salma.bennani@demo.ma',  '$2y$10$xvSyrMtmi1h.vqTaHSjXNu4ZMcuYd8PRKB3YdVnwHx7hKfKPC5kg6', 1, NULL);

INSERT INTO `etudiant_modules` (`etudiant_id`, `module_id`, `semestre`) VALUES
(1, 1, 'S1'), (1, 2, 'S1'), (1, 3, 'S2'),
(2, 1, 'S1'), (2, 2, 'S1'), (2, 3, 'S2');
