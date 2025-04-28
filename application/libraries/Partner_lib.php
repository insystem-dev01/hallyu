<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Partner_lib {
    protected $ci;
    public function __construct(){
        $this->ci =& get_instance();
        $this->ci->load->model('Query_model');
        // Do something with $params
    }
    // 가맹점 정보를 리턴
    function get_partner($mb_id, $fileds='*'){
        #return sql_fetch("select $fileds from shop_partner where mb_id = TRIM('$mb_id')");    
        $sql="select {$fileds} from shop_partner where mb_id = TRIM(?)";
        $data=array($mb_id);    
        return $this->ci->Query_model->returnOneArr($sql,$data);
    }
    // 가맹점 정산요청 정보를 리턴
    function get_partner_payrun($index_no, $fileds='*'){
        #return sql_fetch("select $fileds from shop_partner_payrun where index_no = TRIM('$index_no')");
        if($fileds=="*"){
            $sql="select * from shop_partner_payrun where index_no = TRIM(?)";
            $data=array($index_no);
        }else{
            $sql="select ? from shop_partner_payrun where index_no = TRIM(?)";
            $data=array($fileds,$index_no);
        }
        return $this->ci->Query_model->returnOneArr($sql,$data);
    }
    // 가맹점 관리비연장 정보를 리턴
    function get_partner_term($index_no, $fileds='*'){
        #return sql_fetch("select $fileds from shop_partner_term where index_no = TRIM('$index_no')");
        $sql="select {$fileds} from shop_partner_term where index_no = TRIM(?)";
        $data=array($index_no);
        return $this->ci->Query_model->returnOneArr($sql,$data);
    }
    // 가맹점 등급별 설정값
    function get_partner_basic($gb_no, $fields='*'){
        // $sql = " select $fields from shop_member_grade where gb_no = '$gb_no' ";
        // return sql_fetch($sql);
        $sql = " select {$fields} from shop_member_grade where gb_no = ? ";
        $data=array($gb_no);
        return $this->ci->Query_model->returnOneArr($sql,$data);
    }
    // 사용수수료 입력
    function insert_use_pay($mb_id, $pay, $pp_id=''){
        $pay1 = abs($pay);
        // $sql = " select pp_id, pp_pay, pp_use_pay
        //         from shop_partner_pay
        //         where mb_id = '$mb_id'
        //             and pp_id <> '$pp_id'
        //             and pp_pay > pp_use_pay
        //         order by pp_id asc ";
        // $result = sql_query($sql);
        $sql = " select pp_id, pp_pay, pp_use_pay
                from shop_partner_pay
                where mb_id = ?
                    and pp_id <> ?
                    and pp_pay > pp_use_pay
                order by pp_id asc ";
        $data=array($mb_id,$pp_id);
        $result=$this->ci->Query_model->returnArr($sql,$data);
        foreach($result as $key => $row){
            $pay2 = $row['pp_pay'];
            $pay3 = $row['pp_use_pay'];
            if(($pay2 - $pay3) > $pay1) {
                // $sql = " update shop_partner_pay
                //             set pp_use_pay = pp_use_pay + '$pay1'
                //         where pp_id = '{$row['pp_id']}' ";
                // sql_query($sql);
                $sql = " update shop_partner_pay
                            set pp_use_pay = pp_use_pay + ?
                        where pp_id = ?";
                $data=array($pay1,$row['pp_id']);
                $this->ci->Query_model->returnNull($sql,$data);
                break;
            } else {
                $pay4 = $pay2 - $pay3;
                // $sql = " update shop_partner_pay
                //             set pp_use_pay = pp_use_pay + '$pay4'
                //         where pp_id = '{$row['pp_id']}' ";
                // sql_query($sql);
                $sql = " update shop_partner_pay
                            set pp_use_pay = pp_use_pay + ?
                        where pp_id = ?";
                $data=array($pay4,$row['pp_id']);
                $this->ci->Query_model->returnNull($sql,$data);
                $pay1 -= $pay4;
            }
        }
        /*for($i=0; $row=sql_fetch_array($result); $i++) {
            $pay2 = $row['pp_pay'];
            $pay3 = $row['pp_use_pay'];

            if(($pay2 - $pay3) > $pay1) {
                $sql = " update shop_partner_pay
                            set pp_use_pay = pp_use_pay + '$pay1'
                        where pp_id = '{$row['pp_id']}' ";
                sql_query($sql);
                break;
            } else {
                $pay4 = $pay2 - $pay3;
                $sql = " update shop_partner_pay
                            set pp_use_pay = pp_use_pay + '$pay4'
                        where pp_id = '{$row['pp_id']}' ";
                sql_query($sql);
                $pay1 -= $pay4;
            }
        }*/
    }
    // 사용수수료 삭제
    function delete_use_pay($mb_id, $pay){
        $pay1 = abs($pay);
        // $sql = " select pp_id, pp_use_pay
        //         from shop_partner_pay
        //         where mb_id = '$mb_id'
        //             and pp_use_pay > 0
        //         order by pp_id desc ";
        // $result = sql_query($sql);
        $sql = " select pp_id, pp_use_pay
                from shop_partner_pay
                where mb_id = ?
                    and pp_use_pay > 0
                order by pp_id desc ";
        $data=array($mb_id);
        $result=$this->ci->Query_model->returnArr($sql,$data);
        foreach($result as $key => $row){
            $pay2 = $row['pp_use_pay'];

            if($pay2 > $pay1) {
                // $sql = " update shop_partner_pay
                //             set pp_use_pay = pp_use_pay - '$pay1'
                //         where pp_id = '{$row['pp_id']}' ";
                // sql_query($sql);
                $sql = " update shop_partner_pay
                            set pp_use_pay = pp_use_pay - ?
                        where pp_id = ?";
                $data=array($pay1,$row['pp_id']);
                $this->ci->Query_model->returnNull($sql,$data);
                break;
            } else {
                // $sql = " update shop_partner_pay
                //             set pp_use_pay = '0'
                //         where pp_id = '{$row['pp_id']}' ";
                // sql_query($sql);
                $sql = " update shop_partner_pay
                            set pp_use_pay = '0'
                        where pp_id = ?";
                $data=array($row['pp_id']);
                $this->ci->Query_model->returnNull($sql,$data);
                $pay1 -= $pay2;
            }
        }
        /*
        for($i=0; $row=sql_fetch_array($result); $i++) {
            $pay2 = $row['pp_use_pay'];

            if($pay2 > $pay1) {
                $sql = " update shop_partner_pay
                            set pp_use_pay = pp_use_pay - '$pay1'
                        where pp_id = '{$row['pp_id']}' ";
                sql_query($sql);
                break;
            } else {
                $sql = " update shop_partner_pay
                            set pp_use_pay = '0'
                        where pp_id = '{$row['pp_id']}' ";
                sql_query($sql);

                $pay1 -= $pay2;
            }
        }*/
    }
    // 수수료합
    function get_pay_sum($mb_id){
        // $sql = " select sum(pp_pay) as sum_pay
        //         from shop_partner_pay
        //         where mb_id = '$mb_id' ";
        // $row = sql_fetch($sql);
        $sql = " select sum(pp_pay) as sum_pay
                from shop_partner_pay
                where mb_id = ?";
        $data=array($mb_id);
        $row=$this->ci->Query_model->returnOneArr($sql,$data);
        return (int)$row['sum_pay'];
    }
    // 유형별 수수료합
    function get_pay_status($mb_id, $rel_table, $select_add=''){
        // $sql = " select count(*) as cnt,
        //                 sum(pp_pay) as pay
        //         from shop_partner_pay
        //         where mb_id = '$mb_id'
        //             and pp_rel_table = '$rel_table'
        //             $select_add ";
        // $row = sql_fetch($sql);
        $sql = " select count(*) as cnt,
                        sum(pp_pay) as pay
                from shop_partner_pay
                where mb_id = ?
                    and pp_rel_table = ? {$select_add}";
        $data=array($mb_id,$rel_table);
        $row=$this->ci->Query_model->returnOneArr($sql,$data);
        $info = array();
        $info['cnt'] = (int)$row['cnt'];
        $info['pay'] = (int)$row['pay'];

        return $info;
    }
    // 수수료합 (총적립액, 총지급액)
    function get_pay_sheet($mb_id){
        $sql_where = " where mb_id = '$mb_id' ";

        // $sql1 = " select sum(pp_pay) as pay from shop_partner_pay {$sql_where} and pp_pay > 0 ";
        // $row1 = sql_fetch($sql1);
        $sql1 = " select sum(pp_pay) as pay from shop_partner_pay {$sql_where} and pp_pay > 0 ";
        $row1=$this->ci->Query_model->returnOneArr($sql1);

        // $sql2 = " select sum(pp_pay) as pay from shop_partner_pay {$sql_where} and pp_pay < 0 ";
        // $row2 = sql_fetch($sql2);
        $sql2 = " select sum(pp_pay) as pay from shop_partner_pay {$sql_where} and pp_pay < 0 ";
        $row2=$this->ci->Query_model->returnOneArr($sql2);
        
        $info = array();
        $info['pay'] = (int)$row1['pay'];
        $info['usepay'] = (int)$row2['pay'];

        return $info;
    }
}
?>