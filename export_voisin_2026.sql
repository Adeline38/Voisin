-- =============================================================================
-- SCRIPT DE DONNÉES DE TEST - APPLICATION "VOISIN." (EXAMEN 2026)
-- =============================================================================

-- -----------------------------------------------------------------------------
-- 1. VIDAGE DES TABLES EXISTANTES (Version sécurisée par DELETE)
-- -----------------------------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM `like`;
DELETE FROM `commentaire`;
DELETE FROM `autorisation_ami`;
DELETE FROM `demande_ami`;
DELETE FROM `publication`;
DELETE FROM `utilisateur`;
SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------------------------------
-- 2. INSERTION DES UTILISATEURS (Avec password, est_en_ligne et roles JSON)
-- -----------------------------------------------------------------------------
INSERT INTO `utilisateur` (`id`, `email`, `pseudo`, `password`, `photo`, `biographie`, `date_inscription`, `est_en_ligne`, `roles`) VALUES
(1, 'adeline@voisin.fr', 'Adeline_38', '$2y$13$Vn3RzN64n7oQpE2.95Z8xOm1U.aA4K/i5u9l1mOUp5rRkXjQ1LJy2', 'adeline.jpg', 'Développeuse web passionnée et habitante du quartier depuis 2 ans.', '2026-09-01 10:00:00', 1, '["ROLE_USER"]'),
(2, 'thomas@voisin.fr', 'Thomas_Voisin', '$2y$13$Vn3RzN64n7oQpE2.95Z8xOm1U.aA4K/i5u9l1mOUp5rRkXjQ1LJy2', 'sarah.jpg', 'Amateur de jardinage partagé et de vide-greniers.', '2026-09-01 11:15:00', 1, '["ROLE_USER"]'),
(3, 'sarah@voisin.fr', 'Sarah_Green', '$2y$13$Vn3RzN64n7oQpE2.95Z8xOm1U.aA4K/i5u9l1mOUp5rRkXjQ1LJy2', 'sarah.jpg', 'Toujours prête pour prêter des outils ou donner un coup de main !', '2026-09-02 09:30:00', 0, '["ROLE_USER"]'),
(4, 'lucas@voisin.fr', 'Lucas_Boulange', '$2y$13$Vn3RzN64n7oQpE2.95Z8xOm1U.aA4K/i5u9l1mOUp5rRkXjQ1LJy2', 'sarah.jpg', 'Le boulanger du coin. Ici pour partager les invendus et les bons plans.', '2026-09-03 14:00:00', 1, '["ROLE_USER"]'),
(5, 'emma@voisin.fr', 'Emma_New', '$2y$13$Vn3RzN64n7oQpE2.95Z8xOm1U.aA4K/i5u9l1mOUp5rRkXjQ1LJy2', 'sarah.jpg', 'Nouvelle arrivée dans la résidence. Hâte de faire votre connaissance !', '2026-09-08 15:00:00', 0, '["ROLE_USER"]');

-- -----------------------------------------------------------------------------
-- 3. INSERTION DES PUBLICATIONS (Public et Privé)
-- -----------------------------------------------------------------------------
INSERT INTO `publication` (`id`, `contenu`, `photo`, `visibilite`, `date_creation`, `date_modification`, `utilisateur_id`) VALUES
(1, 'Bonjour à tous ! Ravi de rejoindre le réseau Voisin. Quelqu''un aurait une perceuse à me prêter pour ce week-end ?', NULL, 'public', '2026-09-02 14:00:00', NULL, 1),
(2, 'Avis aux gourmands : il me reste 5 baguettes et 3 croissants suite à la fournée de ce midi. A venir chercher avant la fermeture !', 'croissants.jpg', 'public', '2026-09-03 18:30:00', NULL, 4),
(3, 'Superbe fête des voisins hier soir dans le parc ! Merci à tous pour votre bonne humeur.', 'fete.jpg', 'public', '2026-09-07 10:00:00', NULL, 2),
(4, 'Est-ce que quelqu''un a perdu un chat roux vers la rue des Fleurs ? Il a un collier bleu.', 'fete.jpg', 'public', '2026-09-08 09:00:00', NULL, 3),
(5, '[Réservé aux Amis] Barbecue improvisé chez moi ce soir à partir de 19h. Amenez vos grillades, je m''occupe des boissons !', 'fete.jpg', 'prive', '2026-09-08 11:00:00', NULL, 1),
(6, '[Réservé aux Amis] Je pars en vacances une semaine. Les clés sont chez Adeline si besoin d''arroser les plantes.', 'fete.jpg', 'prive', '2026-09-08 12:00:00', NULL, 2),
(7, '[Réservé aux Amis] Quelqu''un pour m''aider à porter un canapé demain après-midi ? Ça prendra 10 minutes.', 'fete.jpg', 'prive', '2026-09-08 13:15:00', NULL, 3),
(8, '[Réservé aux Amis] Session code sur le balcon. Le projet Symfony avance bien !', 'fete.jpg', 'prive', '2026-09-08 16:00:00', NULL, 1);

-- -----------------------------------------------------------------------------
-- 4. INSERTION DES DEMANDES D'AMI
-- -----------------------------------------------------------------------------
INSERT INTO `demande_ami` (`id`, `utilisateur_demandeur_id`, `utilisateur_receveur_id`, `statut`, `date_creation`) VALUES
(1, 5, 1, 'pending', '2026-09-08 17:00:00'),
(2, 4, 2, 'rejected', '2026-09-05 11:00:00');

-- -----------------------------------------------------------------------------
-- 5. INSERTION DES AUTORISATIONS D'AMI
-- -----------------------------------------------------------------------------
INSERT INTO `autorisation_ami` (`id`, `utilisateur_id`, `utilisateur_ami_id`, `date_autorisation`) VALUES
(1, 1, 2, '2026-09-02 10:00:00'),
(2, 2, 1, '2026-09-02 10:00:00'),
(3, 1, 3, '2026-09-02 18:00:00'),
(4, 3, 1, '2026-09-02 18:00:00'),
(5, 2, 4, '2026-09-04 09:00:00'),
(6, 4, 2, '2026-09-04 09:00:00');

-- -----------------------------------------------------------------------------
-- 6. INSERTION DES COMMENTAIRES (Bonus)
-- -----------------------------------------------------------------------------
INSERT INTO `commentaire` (`id`, `contenu`, `date_creation`, `utilisateur_id`, `publication_id`) VALUES
(1, 'J''en ai une à disposition si tu veux Adeline, passe quand tu veux !', '2026-09-02 14:30:00', 3, 1),
(2, 'Présent ! J''amène une salade de tomates du jardin.', '2026-09-08 11:15:00', 2, 5);

-- -----------------------------------------------------------------------------
-- 7. INSERTION DES LIKES (Bonus)
-- -----------------------------------------------------------------------------
INSERT INTO `like` (`id`, `utilisateur_id`, `publication_id`) VALUES
(1, 2, 1),
(2, 3, 1),
(3, 1, 2);
