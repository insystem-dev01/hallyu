<?php 

class Query_model extends CI_Model {
    function __construct() { 
        parent::__construct(); 
    }
    function returnOneArr($sql,$data=null){
        return $this->db->query($sql,$data)->row_array();
    }
    function returnArr($sql,$data=null){
        return $this->db->query($sql,$data)->result_array();
    }
    function returnQueryObj($sql,$data=null){
        return $this->db->query($sql,$data);
    }
    function returnNull($sql,$data=null){
        return $this->db->query($sql,$data);
    }
    function returnInsertId($sql,$data=null){
        $this->db->query($sql,$data);
        return $this->db->insert_id();
    }
    function insertObj($table,$data){
        $this->db->insert($table,$data);
        return $this->db->insert_id();
    }
    function updateObj($table,$data,$where=null){
        if($this->db->update($table,$data,$where)) return true;
        return false;
    }
    function delete($table,$where){
        $this->db->delete($table,$where);
    }
}
?>