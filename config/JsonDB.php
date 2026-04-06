<?php
/**
 * =============================================================================
 * CLOUD 9 CAFE - JSON DATABASE CLASS
 * =============================================================================
 * 
 * ROLE: This class provides a simple JSON file-based database system.
 *       It replaces MySQL for this demo project, storing data in .json files.
 * 
 * FEATURES:
 * - CRUD operations (Create, Read, Update, Delete)
 * - Auto-increment IDs
 * - Timestamp management (created_at, updated_at)
 * - Search functionality
 * - Aggregation (count, sum)
 * 
 * DATA STORAGE:
 * - Each table is a separate JSON file in the data directory
 * - Files are named: {table_name}.json
 * - Data is stored as array of objects in JSON format
 * 
 * USAGE:
 *   $db = new JsonDB('/path/to/data/');
 *   $id = $db->insert('users', ['name' => 'John']);
 *   $user = $db->selectOne('users', ['id' => 1]);
 */

class JsonDB {
    
    // =============================================================================
    // SECTION: Class Properties
    // DESCRIPTION: Storage for data directory and loaded tables
    // =============================================================================
    
    private $dataDir;       // Path to directory containing JSON files
    private $tables = [];   // Cache for loaded table data
    
    // =============================================================================
    // END SECTION: Class Properties
    // =============================================================================
    
    // =============================================================================
    // SECTION: Constructor
    // DESCRIPTION: Initialize JsonDB with data directory path
    // =============================================================================
    
    /**
     * Constructor - Initialize JsonDB
     * 
     * FUNCTION: __construct()
     * PARAMETER: $dataDir (string) - Path to directory for JSON files
     * EXAMPLE: new JsonDB(__DIR__ . '/../data/');
     */
    public function __construct($dataDir) {
        $this->dataDir = $dataDir;
    }
    // =============================================================================
    // END SECTION: Constructor
    // =============================================================================
    
    // =============================================================================
    // SECTION: Private Helper Methods
    // DESCRIPTION: Internal methods for file operations
    // =============================================================================
    
    /**
     * Get file path for a table
     * 
     * FUNCTION: getTableFile()
     * PARAMETER: $table (string) - Table name
     * RETURNS: (string) - Full path to JSON file
     * EXAMPLE: getTableFile('users') → '/data/users.json'
     */
    private function getTableFile($table) {
        return $this->dataDir . $table . '.json';
    }
    
    /**
     * Load table data from JSON file
     * 
     * FUNCTION: loadTable()
     * PARAMETER: $table (string) - Table name to load
     * DESCRIPTION: Loads table data into memory cache, creates empty table if not exists
     */
    private function loadTable($table) {
        // Check if already loaded (caching)
        if (isset($this->tables[$table])) return;
        
        $file = $this->getTableFile($table);
        
        // Check if file exists
        if (file_exists($file)) {
            // Load and decode JSON data
            // FUNCTION: json_decode() - Converts JSON string to PHP array
            // true = return associative array instead of object
            $this->tables[$table] = json_decode(file_get_contents($file), true) ?: [];
        } else {
            // Create empty table if file doesn't exist
            $this->tables[$table] = [];
        }
    }
    
    /**
     * Save table data to JSON file
     * 
     * FUNCTION: saveTable()
     * PARAMETER: $table (string) - Table name to save
     * DESCRIPTION: Writes cached table data back to JSON file
     */
    private function saveTable($table) {
        $file = $this->getTableFile($table);
        
        // Encode data to JSON with pretty printing
        // FUNCTION: json_encode() - Converts PHP array to JSON string
        // JSON_PRETTY_PRINT = format with indentation for readability
        $json = json_encode($this->tables[$table], JSON_PRETTY_PRINT);
        
        // Write to file
        // FUNCTION: file_put_contents() - Write string to file
        file_put_contents($file, $json);
    }
    
