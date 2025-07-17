

<?php
//외부 api 호출
class External_api
{
    private $public_data_base_url = "https://api.odcloud.kr/api";
    private $binzo_base_url = "https://bizno.net/api/fapi";


    /**
     * 사업자 등록 번호 상태 조회
     * https://www.data.go.kr/iim/api/selectAPIAcountView.do#/%EC%82%AC%EC%97%85%EC%9E%90%EB%93%B1%EB%A1%9D%EC%A0%95%EB%B3%B4%20%EC%A7%84%EC%9C%84%ED%99%95%EC%9D%B8%20API/validate
     * @param mixed $company_number
     * @return bool
     */
    //공공 데이터의 api 상태만 조회
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

            $http_code = isset($response['status']) ? $response['status'] : 0;
            $data = isset($response['body']) ? $response['body'] :  null;

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
                log_message('error', "API 호출 실패 [HTTP CODE: {$http_code}] 응답: $data");
                throw new Exception("API 호출 실패 [HTTP CODE: {$http_code}] 응답: {$data}");
            }
        } catch (Exception $e) {
            log_message('error', '사업자번호 조회 중 예외 발생: ' . $e->getMessage());
            return false;
        }
    }
    //비즈노 api 1일 200건 요청 , 300건 출력
    /**
     * https://api.bizno.net/fapi 참고
     * @param mixed $company_number
     * @return bool
     */
    public function bizno_find_company_name($company_number)
    {
        try {
            $CI = &get_instance();
            $CI->load->helper('env_helper');

            $api_key = env('BIZNO_COMPANY_NUMBER_API');

            $request_url = $this->binzo_base_url;
            $params = [
                'key' => $api_key,
                'gb' => '1',
                //1:사업자등록번호검색 / 2:법인등록번호검색 / 3:상호명검색 / 값이 없을 경우 1+2+3검색 상호명 검색시 최대출력갯수 300
                'q' => $company_number,
                'status' => 'Y',
                'type' => 'json'
            ];


            $response = $this->get_request($request_url, $params);
            log_message('debug', 'CURL 응답: ' . var_export($response, true));

            $data = json_decode($response['body'], true);

            //결과가 하나만 나오도록 하는 로직 resulutCode가 0이 아니면 api 사이트 에러코드 참고
            if (isset($data['resultCode']) && $data['resultCode'] == 0) {
                if (isset($data['totalCount']) && $data['totalCount'] == 1 && isset($data['items'][0])) {
                    $item = $data['items'][0];

                    $company = isset($item['company']) ? $item['company'] : null;
                    $bsttcd = isset($item['bsttcd']) ? $item['bsttcd'] : null;

                    log_message('debug', "사업자 상태: $bsttcd (company_number: $company_number) (company: $company)");

                    //
                    if ($bsttcd == "01") { // 계속 사업자 일 경우 
                        return $company;
                    } else {
                        return false;
                    }
                } else {
                    log_message('debug', "결과 없음 또는 파싱 실패");
                    throw new Exception("결과 없음 또는 파싱 실패");
                }
            } else {
                log_message('error', "API 호출 실패 [resultCode: {$data['resultCode']}] 응답: " . json_encode($data));
                throw new Exception("API 호출 실패 [resultCode: {$data['resultCode']}] 응답: " . json_encode($data));
            }
        } catch (Exception $e) {
            log_message('error', 'bizno 사업자번호 조회 중 예외 발생: ' . $e->getMessage());
            return false;
        }
    }



    //post 요청 보내는거
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
            log_message('error', 'POST API 호출 중 예외 발생: ' . $exception->getMessage());
            return [
                'status' => 0,
                'body' => null,
                'error' => $exception->getMessage()
            ];
        }
    }

    //get 요청 보내는거 
    private function get_request($url, $params = [])
    {
        try {
            // 파라미터가 있다면 URL에 쿼리스트링 추가
            if (!empty($params)) {
                $query = http_build_query($params);
                $url .= (strpos($url, '?') === false ? '?' : '&') . $query;
            }

            $ch = curl_init();

            if ($ch === false) {
                throw new Exception("curl_init 실패");
            }

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            log_message('error', 'curl error: ' . curl_error($ch));
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return [
                'status' => $http_code,
                'body' => $response,
            ];
        } catch (Exception $exception) {
            log_message('error', 'GET API 호출 중 예외 발생: ' . $exception->getMessage());
            return [
                'status' => 0,
                'body' => null,
                'error' => $exception->getMessage()
            ];
        }
    }
}
