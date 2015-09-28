-- Cheesto table schema
ALTER TABLE `dan_cheesto` DROP COLUMN `fullname`;

-- Log table schema
ALTER TABLE `dan_log`
    CHANGE `user_id`
    `user_id`
    int(11)
    NULL;

-- Comment table schema
ALTER TABLE `dan_comment`
    CHANGE `user_id`
    `user_id`
    int(11)
    NULL;

-- Foreign key constraints

-- User table
-- Change username field type so it can be enforced to be unique
ALTER TABLE  `dan_user`
    CHANGE  `username`
    `username` VARCHAR(255)
    CHARACTER SET utf8
    COLLATE utf8_general_ci
    NOT NULL;

ALTER TABLE  `dan_user` ADD UNIQUE (
    `username`
);

ALTER TABLE `dan_user`
    ADD INDEX `fk_user_to_group` (`group_id`)
    COMMENT '';

ALTER TABLE `dan_user`
    ADD CONSTRAINT `fk_user_to_group_ct`
    FOREIGN KEY (`group_id`)
    REFERENCES `dan_group`(`id`)
    ON DELETE RESTRICT
    ON UPDATE NO ACTION;

-- Logs table
ALTER TABLE `dan_log`
    ADD INDEX `fk_log_to_user` (`user_id`)
    COMMENT '';

ALTER TABLE `dan_log`
    ADD CONSTRAINT `fk_log_to_user_ct`
    FOREIGN KEY (`user_id`)
    REFERENCES `dan_user`(`id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION;

-- API keys table
ALTER TABLE `dan_apikey`
    ADD INDEX  `fk_key_to_user` (`user_id`)
    COMMENT  '';

ALTER TABLE `dan_apikey`
    ADD CONSTRAINT `fk_key_to_user_ct`
    FOREIGN KEY (`user_id`)
    REFERENCES `dan_user`(`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION;

-- Cheesto table
ALTER TABLE `dan_cheesto`
    ADD INDEX `fk_status_to_user` (`user_id`)
    COMMENT '';

ALTER TABLE `dan_cheesto`
    ADD CONSTRAINT `fk_status_to_user_ct`
    FOREIGN KEY (`user_id`)
    REFERENCES `dan_user`(`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION;

-- Comments table
ALTER TABLE `dan_comment`
    ADD INDEX `fk_comment_to_user` (`user_id`)
    COMMENT '';

ALTER TABLE `dan_comment`
    ADD INDEX `fk_comment_to_log` (`log_id`)
    COMMENT '';

ALTER TABLE `dan_comment`
    ADD CONSTRAINT `fk_comment_to_user_ct`
    FOREIGN KEY (`user_id`)
    REFERENCES `dan_user`(`id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION;

ALTER TABLE `dan_comment`
    ADD CONSTRAINT `fk_comment_to_log_ct`
    FOREIGN KEY (`log_id`)
    REFERENCES `dan_log`(`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION;
