<h2 class="pg_tit_join"><?php echo ($type == "user") ? "회원가입" : "입점사 가입"?></h2>

<form name="fregister" id="fregister" onsubmit="return fregister_submit(this);" method="POST" autocomplete="off">
    
    <!-- 회원유형 선택-->
    <section id="frgister_type">
        <div class="frgister_type_in">
            <div class="reigster_type">
                <?php if($type == "user") { ?>
                <div class="type_box">
                    <div class="type_in"> 
                        <!-- 일반회원-->
                        <div class="type_layout">
                            <div class="person type1"></div>
                            <div class="txt">
                                <p>일반회원</p>
                                <p>14세 이상 개인</p>
                            </div>
                            <button type="button" name="memberType" value="일반 회원가입" class="b2c" onclick="fregister_submit(this)">가입하기</button>
                        </div>
                    </div>
                </div>
                <?php } ?>
                
                <?php if($type == "seller") { ?>
                <div class="type_box">
                    <div class="type_in">
                        <!-- 입점사 -->
                        <div class="type_layout">
                            <div class="person type2"></div>
                            <div class="txt">
                                <p>입점사</p>
                                <p>사업자 및 기타 단체</p>
                            </div>
                            <button type="button" name="memberType" value="기업 회원가입" class="b2b" onclick="fregister_submit(this)">가입하기</button>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>
</form>
    
<script>
function fregister_submit(e) {
    var type = $(e).val();
    
    var f = document.forms['fregister'];

    var url = "<?php echo $register_action_url; ?>";
    f.action = url;
    f.submit();
}
</script>