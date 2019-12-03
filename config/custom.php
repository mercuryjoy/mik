<?php

return [
    'app_url' => env('APP_URL'),                                                   // 当前域名地址
    'android_apk_path' => env('ANDROID_APK_PATH', 'uploads/packages/'),    // 安卓APP包上传路径
    'app_qr_code_path' => env('APP_QR_CODE_PATH', 'uploads/codes/'),       // APP下载二维码路径

    // API接口APP版本号
    'api_version_2_3' => '2.3',

    // ALIPAY_CONFIG
    'app_id' => '2018012302042945',                                                        // 应用ID
    'notify_url' => 'http://app-dev.mikwine.com/api/goods/orders/notifications',           // 回调通知地址
    //私钥值
    'rsa_private_key' => 'MIIEowIBAAKCAQEAvZMfVqSHDaMixszcdyVEW5IEj4PTFEQjL6QFYzAdWstVNKfFvRNL60AShCSHTFQhbqI5xXNT8pKG7QiTVtg76ES5otZU9o47/Cw6mkXLI7zgCdh2Cn+TkdNy+/oG8VM1rUOyqwTtD3OaXzffvIsEytVptUtNjeSkqu5yVVpxhunco3/peWo+XqYIzgiWz6IMXe2kOnyvb2yJO2r6V3+IIVy/nQxQwGZs1T+4WfU+Rnk/B6XDMfIFOuvmreaqLMaafIxLp9xIiFUsAPDmFhwXZTy+tXGha8AZrd9eGsC9HhZGiRdpaHth2gFV0zaXJUDzSiou6NQqOOJuMwkG5TfXNQIDAQABAoIBAQCxJGO5O34zQQhDCbTM5JbulhAK5tx7aHwUjUi3eYNEjxGsZUVBu2FKjxF+Rv/iVA71aUNgfLapyT+pCEVddirsjCOGxI6Z4SiuJ8oO1D7QwzH6ITmT78g+EH62c9i+8ckLgWHXqn+124kZhoVbke/FdQwU6yup9kfkrXFKznuc2xg7ldiPmnfSZvIB88KI1LfAkfF6pXGIOsh1foHV3N3WYKf9zo4ofyoAoPHa7PXgZ72/wpihLsneIDfySJaeph4x5DrbBw6HUAntuWBlJesISfr51Fe5IySidJtHHySaNT6DnFXaOKG1tDzyqaK3kb54ih4+0MRUa0FIojckhLVhAoGBAOao7+MgZPua1ETbb12F0FCecg8sN9UEoTjopxMB+r4aK/SQ8OHkXGKqZ8ZiNuhV4MJ1Tnft0oPpL0V3uii0xi5DfGlpf/aAYHVKBerJWECFYqohzh4SA1HHNzW9iNGOE1qfQT+4e01qzPTEzpXOIQfCZ5UDXOFnQqSUh7Hj6Vz9AoGBANJmtS3Xe/Fw6nLZdGnw3m9dEgUg1Y0C2UGqdesAuuWHkAt+Yxe2Molq+F/Pm9pWjkkyg3hhnAVHz/WW+LydjPMlyOLJNPbgjcallKtrcGUQUIVa05fT+bKhAMPslwL2b0O/SRY+DgAqrBsIIiEUsFJKUbdRYJSVJQusWB+IAZSZAoGAAc42un9pavLGUJerIn9GpyrmV6oP8dHsdSaVEDgGv3AzAeIUkKAZ/Qe6cjoYle7+KGeEqrmo2TF9Fj0eruzjVD9g/O4/ey18E871huX2k+K3Z/+FvoSDlCNMElaeeI11J1Nxzk7iYDPC0POtbkzDw2zJJMh29Ki7Q9CJ02GmVP0CgYAUiKmN+8XA3oBDDS3rWPKcc7zae0XcKTcCzZwMf1m+JOjN9lu9aK6t8p6i2yQevuvMAP4LtZsAeO22zjEgV0/2Ou3MFE7y+R9dD7PetvGVK25wVVjpLGrmIAhvFpv2Ug6x0e1UGmJLad66FKUgDWDX5yDqfyqp1ZRz+zHugduFoQKBgAdmY3KQY5m+fl0XSjuoAird/+OxqsEz041d2sFGUgObhZgy84RVsnznLH9GdPvHeXTRUgD/pCKpb7okknenNjpp9a/Rh5c8JdwAIdUZGjbT1ucvTFiswYq7Eh6BjZVE92OuB/vNjdCQlcihu31dPPNdzidq5py1rdGcVQRq+3v2',
    //公钥值
    'rsa_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvZMfVqSHDaMixszcdyVEW5IEj4PTFEQjL6QFYzAdWstVNKfFvRNL60AShCSHTFQhbqI5xXNT8pKG7QiTVtg76ES5otZU9o47/Cw6mkXLI7zgCdh2Cn+TkdNy+/oG8VM1rUOyqwTtD3OaXzffvIsEytVptUtNjeSkqu5yVVpxhunco3/peWo+XqYIzgiWz6IMXe2kOnyvb2yJO2r6V3+IIVy/nQxQwGZs1T+4WfU+Rnk/B6XDMfIFOuvmreaqLMaafIxLp9xIiFUsAPDmFhwXZTy+tXGha8AZrd9eGsC9HhZGiRdpaHth2gFV0zaXJUDzSiou6NQqOOJuMwkG5TfXNQIDAQAB',

    // NET_API_URL
    'net_url' => 'https://mkapi.soonku.net/',           // 测试
    // 'net_url' => 'https://wxapi.mikwine.com/',       // 正式
    'checkout_coupon_api_url' => 'IntegralMallWebService.asmx/UpdCouponNewByWriteOff',     // 核销优惠券
    'scan_code_add_category_api_url' => 'IntegralMallWebService.asmx/AddMemberTag',        // 创建标签

    // DB_API_URL
    'db_url' => 'http://bdapi-dev.mikwine.com/',        // 测试
    // 'db_url' => 'http://wxbdapi.mikwine.com/',         // 正式
    'order_notification_api_url' => 'SalesUserWebService.asmx/AddSysContent',        // DB下单和发货通知

    // telephone regex
    'telephone_regex' => '/^1[3456789][0-9]{9}$/',
];
