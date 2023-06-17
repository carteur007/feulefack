<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230616134816 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE candidat (id INT NOT NULL, genre VARCHAR(5) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE candidat_offre_emploi (candidat_id INT NOT NULL, offre_emploi_id INT NOT NULL, INDEX IDX_B1E2339A8D0EB82 (candidat_id), INDEX IDX_B1E2339AB08996ED (offre_emploi_id), PRIMARY KEY(candidat_id, offre_emploi_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE employeur (id INT NOT NULL, nom_entreprise VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE employeur_pack_cv (employeur_id INT NOT NULL, pack_cv_id INT NOT NULL, INDEX IDX_A0287F735D7C53EC (employeur_id), INDEX IDX_A0287F737D109393 (pack_cv_id), PRIMARY KEY(employeur_id, pack_cv_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE employeur_abonnement (employeur_id INT NOT NULL, abonnement_id INT NOT NULL, INDEX IDX_E5E625725D7C53EC (employeur_id), INDEX IDX_E5E62572F1D74413 (abonnement_id), PRIMARY KEY(employeur_id, abonnement_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offre_emploi (id INT AUTO_INCREMENT NOT NULL, categorie_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_132AD0D1BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE candidat ADD CONSTRAINT FK_6AB5B471BF396750 FOREIGN KEY (id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidat_offre_emploi ADD CONSTRAINT FK_B1E2339A8D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidat_offre_emploi ADD CONSTRAINT FK_B1E2339AB08996ED FOREIGN KEY (offre_emploi_id) REFERENCES offre_emploi (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employeur ADD CONSTRAINT FK_8747E1C7BF396750 FOREIGN KEY (id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employeur_pack_cv ADD CONSTRAINT FK_A0287F735D7C53EC FOREIGN KEY (employeur_id) REFERENCES employeur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employeur_pack_cv ADD CONSTRAINT FK_A0287F737D109393 FOREIGN KEY (pack_cv_id) REFERENCES pack_cv (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employeur_abonnement ADD CONSTRAINT FK_E5E625725D7C53EC FOREIGN KEY (employeur_id) REFERENCES employeur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employeur_abonnement ADD CONSTRAINT FK_E5E62572F1D74413 FOREIGN KEY (abonnement_id) REFERENCES abonnement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offre_emploi ADD CONSTRAINT FK_132AD0D1BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE cv ADD CONSTRAINT FK_B66FFE928D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id)');
        $this->addSql('ALTER TABLE formation_candidat ADD CONSTRAINT FK_AD27E3805200282E FOREIGN KEY (formation_id) REFERENCES formation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE formation_candidat ADD CONSTRAINT FK_AD27E3808D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_employeur ADD CONSTRAINT FK_DFD6DB8DED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_employeur ADD CONSTRAINT FK_DFD6DB8D5D7C53EC FOREIGN KEY (employeur_id) REFERENCES employeur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur ADD discr VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cv DROP FOREIGN KEY FK_B66FFE928D0EB82');
        $this->addSql('ALTER TABLE formation_candidat DROP FOREIGN KEY FK_AD27E3808D0EB82');
        $this->addSql('ALTER TABLE service_employeur DROP FOREIGN KEY FK_DFD6DB8D5D7C53EC');
        $this->addSql('ALTER TABLE candidat DROP FOREIGN KEY FK_6AB5B471BF396750');
        $this->addSql('ALTER TABLE candidat_offre_emploi DROP FOREIGN KEY FK_B1E2339A8D0EB82');
        $this->addSql('ALTER TABLE candidat_offre_emploi DROP FOREIGN KEY FK_B1E2339AB08996ED');
        $this->addSql('ALTER TABLE employeur DROP FOREIGN KEY FK_8747E1C7BF396750');
        $this->addSql('ALTER TABLE employeur_pack_cv DROP FOREIGN KEY FK_A0287F735D7C53EC');
        $this->addSql('ALTER TABLE employeur_pack_cv DROP FOREIGN KEY FK_A0287F737D109393');
        $this->addSql('ALTER TABLE employeur_abonnement DROP FOREIGN KEY FK_E5E625725D7C53EC');
        $this->addSql('ALTER TABLE employeur_abonnement DROP FOREIGN KEY FK_E5E62572F1D74413');
        $this->addSql('ALTER TABLE offre_emploi DROP FOREIGN KEY FK_132AD0D1BCF5E72D');
        $this->addSql('DROP TABLE candidat');
        $this->addSql('DROP TABLE candidat_offre_emploi');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE employeur');
        $this->addSql('DROP TABLE employeur_pack_cv');
        $this->addSql('DROP TABLE employeur_abonnement');
        $this->addSql('DROP TABLE offre_emploi');
        $this->addSql('ALTER TABLE utilisateur DROP discr');
        $this->addSql('ALTER TABLE service_employeur DROP FOREIGN KEY FK_DFD6DB8DED5CA9E6');
        $this->addSql('ALTER TABLE formation_candidat DROP FOREIGN KEY FK_AD27E3805200282E');
    }
}
