CREATE TABLE project (id VARCHAR(36) NOT NULL, path VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, version VARCHAR(255) NOT NULL, UNIQUE INDEX project_path_idx (path), UNIQUE INDEX project_name_version_idx (name, version), UNIQUE INDEX project_id_idx (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

CREATE TABLE project_dependencies (parent VARCHAR(36) NOT NULL, son VARCHAR(36) NOT NULL, INDEX IDX_C0A894EE3D8E604F (parent), INDEX IDX_C0A894EEE199342C (son), PRIMARY KEY(parent, son)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

ALTER TABLE project_dependencies ADD CONSTRAINT FK_C0A894EE3D8E604F FOREIGN KEY (parent) REFERENCES project (id);

ALTER TABLE project_dependencies ADD CONSTRAINT FK_C0A894EEE199342C FOREIGN KEY (son) REFERENCES project (id);
