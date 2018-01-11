CREATE TABLE IF NOT EXISTS van_setting (
    identifier TINYTEXT NOT NULL,
    val TEXT
);

INSERT INTO van_setting (identifier, val) VALUES
('theme', 'delft'),
('path', '/srv/http/Syracuse'),
('url', 'http://localhost/Syracuse');