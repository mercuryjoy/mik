<?php

use Illuminate\Database\Seeder;
use App\Admin;
use App\Area;
use App\Distributor;
use App\Shop;
use App\User;
use App\SMSLog;
use App\Code;
use App\CodeBatch;
use App\ScanLog;
use App\DrawRule;
use App\StoreItem;
use App\StoreOrder;
use App\Feedback;
use App\FundingPoolLog;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminTableSeeder::class);
        $this->call(AreaTableSeeder::class);
        $this->call(DistributorTableSeeder::class);
        $this->call(ShopTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(SMSTableSeeder::class);
        $this->call(CodeTableSeeder::class);
        $this->call(ScanLogTableSeeder::class);
        $this->call(DrawRuleTableSeeder::class);
        $this->call(StoreItemTableSeeder::class);
        $this->call(StoreOrderTableSeeder::class);
        $this->call(FeedbackTableSeeder::class);
        $this->call(FundingPoolLogTableSeeder::class);
    }
}


class AdminTableSeeder extends Seeder {

    public function run()
    {
        DB::table('admins')->delete();

        Admin::create([
            'name' => 'Cary Yang',
            'email' => 'getcary@gmail.com',
            'password' => bcrypt("123456"),
            'level' => 99,
        ]);
        Admin::create([
            'name' => '超级管理员',
            'email' => 'admin@mikwine.com',
            'password' => bcrypt("123456"),
            'level' => 2,
        ]);
    }
}

class AreaTableSeeder extends Seeder {

    public function run()
    {
        DB::table('areas')->delete();

        $areasJson = File::get(base_path() . "/database/seeds/area.json");
        $areas = json_decode($areasJson, true);
        $titleMap = [];
        foreach ($areas as $area) {
            $titleMap[$area['id']] = $area['name'];
            Area::create(array(
                'id' => $area['id'],
                'name' => $area['name'],
                'parent_id' => isset($area['parent_id']) ? $area['parent_id'] : null,
                'grandparent_id' => isset($area['grandparent_id']) ? $area['grandparent_id'] : null,
                'display' => (isset($area['grandparent_id']) ? $titleMap[$area['grandparent_id']] . ' ' : '').
                    (isset($area['parent_id']) ? $titleMap[$area['parent_id']] . ' ' . $area['name'] : $area['name'])
            ));
        }
    }
}

class DistributorTableSeeder extends Seeder {
    public function run() {
        DB::table("distributors")->delete();

        factory(Distributor::class, 100)
            ->create()
            ->each(function (Distributor $distributor) {
                $distributor->parent_distributor_id = $distributor->id / 10;
                $distributor->area_id = 110105 + $distributor->id % 10;
                $distributor->save();
            });
    }
}

class ShopTableSeeder extends Seeder {
    public function run() {
        DB::table("shops")->delete();

        factory(Shop::class, 100)
            ->create()
            ->each(function (Shop $shop) {
                $shop->distributor_id = $shop->id % 10;
                $shop->area_id = 110105 + $shop->id % 10;
                $shop->save();
            });
    }
}

class UserTableSeeder extends Seeder {
    public function run() {
        DB::table("users")->delete();

        factory(User::class, 100)
            ->create()
            ->each(function (User $user) {
                $user->shop_id = intval($user->id / 2);
                $user->save();
            });
    }
}

class SMSTableSeeder extends Seeder {
    public function run() {
        DB::table("sms_logs")->delete();

        for ($i = 0; $i < 20; $i++) {
            SMSLog::create([
                'telephone' => '18000000001', 'content' => 'Test',
                'type' => 'test', 'status' => 'error',
            ]);
            SMSLog::create([
                'telephone' => '18000000002', 'content' => 'Test',
                'type' => 'test', 'status' => 'sent',
            ]);
            SMSLog::create([
                'telephone' => '18000000003', 'content' => 'Verify code 1235',
                'type' => 'verify_register', 'status' => 'used', 'code' => '1235',
            ]);
            SMSLog::create([
                'telephone' => '18000000004', 'content' => 'Verify code 1237',
                'type' => 'verify_register', 'status' => 'sent', 'code' => '1237',
            ]);
            SMSLog::create([
                'telephone' => '18000000005', 'content' => 'Notify',
                'type' => 'admin_notify', 'status' => 'sent',
            ]);
            SMSLog::create([
                'telephone' => '18000000006', 'content' => 'Others SMS',
                'type' => 'others', 'status' => 'error',
            ]);
        }
    }
}

