<!-- 토스 결제 시작 { -->
<div id="sod_fin">
    <section id="sod_fin_list">
        <h2>주문하실 상품</h2>
        <ul id="sod_list_inq" class="sod_list">
            <?= $ulList ?>
        </ul>

        <dl id="sod_bsk_tot">
            <dt class="sod_bsk_dvr"><span>주문총액</span></dt>
            <dd class="sod_bsk_dvr"><strong><?= $totPrice ?></strong></dd>
            <!-- <?= $couponTag ?> -->
            <!-- <?= $usePointTag ?> -->
            <?= $baesongTag ?>
            <dt class="sod_bsk_cnt"><span>총계</span></dt>
            <dd class="sod_bsk_cnt"><strong><?= $stotalUsePrice ?></strong></dd>
            <!-- <dt class="sod_bsk_point"><span>포인트적립</span></dt>
      <dd class="sod_bsk_point"><strong><?= $stotalPrice ?></strong></dd> -->
        </dl>
    </section>

    <!-- ★ 이니시스 전용 폼/히든필드 제거 -->
    <!-- ★ 주문 정보 임시저장에 쓰는 폼은 그대로 유지 -->
    <form id="forderform" name="forderform" method="post" action="#" autocomplete="off">


        <section id="sod_fin_orderer">
            <h3 class="anc_tit">주문하시는 분</h3>
            <div class="odf_tbl">
                <table>
                    <colgroup>
                        <col class="w70">
                        <col>
                    </colgroup>
                    <tbody>
                        <tr>
                            <th scope="row">이 름</th>
                            <td><?= $od_name ?></td>
                        </tr>
                        <tr>
                            <th scope="row">연락처</th>
                            <td><?= $od_cel ?></td>
                        </tr>
                        <tr>
                            <th scope="row">주 소</th>
                            <td><?= $od_add ?></td>
                        </tr>
                        <tr>
                            <th scope="row">E-mail</th>
                            <td><?= $od_email ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section id="sod_fin_receiver">
            <h3 class="anc_tit">받으시는 분</h3>
            <div class="odf_tbl">
                <table>
                    <colgroup>
                        <col class="w70">
                        <col>
                    </colgroup>
                    <tbody>
                        <tr>
                            <th scope="row">이 름</th>
                            <td><?= $od_bname ?></td>
                        </tr>
                        <tr>
                            <th scope="row">연락처</th>
                            <td><?= $od_bcel ?></td>
                        </tr>
                        <tr>
                            <th scope="row">주 소</th>
                            <td><?= $od_baddr ?></td>
                        </tr>
                        <?= $od_memo ?>
                    </tbody>
                </table>
            </div>
        </section>

        <div id="show_progress" style="display:none;">
            <img src="<?= base_url(); ?>mb/img/loading.gif" alt="">
            <span>주문완료 중입니다. 잠시만 기다려 주십시오.</span>
        </div>

        <!-- 결제 버튼 -->
        <div id="display_pay_button" class="btn_confirm">
            <?php
            // 결제대행사별 코드 include (결제대행사 정보 필드)
            echo $orderform2;
            ?>
            <!-- 결제수단 라디오/셀렉트에서 선택한 값을 f.od_settle_case로 그대로 사용한다는 가정 -->
            <!-- <input type="hidden" name="od_settle_case" value="<?= $od_method ?>">페이지에 맞게 바인딩 -->
            <input type="hidden" name="good_mny" value="<?= (int)$tot_price ?>">
            <input type="hidden" name="od_name" value="<?= htmlspecialchars($od_name, ENT_QUOTES, 'UTF-8') ?>">
            <!-- <input type="hidden" name="od_hp" value="<?= preg_replace('/[^0-9]/', '', $od_cel) ?>"> -->
            <input type="hidden" name="od_email" value="<?= htmlspecialchars($od_email, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="od_addr1" value="<?= htmlspecialchars($od_add, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="od_zip" value=""><!-- 필요시 채움 -->

            <button type="button" class="btn_medium wset" onclick="forderform_check(document.forderform)">결제하기</button>
            <a href="<?= base_url('m/main') ?>" class="btn_medium bx-white">취소</a>
        </div>
    </form>
</div>

<!-- 토스 JS SDK -->
<!-- 토스 JS SDK (standard) -->
<script src="https://js.tosspayments.com/v2/standard"></script>
<script>
    // 페이지 공통 값
    var base_url = document.getElementById('base_url') ? document.getElementById('base_url').value : "<?= base_url() ?>";
    var od_id = "<?= $od_id ?>";
    var orderName = "<?= htmlspecialchars($goods, ENT_QUOTES, 'UTF-8') ?>";

    var amount = <?= (int)$tot_price ?>;
    var taxFree = <?= (int)$comm_free_mny ?>;
    var successUrl = "<?= $successUrl ?>"; // 절대 URL
    var failUrl = "<?= $failUrl ?>"; // 절대 URL

    // 토스 초기화
    var tossPayments = TossPayments("<?= $toss_client_key ?>");
    const customerKey = "<?= $user_id ?>";
    const payment = tossPayments.payment({
        customerKey
    });
    const is_tax_flag_use = <?= $is_tax_flag_use ?>;
    async function paybtn(f) {
        var pay_method = f.od_settle_case.value;

        console.log(pay_method);
        console.log('raw:', f.od_hp, f.od_hp?.value);
        if (pay_method == '신용카드') {
            pay_method = 'CARD';
            var method_info = {
                card: {
                    useEscrow: false,
                    flowMode: 'DEFAULT',
                    useCardPoint: false,
                    useAppCardOnly: false,
                }
            };
        } else if (pay_method == '계좌이체') {
            pay_method = 'TRANSFER';
            var method_info = {
                transfer: {
                    cashReceipt: {
                        type: '소득공제',
                    },
                    useEscrow: false,
                }
            };
        }
        if (is_tax_flag_use) {
            var comm_tax_mny = Number(f.comm_tax_mny.value);
            var comm_vat_mny = Number(f.comm_vat_mny.value);
            var comm_free_mny = Number(f.comm_free_mny.value);
        }

        const paymentData = {
            method: pay_method,
            amount: {
                currency: 'KRW',
                value: Number(f.price.value),
            },
            orderId: od_id,
            orderName: orderName,
            successUrl: base_url + 'm/process/toss_success',
            failUrl: base_url + 'm/process/toss_fail?od_id=' +
                encodeURIComponent(od_id),
            customerName: f.od_name.value,
            customerMobilePhone: f.od_hp.value,
            customerEmail: f.buyeremail.value,
            ...method_info,

        };

        if (is_tax_flag_use) {
            paymentData.taxFreeAmount = comm_free_mny;
        }

        // 결제 전 뒤로가기 방지 안내
        history.pushState(null, '', location.href);
        window.addEventListener('popstate', onBackBlock);

        try {
            await payment.requestPayment(paymentData);
        } finally {
            // 결제창에서 돌아오면(성공/실패) 핸들러 해제
            window.removeEventListener('popstate', onBackBlock);
        }
    }


    function forderform_check(f) {
        if (!payment_check(f)) return false;

        // 임시저장 (기존 유지)
        var order_data = $(f).serialize();
        var save_result = "";
        $.ajax({
            type: "POST",
            data: order_data,
            url: base_url + "process/orderdatasaveAjax",
            cache: false,
            async: false,
            success: function(data) {
                save_result = (data || "").trim();
            },
            error: function(a, b, c) {
                console.log(a, b, c);
            }
        });
        if (save_result) {
            alert(save_result);
            return false;
        }

        paybtn(f);
    }

    function onBackBlock() {
        alert('결제 중에는 뒤로가기를 누르지 말아주세요. 취소는 결제창의 버튼으로 해주세요.');
        history.pushState(null, '', location.href);
    }

    function payment_check(f) {
        var tot_price = parseInt(f.good_mny.value, 10);
        if (f.od_settle_case.value === '계좌이체' && tot_price < 150) {
            alert("계좌이체는 150원 이상 결제");
            return false;
        }
        if (f.od_settle_case.value === '신용카드' && tot_price < 1000) {
            alert("신용카드는 1000원 이상 결제");
            return false;
        }
        if (f.od_settle_case.value === '휴대폰' && tot_price < 350) {
            alert("휴대폰은 350원 이상 결제");
            return false;
        }
        return true;
    }
</script>

<!-- } 토스 결제 끝 -->