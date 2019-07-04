CREATE TOKEN TABLE 
    CREATE TABLE wp_blooptoken (
    bt_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    collection_id bigint(20) UNSIGNED NOT NULL,
    token_generated longtext,
    remarks longtext,
    author bigint(20) UNSIGNED NOT NULL,
    created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (bt_id)
)