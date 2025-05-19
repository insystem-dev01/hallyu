

<?php
//외부 api 호출
class External_api
{
    private $public_data_base_url = "https://api.odcloud.kr/api";


    /**
     * 사업자 등록 번호 상태 조회
     * https://www.data.go.kr/iim/api/selectAPIAcountView.do#/%EC%82%AC%EC%97%85%EC%9E%90%EB%93%B1%EB%A1%9D%EC%A0%95%EB%B3%B4%20%EC%A7%84%EC%9C%84%ED%99%95%EC%9D%B8%20API/validate
     * @param mixed $company_number
     * @return bool
     */
    public function find_business_man_number($company_number)
    {

        try {
            $CI = &get_instance();
            $CI->load->helper('env_helper');

            $api_key = env('PUBLIC_DATA_COMPANY_NUMBER_API');

            $endpoint = "/nts-businessman/v1/status?serviceKey=";

            $request_url = $this->public_data_base_url . $endpoint . $api_key;
            $request_data = [
                "b_no" => [$company_number],
            ];

            $response = $this->post_request($request_url, $request_data);

            $http_code = $response['status'] ?? 0;
            $data = $response['body'] ?? null;

            if ($http_code === 200 && $data) {
                $result = json_decode($data, true);

                if (is_array($result) && isset($result['data'][0]['b_stt'])) {
                    $business_status = $result['data'][0]['b_stt'];
                    log_message('debug', "사업자 상태: $business_status (b_no: $company_number)");


                    // 계속사업자, 휴업자, 폐업자,
                    return $business_status === "계속사업자";
                } else {
                    log_message('debug', "결과 없음 또는 파싱 실패");
                    throw new Exception("결과 없음 또는 파싱 실패");
                }
            } else {
                log_message('error', "API 호출 실패 [HTTP CODE: $http_code] 응답: $data");
                throw new Exception("API 호출 실패 [HTTP CODE: $http_code] 응답: $data");
            }
        } catch (Exception $e) {
            log_message('error', '사업자번호 조회 중 예외 발생: ' . $e->getMessage());
            return false;
        }
    }

    private function post_request($url, $data)
    {

        try {
            $ch = curl_init();

            if ($ch === false) {
                throw new Exception("curl_init 실패");
            }

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Accept: application/json'
                ],
                CURLOPT_POSTFIELDS => json_encode($data),
            ]);


            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return [
                'status' => $http_code,
                'body' => $response,
            ];
        } catch (Exception $exception) {
            log_message('error', 'API 호출 중 예외 발생: ' . $exception->getMessage());
            return [
                'status' => 0,
                'body' => null,
                'error' => $exception->getMessage()
            ];
        }
    }
}
