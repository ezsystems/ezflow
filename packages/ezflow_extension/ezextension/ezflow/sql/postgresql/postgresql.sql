CREATE TABLE ezm_block (
    id CHAR(32) NOT NULL,
    zone_id CHAR(32) NOT NULL,
    name VARCHAR(255) NULL,
    node_id INTEGER NOT NULL,
    overflow_id CHAR(32) NULL,
    last_update INTEGER NULL DEFAULT 0,
    block_type VARCHAR(255) NULL,
    fetch_params TEXT NULL,
    rotation_type INTEGER NULL,
    rotation_interval INTEGER NULL,
    is_removed INTEGER NULL DEFAULT 0
);

CREATE INDEX ezm_block__is_removed ON ezm_block USING btree (is_removed);
CREATE INDEX ezm_block__node_id ON ezm_block USING btree (node_id);

ALTER TABLE ONLY ezm_block ADD CONSTRAINT ezm_block_pkey PRIMARY KEY (id);

CREATE TABLE ezm_pool (
    block_id CHAR(32) NOT NULL,
    object_id INTEGER NOT NULL,
    node_id INTEGER NOT NULL,
    priority INTEGER NULL DEFAULT 0,
    ts_publication INTEGER NULL DEFAULT 0,
    ts_visible INTEGER NULL DEFAULT 0,
    ts_hidden INTEGER NULL DEFAULT 0,
    rotation_until INTEGER NULL DEFAULT 0,
    moved_to CHAR(32) NULL
);

CREATE INDEX ezm_pool__block_id__ts_publication__priority ON ezm_pool USING btree (block_id,ts_publication,priority);
CREATE INDEX ezm_pool__block_id__ts_visible ON ezm_pool USING btree (block_id,ts_visible);
CREATE INDEX ezm_pool__block_id__ts_hidden ON ezm_pool USING btree (block_id,ts_hidden);


ALTER TABLE ONLY ezm_pool ADD CONSTRAINT ezm_pool_pkey PRIMARY KEY (block_id, object_id);
