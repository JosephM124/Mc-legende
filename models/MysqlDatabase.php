<?php
namespace Models;

class MysqlDatabase extends Database
{
    public function __construct(\Config\Config $config)
    {
        parent::__construct($config);
    }

    /**
     * Méthodes spécifiques à MySQL
     */
    
    /**
     * Récupérer la version de MySQL
     */
    public function getVersion()
    {
        try {
            $result = $this->select("SELECT VERSION() as version");
            return $result[0]['version'] ?? 'Unknown';
        } catch(\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Récupérer les informations sur les bases de données
     */
    public function getDatabases()
    {
        try {
            return $this->select("SHOW DATABASES");
        } catch(\Exception $e) {
            return [];
        }
    }

    /**
     * Récupérer les informations sur les tables
     */
    public function getTables()
    {
        try {
            return $this->select("SHOW TABLES");
        } catch(\Exception $e) {
            return [];
        }
    }

    /**
     * Récupérer les informations détaillées sur une table
     */
    public function getTableInfo($table)
    {
        try {
            return $this->select("SHOW TABLE STATUS WHERE Name = ?", [$table]);
        } catch(\Exception $e) {
            return [];
        }
    }

    /**
     * Optimiser une table
     */
    public function optimizeTable($table)
    {
        try {
            return $this->query("OPTIMIZE TABLE $table");
        } catch(\Exception $e) {
            return false;
        }
    }

    /**
     * Réparer une table
     */
    public function repairTable($table)
    {
        try {
            return $this->query("REPAIR TABLE $table");
        } catch(\Exception $e) {
            return false;
        }
    }

    /**
     * Vérifier une table
     */
    public function checkTable($table)
    {
        try {
            return $this->query("CHECK TABLE $table");
        } catch(\Exception $e) {
            return false;
        }
    }

    /**
     * Récupérer les processus actifs
     */
    public function getProcesses()
    {
        try {
            return $this->select("SHOW PROCESSLIST");
        } catch(\Exception $e) {
            return [];
        }
    }

    /**
     * Tuer un processus
     */
    public function killProcess($processId)
    {
        try {
            return $this->query("KILL $processId");
        } catch(\Exception $e) {
            return false;
        }
    }

    /**
     * Récupérer les variables système
     */
    public function getVariables()
    {
        try {
            return $this->select("SHOW VARIABLES");
        } catch(\Exception $e) {
            return [];
        }
    }

    /**
     * Récupérer les statuts système
     */
    public function getStatus()
    {
        try {
            return $this->select("SHOW STATUS");
        } catch(\Exception $e) {
            return [];
        }
    }

    /**
     * Créer une sauvegarde de la base de données
     */
    public function backup($tables = [])
    {
        try {
            $backup = [];
            
            if (empty($tables)) {
                $tables = $this->getTables();
                $tables = array_column($tables, 'Tables_in_' . $this->dbname);
            }

            foreach ($tables as $table) {
                // Structure de la table
                $createTable = $this->select("SHOW CREATE TABLE $table");
                $backup[$table]['structure'] = $createTable[0]['Create Table'];

                // Données de la table
                $data = $this->select("SELECT * FROM $table");
                $backup[$table]['data'] = $data;
            }

            return $backup;
        } catch(\Exception $e) {
            throw new \Exception("Backup error: " . $e->getMessage());
        }
    }

    /**
     * Restaurer une sauvegarde
     */
    public function restore($backup)
    {
        try {
            $this->beginTransaction();

            foreach ($backup as $table => $data) {
                // Supprimer la table si elle existe
                $this->query("DROP TABLE IF EXISTS $table");

                // Recréer la table
                $this->query($data['structure']);

                // Insérer les données
                if (!empty($data['data'])) {
                    foreach ($data['data'] as $row) {
                        $columns = implode(', ', array_keys($row));
                        $values = implode(', ', array_fill(0, count($row), '?'));
                        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
                        $this->prepare($sql, array_values($row));
                    }
                }
            }

            $this->commit();
            return true;
        } catch(\Exception $e) {
            $this->rollback();
            throw new \Exception("Restore error: " . $e->getMessage());
        }
    }

    /**
     * Exporter une table en CSV
     */
    public function exportToCSV($table, $filename = null)
    {
        try {
            $data = $this->select("SELECT * FROM $table");
            
            if (empty($data)) {
                return false;
            }

            if (!$filename) {
                $filename = $table . '_' . date('Y-m-d_H-i-s') . '.csv';
            }

            $file = fopen($filename, 'w');
            
            // En-têtes
            fputcsv($file, array_keys($data[0]));
            
            // Données
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
            return $filename;
        } catch(\Exception $e) {
            throw new \Exception("Export error: " . $e->getMessage());
        }
    }

    /**
     * Importer des données depuis un fichier CSV
     */
    public function importFromCSV($table, $filename, $columns = [])
    {
        try {
            if (!file_exists($filename)) {
                throw new \Exception("File not found: $filename");
            }

            $this->beginTransaction();

            $file = fopen($filename, 'r');
            $headers = fgetcsv($file);

            if (empty($columns)) {
                $columns = $headers;
            }

            $inserted = 0;
            while (($data = fgetcsv($file)) !== FALSE) {
                $row = array_combine($headers, $data);
                $filteredRow = array_intersect_key($row, array_flip($columns));
                
                $columnNames = implode(', ', array_keys($filteredRow));
                $placeholders = implode(', ', array_fill(0, count($filteredRow), '?'));
                
                $sql = "INSERT INTO $table ($columnNames) VALUES ($placeholders)";
                $this->prepare($sql, array_values($filteredRow));
                $inserted++;
            }

            fclose($file);
            $this->commit();
            
            return $inserted;
        } catch(\Exception $e) {
            $this->rollback();
            throw new \Exception("Import error: " . $e->getMessage());
        }
    }
}
?>