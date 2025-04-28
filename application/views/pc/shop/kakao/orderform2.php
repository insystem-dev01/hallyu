<input type="hidden" name="od_id" value="<?php echo $od_id; ?>">
<input type="hidden" name="od_settle_case" value="<?php echo $od['paymethod']; ?>">
<input type="hidden" name="od_name" value="<?php echo $od['name']; ?>">
<input type="hidden" name="od_tel" value="<?php echo $od['telephone']; ?>">
<input type="hidden" name="od_hp" value="<?php echo $od['cellphone']; ?>">
<input type="hidden" name="od_zip" value="<?php echo $od['zip']; ?>">
<input type="hidden" name="od_addr1" value="<?php echo $od['addr1']; ?>">
<input type="hidden" name="od_addr2" value="<?php echo $od['addr2']; ?>">
<input type="hidden" name="od_addr3" value="<?php echo $od['addr3']; ?>">
<input type="hidden" name="od_addr_jibeon" value="<?php echo $od['addr_jibeon']; ?>">
<input type="hidden" name="od_email" value="<?php echo $od['email']; ?>">
<input type="hidden" name="od_b_name" value="<?php echo $od['b_name']; ?>">
<input type="hidden" name="od_b_tel" value="<?php echo $od['b_telephone']; ?>">
<input type="hidden" name="od_b_hp" value="<?php echo $od['b_cellphone']; ?>">
<input type="hidden" name="od_b_zip" value="<?php echo $od['b_zip']; ?>">
<input type="hidden" name="od_b_addr1" value="<?php echo $od['b_addr1']; ?>">
<input type="hidden" name="od_b_addr2" value="<?php echo $od['b_addr2']; ?>">
<input type="hidden" name="od_b_addr3" value="<?php echo $od['b_addr3']; ?>">
<input type="hidden" name="od_b_addr_jibeon" value="<?php echo $od['b_addr_jibeon']; ?>">

<input type="hidden" name="good_mny" value="<?php echo $tot_price; ?>">
<?=$taxTag?>
<div id="kakaopay_request">
    <input type="hidden" name="partner_order_id" value="<?php echo $od_id; ?>">
    <input type="hidden" name="partner_user_id" value="<?php echo $od['mb_id']; ?>">
    <input type="hidden" name="item_name" value="<?php echo $goods; ?>">
    <input type="hidden" name="quantity" value="<?php echo $goods_cnt; ?>">
    <input type="hidden" name="total_amount" value="<?php echo $tot_price; ?>">
    <input type="hidden" name="tax_free_amount" value="<?php echo $comm_free_mny; ?>">
    <input type="hidden" name="vat_amount" value="<?php echo $comm_vat_mny; ?>">
    <input type="hidden" name="price">
    <input type="hidden" name="tax">
    <input type="hidden" name="taxfree">
    <input type="hidden" name="buyername">
    <input type="hidden" name="buyeremail">
    <input type="hidden" name="buyertel">
    <input type="hidden" name="recvname">
    <input type="hidden" name="recvtel">
    <input type="hidden" name="recvpostnum">
    <input type="hidden" name="recvaddr">
</div>