    /**
     * Get next auto-increment ID for table
     * 
     * FUNCTION: getNextId()
     * PARAMETER: $table (string) - Table name
     * RETURNS: (int) - Next available ID
     * DESCRIPTION: Finds highest ID in table and returns next number
     */
    private function getNextId($table) {
        $this->loadTable($table);
        
        // If table is empty, start with ID 1
        if (empty($this->tables[$table])) return 1;
        
        // Get all IDs
        $ids = array_column($this->tables[$table], 'id');
        
        // Return max ID + 1
        return max($ids) + 1;
    }
    // =============================================================================
    // END SECTION: Private Helper Methods
    // =============================================================================
    
    // =============================================================================
    // SECTION: CRUD Operations - Create
    // DESCRIPTION: Insert new records into tables
    // =============================================================================
    
    /**
     * Insert a new record into table
     * 
     * FUNCTION: insert()
     * PARAMETERS:
     *   - $table (string) - Table name
     *   - $data (array) - Associative array of field => value
     * RETURNS: (int) - ID of newly created record
     * 
     * AUTO-ADDED FIELDS:
     *   - id: Auto-increment ID
     *   - created_at: Current timestamp
     *   - updated_at: Current timestamp
     * 
     * EXAMPLE:
     *   $db->insert('users', ['name' => 'John', 'email' => 'john@example.com']);
     */
    public function insert($table, $data) {
        $this->loadTable($table);
        
        // Add auto-increment ID
        $data['id'] = $this->getNextId($table);
        
        // Add timestamps
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        // Add to table array
        $this->tables[$table][] = $data;
        
        // Save to file
        $this->saveTable($table);
        
        return $data['id'];
    }
    // =============================================================================
    // END SECTION: CRUD Operations - Create
    // =============================================================================
    
    // =============================================================================
    // SECTION: CRUD Operations - Read
    // DESCRIPTION: Select/query records from tables
    // =============================================================================
    
    /**
     * Select records matching conditions
     * 
     * FUNCTION: select()
     * PARAMETERS:
     *   - $table (string) - Table name
     *   - $where (array) - Conditions as field => value pairs (optional)
     *   - $orderBy (array) - Sort as field => direction (optional)
     *   - $limit (int) - Maximum records to return (optional)
     * RETURNS: (array) - Array of matching records
     * 
     * EXAMPLES:
     *   $db->select('users');  // Get all users
     *   $db->select('users', ['status' => 'Active']);  // Get active users
     *   $db->select('users', ['role' => 'Admin'], ['created_at' => 'DESC'], 10);
     */
    public function select($table, $where = [], $orderBy = null, $limit = null) {
        $this->loadTable($table);
        
        // Start with all records
        $results = $this->tables[$table];
        
        // Apply WHERE conditions
        if (!empty($where)) {
            $results = array_filter($results, function($row) use ($where) {
                foreach ($where as $key => $value) {
                    if (!isset($row[$key]) || $row[$key] != $value) {
                        return false;
                    }
                }
                return true;
            });
            // Re-index array after filtering
            $results = array_values($results);
        }
        
        // Apply ORDER BY
        if ($orderBy) {
            $field = key($orderBy);
            $direction = $orderBy[$field];
            
            usort($results, function($a, $b) use ($field, $direction) {
                if (!isset($a[$field]) || !isset($b[$field])) return 0;
                $comparison = $a[$field] <=> $b[$field];
                return $direction === 'DESC' ? -$comparison : $comparison;
            });
        }
        
        // Apply LIMIT
        if ($limit) {
            $results = array_slice($results, 0, $limit);
        }
        
        return $results;
    }
    
    /**
     * Select a single record matching conditions
     * 
     * FUNCTION: selectOne()
     * PARAMETERS:
     *   - $table (string) - Table name
     *   - $where (array) - Conditions as field => value pairs
     * RETURNS: (array|null) - First matching record or null if not found
     * 
     * EXAMPLE:
     *   $user = $db->selectOne('users', ['email' => 'john@example.com']);
     */
    public function selectOne($table, $where) {
        $results = $this->select($table, $where, null, 1);
        return $results[0] ?? null;
    }
    // =============================================================================
    // END SECTION: CRUD Operations - Read
    // =============================================================================
    
    // =============================================================================
    // SECTION: CRUD Operations - Update
    // DESCRIPTION: Update existing records
    // =============================================================================
    
