<?php
/*
 * Quick and dirty script to update deprecated link format. That is, replace
 * [[link label]] with [[link | label]].
 *
 * For more information, see https://github.com/wikkawik/WikkaWiki/issues/1207.
 *
 * USAGE
 *  php scripts/migrateDeprecatedLinks.php
 */
class LinkMigrator {
    public function __construct($config) {
        $this->pages_updated = 0;
        $this->links_migrated = 0;
        $this->updated = 0;
        $this->config = $config;
        $this->db = $this->connect_to_database();
    }

    public function run() {
        $this->backup_database($this->config);
        $pages = $this->load_pages();

        foreach ( $pages as $page ) {
            $page['body'] = $this->replace_old_style_links($page);
            if($this->updated == 1) {
                $this->updated = 0;
                $this->update_page($page);
            }
        }

        $this->report_results();
    }

    protected function backup_database($config) {
        $db_archive_file = sprintf('%s-backup.%s.sql', $config['mysql_database'], date('Ymd'));
        $mysqldump_f = 'mysqldump %s --user=%s --password=%s --single-transaction > /tmp/%s';

        # Is exec the way to go here?
        $result = exec(sprintf($mysqldump_f,
                               $config['mysql_database'],
                               $config['mysql_user'],
                               $config['mysql_password'],
                               $db_archive_file));

        if ( ! $result ) {
            printf("Database %s backed up to /tmp/%s\n", $config['mysql_database'], $db_archive_file);
        }
        else {
            printf("Database backup failed: %s\n", $result);
        }
    }

    protected function load_pages() {
        $sql = sprintf('SELECT id, tag, body, owner, user FROM %spages where latest="Y"',
                       $this->config['table_prefix']);
        $statement = $this->db->query($sql);
        return $statement->fetchAll();
    }

    protected function replace_old_style_links($page) {
        $page_body = $page['body'];
        $links_found = preg_match_all("/\[\[[^\[]*?\]\]/msu", $page_body, $matches);

        if ( $links_found ) {
            $links = $matches[0];

            foreach ( $links as $link ) {
                # TODO: There is probably a better way to do this.
                # For regex, see https://github.com/wikkawik/WikkaWiki/commit/3abc0d9935.
                if ( preg_match("/^(.*?)\s+([^|]+)$/su", $link, $delink) ) {
                    $url = $delink[1];
                    $label = $delink[2];

                    # Skip if already has pipe
                    if ( strpos($url, '|') !== false ) {
                        $new_link = sprintf('%s %s', $url, $label);
                    }
                    else {
                        $new_link = sprintf('%s | %s', $url, $label);
                        $this->links_migrated += 1;
                        $this->updated = 1;
                        echo $delink[1]." ".$delink[2] ." -> ".  $new_link." on page ".$page['tag']."<br/>\n";
                    }

                    #echo join(' -> ', array($link, $new_link)), PHP_EOL;
                    $page_body = str_replace($link, $new_link, $page_body);
                }
            }
        }

        return $page_body;
    }

    protected function update_page($page) {
        # Save new page version.
        $insert = 'INSERT INTO %spages (tag, body, owner, user, note, latest, time) ' .
                  'VALUES (?, ?, ?, ?, ?, "Y", NOW())';
        $sql = sprintf($insert, $this->config['table_prefix']);
        $note = 'Replaces old-style internal links with new pipe-split links.';
        $params = array($page['tag'], $page['body'], $page['owner'], $page['user'], $note);

        $query = $this->db->prepare($sql);
        $query->execute($params);

        # Update last page version.
        $update = 'UPDATE %spages SET latest="N" WHERE id = ?';
        $sql = sprintf($update, $this->config['table_prefix']);
        $query = $this->db->prepare($sql);
        $query->execute(array($page['id']));

        $this->pages_updated += 1;
    }

    protected function report_results() {
        printf("Migrated %d links in %d page records\n", $this->links_migrated, $this->pages_updated);
    }

    protected function connect_to_database() {
        $dsn = sprintf('mysql:host=%s;dbname=%s;',
                       $this->config['mysql_host'],
                       $this->config['mysql_database']);
        $db = new PDO($dsn, $this->config['mysql_user'], $this->config['mysql_password']);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    }
}

include_once('wikka.config.php');
$migrator = new LinkMigrator($wakkaConfig);
$migrator->run();