class CodeTableSeeder extends Seeder {
    public function run()
    {
        DB::table("codes")->delete();

        for ($i = 0; $i < 100; $i++) {
            if ($i % 10 == 0) {
                CodeBatch::create([
                    'name' => '201701-' . intval($i / 10),
                    'count' => 10
                ]);
            }

            Code::create([
                'code' => '0000' . mt_rand(100000, 999999),
                'batch_id' => intval($i / 10 + 1),
                'scan_log_id' => mt_rand(1, 3) > 2 ? 1 : 0,
            ]);
        }
    }
}

class ScanLogTableSeeder extends Seeder {
    public function run()
    {
        DB::table("scan_logs")->delete();

        for ($i = 0; $i < 100; $i++) {
            ScanLog::create([
                'code_id' => $i * 2,
                'user_id' => mt_rand(1, 99),
                'shop_id' => mt_rand(1, 99),
                'luck_id' => mt_rand(1, 100) / 20,
                'money' => mt_rand(1, 2000),
                'point' => 20,
            ]);
        }
    }
}

class DrawRuleTableSeeder extends Seeder {
    public function run()
    {
        DB::table("draw_rules")->delete();

        DrawRule::create(['rule_json' => '[]']);

        for ($i = 0; $i < 5; $i++) {
            DrawRule::create([
                'area_id' => 120100 + $i,
                'rule_json' => '[]'
            ]);
        }

        for ($i = 0; $i < 5; $i++) {
            DrawRule::create([
                'distributor_id' => $i * 8 + 20,
                'rule_json' => '[]'
            ]);
        }

        for ($i = 0; $i < 5; $i++) {
            DrawRule::create([
                'shop_id' => $i * 7 + 36,
                'rule_json' => '[]'
            ]);
        }
    }
}

class StoreItemTableSeeder extends Seeder {
    public function run()
    {
        DB::table("store_items")->delete();

        for ($i = 0; $i < 100; $i++) {
            StoreItem::create([
                'name' => '商品' . ($i + 1),
                'description' => '描述描述',
                'stock' => mt_rand(1, 2000),
                'photo_url' => 'https://gd4.alicdn.com/bao/uploaded/i4/TB1X3_qJFXXXXXCXXXXXXXXXXXX_!!0-item_pic.jpg_400x400.jpg'
            ]);
        }
    }
}

class StoreOrderTableSeeder extends Seeder {
    public function run()
    {
        DB::table('store_orders')->delete();

        for ($i = 0; $i < 100; $i++) {
            StoreOrder::create([
                'item_id' => mt_rand(1, 100),
                'user_id' => mt_rand(1, 100),
                'amount' => mt_rand(1, 10),
                'status' => ['created', 'shipped', 'canceled'][mt_rand(0, 2)],
            ]);
        }
    }
}

class FeedbackTableSeeder extends Seeder {
    public function run()
    {
        DB::table('feedbacks')->delete();

        for ($i = 0; $i < 10; $i++) {
            Feedback::create([
                'user_id' => mt_rand(0, 1) == 0 ? null : mt_rand(1, 100),
                'content' => "内容" . $i,
            ]);
        }
    }
}

class FundingPoolLogTableSeeder extends Seeder {
    public function run()
    {
        DB::table('funding_pool_logs')->delete();

        $balance = 5000000;

        FundingPoolLog::create([
            'type' => 'deposit',
            'amount' => $balance,
            'balance' => $balance,
            'admin_id' => 1,
            'comment' => 'init balance',
        ]);

        for ($i = 0; $i < 100; $i++) {
            $log = factory(FundingPoolLog::class)->make();
            $balance = $balance + $log->amount;
            $log->balance = $balance;
            if ($log->type == 'user_withdraw') {
                $log->user_id = 1;
            } else {
                $log->admin_id = 1;
            }
            $log->save();
        }
    }
}