    /**
     * Update records matching conditions
     * 
     * FUNCTION: update()
     * PARAMETERS:
     *   - $table (string) - Table name
     *   - $data (array) - New values as field => value pairs
     *   - $where (array) - Conditions to match records to update
     * RETURNS: (int) - Number of records updated
     * 
     * AUTO-ADDED FIELDS:
     *   - updated_at: Current timestamp
     * 
     * EXAMPLE:
     *   $db->update('users', ['name' => 'Johnny'], ['id' => 1]);
     */
    public function update($table, $data, $where) {
        $this->loadTable($table);
        
        $updated = 0;
        
        // Update matching records
        foreach ($this->tables[$table] as &$row) {
            $match = true;
            
            // Check if row matches all WHERE conditions
            foreach ($where as $key => $value) {
                if (!isset($row[$key]) || $row[$key] != $value) {
                    $match = false;
                    break;
                }
            }
            
            // Update if matched
            if ($match) {
                foreach ($data as $key => $value) {
                    $row[$key] = $value;
                }
                $row['updated_at'] = date('Y-m-d H:i:s');
                $updated++;
            }
        }
        
        // Save if any records were updated
        if ($updated > 0) {
            $this->saveTable($table);
        }
        
        return $updated;
    }
    // =============================================================================
    // END SECTION: CRUD Operations - Update
    // =============================================================================
    
    // =============================================================================
    // SECTION: CRUD Operations - Delete
    // DESCRIPTION: Remove records from tables
    // =============================================================================
    
    /**
     * Delete records matching conditions
     * 
     * FUNCTION: delete()
     * PARAMETERS:
     *   - $table (string) - Table name
     *   - $where (array) - Conditions to match records to delete
     * RETURNS: (int) - Number of records deleted
     * 
     * EXAMPLE:
     *   $db->delete('users', ['id' => 1]);
     */
    public function delete($table, $where) {
        $this->loadTable($table);
        
        $originalCount = count($this->tables[$table]);
        
        // Filter out matching records
        $this->tables[$table] = array_filter($this->tables[$table], function($row) use ($where) {
            foreach ($where as $key => $value) {
                if (isset($row[$key]) && $row[$key] == $value) {
                    return false; // Remove this row
                }
            }
            return true; // Keep this row
        });
        
        // Re-index array
        $this->tables[$table] = array_values($this->tables[$table]);
        
        $deleted = $originalCount - count($this->tables[$table]);
        
        // Save if any records were deleted
        if ($deleted > 0) {
            $this->saveTable($table);
        }
        
        return $deleted;
    }
    // =============================================================================
    // END SECTION: CRUD Operations - Delete
    // =============================================================================
    
    // =============================================================================
    // SECTION: Aggregation Methods
    // DESCRIPTION: Count records and sum values
    // =============================================================================
    
    /**
     * Count records in table
     * 
     * FUNCTION: count()
     * PARAMETERS:
     *   - $table (string) - Table name
     *   - $where (array) - Optional conditions to count only matching records
     * RETURNS: (int) - Number of records
     * 
     * EXAMPLES:
     *   $db->count('users');  // Total users
     *   $db->count('users', ['status' => 'Active']);  // Active users only
     */
    public function count($table, $where = []) {
        $results = $this->select($table, $where);
        return count($results);
    }
    
    /**
     * Sum values of a column
     * 
     * FUNCTION: sum()
     * PARAMETERS:
     *   - $table (string) - Table name
     *   - $column (string) - Column to sum
     *   - $where (array) - Optional conditions
     * RETURNS: (float) - Sum of column values
     * 
     * EXAMPLE:
     *   $total = $db->sum('orders', 'total_amount', ['status' => 'Completed']);
     */
    public function sum($table, $column, $where = []) {
        $results = $this->select($table, $where);
        $sum = 0;
        
        foreach ($results as $row) {
            if (isset($row[$column])) {
                $sum += floatval($row[$column]);
            }
        }
        
        return $sum;
    }
    // =============================================================================
    // END SECTION: Aggregation Methods
    // =============================================================================
}
?>
