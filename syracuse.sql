CREATE TABLE IF NOT EXISTS van_setting (
    identifier TINYTEXT NOT NULL,
    val TEXT
);

CREATE TABLE IF NOT EXISTS van_language (
    id INT UNSIGNED NOT NULL PRIMARY KEY,
    `name` TINYTEXT NOT NULL,
    native TINYTEXT NOT NULL,
    code CHAR(5) NOT NULL
);

INSERT INTO van_language (id, `name`, native, code) VALUES
(1, 'English (US)', 'English', 'en_US');

INSERT INTO van_setting (identifier, val) VALUES
('theme', 'delft'),
('path', '/srv/http/Syracuse'),
('url', 'http://localhost/Syracuse'),
('language', 1);