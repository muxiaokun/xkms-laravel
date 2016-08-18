<?php

use Illuminate\Database\Seeder;

class MessageBoardsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('message_boards')->insert([
            ['name' => 'default', 'config' => 'a:8:{s:6:"姓名";a:4:{s:8:"msg_name";s:6:"姓名";s:8:"msg_type";s:4:"text";s:12:"msg_required";b:1;s:10:"msg_length";s:2:"20";}s:6:"性别";a:5:{s:10:"msg_option";a:3:{i:0;s:3:"男";i:1;s:3:"女";i:2;s:6:"未知";}s:8:"msg_name";s:6:"性别";s:8:"msg_type";s:5:"radio";s:12:"msg_required";b:0;s:10:"msg_length";s:2:"20";}s:6:"爱好";a:5:{s:10:"msg_option";a:3:{i:0;s:5:"test1";i:1;s:5:"test2";i:2;s:5:"test3";}s:8:"msg_name";s:6:"爱好";s:8:"msg_type";s:8:"checkbox";s:12:"msg_required";b:0;s:10:"msg_length";s:2:"20";}s:12:"联系电话";a:4:{s:8:"msg_name";s:12:"联系电话";s:8:"msg_type";s:4:"text";s:12:"msg_required";b:1;s:10:"msg_length";s:2:"20";}s:12:"公司名称";a:4:{s:8:"msg_name";s:12:"公司名称";s:8:"msg_type";s:4:"text";s:12:"msg_required";b:1;s:10:"msg_length";s:2:"20";}s:12:"公司地址";a:4:{s:8:"msg_name";s:12:"公司地址";s:8:"msg_type";s:4:"text";s:12:"msg_required";b:1;s:10:"msg_length";s:2:"20";}s:6:"E-mail";a:4:{s:8:"msg_name";s:6:"E-mail";s:8:"msg_type";s:4:"text";s:12:"msg_required";b:0;s:10:"msg_length";s:2:"60";}s:12:"网站需求";a:4:{s:8:"msg_name";s:12:"网站需求";s:8:"msg_type";s:8:"textarea";s:12:"msg_required";b:0;s:10:"msg_length";s:3:"560";}}',],
        ]);
    }
}
