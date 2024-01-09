<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Auther : Deep Basak
 * IDE    : VS Code
 * Date   : 13/02/2023
 */

class Common_model extends CI_Model
{
    public function add($table, $data)
    {
        try {
            $query = $this->db->insert($table, $data);
            return TRUE;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function add_get_lstId($table, $data)
    {
        try {
            $query = $this->db->insert($table, $data);
            $insert_id = $this->db->insert_id();

            return $insert_id;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function select($table)
    {
        try {
            $query = $this->db->get($table);
            return $query->result();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update_data($id, $table)
    {
        try {
            $this->db->where($id);
            $query = $this->db->get($table);
            return $query->row();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update($id, $data, $table)
    {
        try {
            if (empty($id))
                return FALSE;
            $this->db->update($table, $data, array('id' => $id));
            return TRUE;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function UpdateDB($table, $Where, $Data, $whereinField = '', $where_in_array = '')
    {
        try {
            if ($whereinField != '' && $where_in_array != '') {
                $this->db->where_in($whereinField, $where_in_array);
            }
            if ($Where != '') {
                $this->db->where($Where);
            }

            $Update = $this->db->update($table, $Data);
            if ($Update):
                return true;
            else:
                return false;
            endif;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete($id, $table)
    {
        try {
            $this->db->where($id);
            $this->db->delete($table);
            return TRUE;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getAllData($table, $specific = '', $row = '', $Where = '', $order = '', $limit = '', $groupBy = '', $like = '', $orLike = [], $joins = [], $where_in_array = '', $whereinField = '', $having = '') //CR ID-8-220209
    {
        try {
            // If Condition
            if (!empty($Where)):
                $this->db->where($Where);
            endif;

            if (!empty($where_in_array)) {
                $this->db->where_in($whereinField, $where_in_array);
            }

            // If Specific Columns are require
            if (!empty($specific)):
                $this->db->select($specific);
            else:
                $this->db->select('*');
            endif;

            if (!empty($joins)) {
                for ($counter = 0; $counter < count($joins); $counter++) {
                    $this->db->join($joins[$counter]['table'], $joins[$counter]['on'], $joins[$counter]['type']);
                }
            }

            if (!empty($groupBy)):
                $this->db->group_by($groupBy);
            endif;

            //Having	//CR ID-8-220209
            if (!empty($having)):
                $this->db->having($having);
            endif; //CR ID-8-220209

            // if Order
            if (!empty($order)):
                $this->db->order_by($order);
            endif;

            // if limit
            if (!empty($limit)):
                $this->db->limit($limit);
            endif;

            //if like
            if (!empty($like)):
                $this->db->like($like);
            endif;

            // get Data
            if (!empty($orLike)):
                foreach ($orLike as $key => $lkVal) {
                    if ($key == 0)
                        $this->db->like($lkVal);
                    else
                        $this->db->or_like($lkVal);
                }
            endif;

            //if select row
            if (!empty($row) && $row == 2):
                $GetData = $this->db->get($table);
                return $GetData->result_array();
            elseif (!empty($row)):
                $GetData = $this->db->get($table);
                return $GetData->row();
            else:
                $GetData = $this->db->get($table);
                return $GetData->result();
            endif;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function get_last_id($table)
    {
        try {
            $this->db->order_by('id', 'desc');
            $query = $this->db->get($table);
            $result = $query->row();

            if (!empty($result)) {
                return $result->id;
            } else {
                return 0;
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //Added By Deep
    public function get_num_rows($table, $Where = '', $groupBy = '', $joins = [], $whereinField = '', $where_in_array = '')
    {
        try {
            if (!empty($joins)) {
                for ($counter = 0; $counter < count($joins); $counter++) {
                    $this->db->join($joins[$counter]['table'], $joins[$counter]['on'], $joins[$counter]['type']);
                }
            }

            if (!empty($Where)):
                $this->db->where($Where);
            endif;

            if (!empty($whereinField) && !empty($where_in_array)) {
                $this->db->where_in($whereinField, $where_in_array);
            }

            if (!empty($groupBy)):
                $this->db->group_by($groupBy);
            endif;

            $query = $this->db->get($table);
            return $query->num_rows();
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    
    /*******************Devoloper Suhrid Start 30-03-2023 ***************************/
    /* 
   add by suhrid
    */
    public function get2WhereIn($table_name,$id,$fieldname,$id2,$fieldname2)
    {
        $this->db->select ('*'); 
        $this->db->from($table_name);
        $this->db->where($fieldname, $id);
        $this->db->where($fieldname2, $id2);
        $query = $this->db->get();
        return $query->result();
            
    }
    /*******************Devoloper Suhrid END 30-03-2023 ***************************/
             
    //Added by DEEP BASAK on April 13, 2023
	//For alter table query for dynamic form
	public function alterTableForDynamicForm($table_name, $field_name, $data_type, $is_null, $comments = '', $after = 'id'){
		try{
			$this->db->query("ALTER TABLE ".$table_name." ADD `".$field_name."` ".$data_type." ".$is_null." COMMENT '".$comments."' AFTER `".$after."`;");
		} catch (\Throwable $th) {
            throw $th;
        }
	}
	
	//Added by DEEP BASAK on June 09, 2023
	//Modify by DEEP BASAK on June 11, 2023
    //Modify by DEEP BASAK on July 11, 2023
    //For Call sp
    public function callSP($query = "",$type= "result"){
        try {
            $req = $this->db->query($query);
            mysqli_next_result($this->db->conn_id);
            if ($req->num_rows() > 0) {
                if ($type == 'row') {
                    return $req->row_array();
                }else {
					return $req->result_array();
				}
            } else {
                return 1;   //Added by DEEP BASAK on July 11, 2023
            }
        } catch (\Throwable $th) {
			throw $th;
		}
    }

}
?>
