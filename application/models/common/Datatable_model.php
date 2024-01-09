<?php
//Created By : Deep Basak
//Created On : 10-10-22
//Purpose : Datable Master Model
//Logs :

/*
Employee ID | Version |Date Range |CR ID    |Propose                      
8           |initial  |27.01.22   |8-220127 | Add OR_WHERE                    
*/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Datatable_model extends CI_Model{
    public $table;
    public $search_column;
    public $colum_array;
    public $order_by;
    public $query;
    public $callType;
    public $funcProcName;
    public $procedureParams;
    
    function __construct($table,$colum_array = array(), $search_column = array(), $order_by = array(), $query = array(), $c_Type = 'table', $f_c_name= '', $procParams = array()) {
        // Set table name
        $this->table = $table;
        // Set orderable column fields
        $this->column_order = $colum_array;
        // Set searchable column fields
        $this->column_search = $search_column;
        // Set default order
        $this->order = $order_by;
        // Set Additional Query
        $this->addlquery = $query;
        // Set Type of Call is Calling from Table or Calling From Procedure
        $this->callType = $c_Type;
        //Initializing Procedure Parameters
        $this->procedureParams = $procParams;
        //Initialize Procedure or Function Name
        $this->funcProcName = $f_c_name;
    }
    
    /*
     * Fetch members data from the database
     * @param $_POST filter data based on the posted parameters
     */
    public function getRows($postData){ 
        $this->db->query('SET SESSION sql_mode = ""'); //for group_by session 

        if($this->callType == 'table'){
            $this->_get_datatables_query($postData);
            if (isset($postData['length']) && $postData['length']) {
                if($postData['length'] != -1){
                    $this->db->limit($postData['length'], $postData['start']);
                }
            }
            $query = $this->db->get();
            return $query->result();
        }
        else{
            //Execute Function if Function or Procedure
            $procParam = "'".implode("','",$this->procedureParams)."'";
            $query = $this->db->query("CALL ".$this->funcProcName."(".$procParam.")");
            mysqli_next_result( $this->db->conn_id );
            return $query->result();
        }
        
    }
    
    /*
     * Count all records
     */
    public function countAll(){
        if($this->callType == 'table'){
            $this->db->from($this->table);
            return $this->db->count_all_results();
        }
        else{
            $procParam = "'".implode("','",$this->procedureParams)."'";
            $query = $this->db->query("CALL ".$this->funcProcName."(".$procParam.")");
            mysqli_next_result( $this->db->conn_id );
            return $query->num_rows();
        }
    }
    
    /*
     * Count records based on the filter params
     * @param $_POST filter data based on the posted parameters
     */
    public function countFiltered($postData){
        if($this->callType == 'table'){
            //Call With Table Function
            $this->_get_datatables_query($postData);
            $query = $this->db->get();
            return $query->num_rows();
        }else{
            //Execute Function if Function or Procedure
            $procParam = "'".implode("','",$this->procedureParams)."'";
            $query = $this->db->query("CALL ".$this->funcProcName."(".$procParam.")");
            mysqli_next_result( $this->db->conn_id );
            return $query->num_rows();
        }
        
    }
    
    /*
     * Perform the SQL queries needed for an server-side processing requested
     * @param $_POST filter data based on the posted parameters
     */
    private function _get_datatables_query($postData){
        $this->db->query('SET SESSION sql_mode = "";');
         
        $this->db->from($this->table);
 
        $i = 0;
        // loop searchable columns 
        foreach($this->column_search as $item){
            // if datatable send POST for search
            if($postData['search']['value']){
                // first loop
                if($i===0){
                    // open bracket
                    $this->db->group_start();
                    $this->db->like($item, $postData['search']['value']);
                }else{
                    $this->db->or_like($item, $postData['search']['value']);
                }
                
                // last loop
                if(count($this->column_search) - 1 == $i){
                    // close bracket
                    $this->db->group_end();
                }
            }
            $i++;
        }

        //Add Additional Query if Exists
        if ($this->addlquery != '' && sizeof($this->addlquery) > 0) {
            $queryArray = $this->addlquery;

            //For Select Clause
            if ($queryArray['select'] != '') {
                $this->db->select($queryArray['select']);
            }

            //For Join Clause
            if (sizeof($queryArray['join']) > 0) {
                for ($join=0; $join < sizeof($queryArray['join']) ; $join++) { 
                    $joinParams = $queryArray['join'][$join];
                    $this->db->join($joinParams['ontable'],$joinParams['onParams'],$joinParams['type']);    
                }
            }

            //For Where Clause
            if (is_array($queryArray['where']) && sizeof($queryArray['where']) > 0) {
                $this->db->where($queryArray['where']);
            } elseif($queryArray['where'] != ''){
                $this->db->where($queryArray['where']);
            }

            //For Or_Where Clause   // CR ID-8-220127
            if (isset($queryArray['or_where']) && sizeof($queryArray['or_where']) > 0) {
                $this->db->or_where($queryArray['or_where']);
            }   // CR ID-8-220127

            //For Where_in Clause
            if (sizeof($queryArray['where_in'])) {
                $this->db->where_in($queryArray['where_in']['column_name'], $queryArray['where_in']['parameters']);
            }

            //For Group By
            if ($queryArray['group_by'] != '') {
                $this->db->group_by($queryArray['group_by']);
            }
        }
         
        if(isset($postData['order'])){
            $this->db->order_by($this->column_order[$postData['order']['0']['column']], $postData['order']['0']['dir']);
        }else if(isset($this->order)){
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

}