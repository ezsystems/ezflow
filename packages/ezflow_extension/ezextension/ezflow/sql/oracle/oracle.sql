-- note: the DEFAULT '' NOT NULL are OK, since NOT NULL takes pecendence and we do not rely on ezdbschema to remove the default clause

CREATE TABLE ezm_block (
    id CHAR(32) DEFAULT '' NOT NULL,
    zone_id CHAR(32) DEFAULT '' NOT NULL,
    name VARCHAR2(255),
    node_id INTEGER NOT NULL,
    overflow_id CHAR(32),
    last_update INTEGER DEFAULT 0,
    block_type VARCHAR2(255),
    fetch_params CLOB,
    rotation_type INTEGER,
    rotation_interval INTEGER,
    is_removed INTEGER DEFAULT 0
);

CREATE INDEX ezm_block_is_removed ON ezm_block (is_removed);
CREATE INDEX ezm_block_node_id ON ezm_block (node_id);

ALTER TABLE ezm_block ADD CONSTRAINT ezm_block_pkey PRIMARY KEY (id);

CREATE TABLE ezm_pool (
    block_id CHAR(32) DEFAULT '' NOT NULL,
    object_id INTEGER NOT NULL,
    node_id INTEGER NOT NULL,
    priority INTEGER DEFAULT 0,
    ts_publication INTEGER DEFAULT 0,
    ts_visible INTEGER DEFAULT 0,
    ts_hidden INTEGER DEFAULT 0,
    rotation_until INTEGER DEFAULT 0,
    moved_to CHAR(32)
);

CREATE INDEX ezm_pool_block_id_ts_publ_prio ON ezm_pool (block_id,ts_publication,priority);
CREATE INDEX ezm_pool_block_id_ts_visible ON ezm_pool (block_id,ts_visible);
CREATE INDEX ezm_pool_block_id_ts_hidden ON ezm_pool (block_id,ts_hidden);

ALTER TABLE ezm_pool ADD CONSTRAINT ezm_pool_pkey PRIMARY KEY (block_id, object_id);
