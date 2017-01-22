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
            [
                'name'   => 'default',
                'config' => json_encode([
                    '姓名'     =>
                        [
                            'msg_name'     => '姓名',
                            'msg_type'     => 'text',
                            'msg_required' => true,
                            'msg_length'   => '20',
                        ],
                    '性别'     =>
                        [
                            'msg_option'   =>
                                [
                                    0 => '男',
                                    1 => '女',
                                    2 => '未知',
                                ],
                            'msg_name'     => '性别',
                            'msg_type'     => 'radio',
                            'msg_required' => false,
                            'msg_length'   => '20',
                        ],
                    '爱好'     =>
                        [
                            'msg_option'   =>
                                [
                                    0 => 'test1',
                                    1 => 'test2',
                                    2 => 'test3',
                                ],
                            'msg_name'     => '爱好',
                            'msg_type'     => 'checkbox',
                            'msg_required' => false,
                            'msg_length'   => '20',
                        ],
                    '联系电话'   =>
                        [
                            'msg_name'     => '联系电话',
                            'msg_type'     => 'text',
                            'msg_required' => true,
                            'msg_length'   => '20',
                        ],
                    '公司名称'   =>
                        [
                            'msg_name'     => '公司名称',
                            'msg_type'     => 'text',
                            'msg_required' => true,
                            'msg_length'   => '20',
                        ],
                    '公司地址'   =>
                        [
                            'msg_name'     => '公司地址',
                            'msg_type'     => 'text',
                            'msg_required' => true,
                            'msg_length'   => '20',
                        ],
                    'E-mail' =>
                        [
                            'msg_name'     => 'E-mail',
                            'msg_type'     => 'text',
                            'msg_required' => false,
                            'msg_length'   => '60',
                        ],
                    '网站需求'   =>
                        [
                            'msg_name'     => '网站需求',
                            'msg_type'     => 'textarea',
                            'msg_required' => false,
                            'msg_length'   => '560',
                        ],
                ]),
            ],
        ]);
    }
}
