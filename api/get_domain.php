<?php

/**
 * 
 * 获取最新域名
 */
function get_domain()
{
    stream_context_set_default([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ]);
    // 初始化JSON文件
    if (!file_exists('domain_info.json')) {
        $domain_info = [
            'update_at' => 0,
            'domain' => 'https://web.dxj.mobi/'
        ];
        file_put_contents('domain_info.json', json_encode($domain_info));
    } else {
        $domain_info = json_decode(file_get_contents('domain_info.json'), true);
    }
    $update_at = $domain_info['update_at'];
    // 判断是否过期 1天更新一次
    if (time() - $update_at > 24 * 60 * 60) {
        // $headers = get_headers($domain_info['domain']);
        $headers = get_headers('https://web.dxj.mobi/');
        $domain_info['update_at'] = time();
        $flag = false;
        foreach ($headers as $item) {
            preg_match('/location.*?:.*?(http.*)/i', $item, $matches);
            if (isset($matches[1])) {
                $domain_info['domain'] = $matches[1];
                $flag = true;
                break;
            }
        }
        if (!$flag) {
            return 'error';
        }
        file_put_contents('domain_info.json', json_encode($domain_info));
    }
    return $domain_info['domain'];
}
if (isset($_GET['run']) && $_GET['run'] == 'true') {
    echo get_domain();
}
