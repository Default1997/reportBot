<?php

namespace app\controllers;

use Yii;
use yii\base\ErrorException;

class BotController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;
    
    public function actionIndex()
    {   
        try {
        $telegram = Yii::$app->telegram;
        $res = $telegram->setWebhook([
            'url' => 'https://f538-85-95-189-148.eu.ngrok.io/bot/index',
        ]);

        
        // if ($data['callback_query']['data'] === NULL) {
            $data['callback_query']['data'] = 0;
        // }
        

        $data = json_decode(file_get_contents('php://input'), true);
        



        if ($data['message']['text'] === 'z') {
            // $data['callback_query']['data'] = 'fix_task';
            Yii::$app->telegram->sendMessage([
                'chat_id' => $telegram->input->message->chat->id,
                'text' => 'qqq'
            ]);
            $telegramUserId = Yii::$app->telegram->input->message->chat->id;
        }else{
            $telegramUserId = $data['callback_query']['message']['chat']['id'];
        }

        file_put_contents(__DIR__ . '/log.txt', $data . print_r($data, true).'\r\n', FILE_APPEND);
        // print_r($data);die;


        $telegramUserId = $data['callback_query']['message']['chat']['id'];//Yii::$app->telegram->input->message->chat->id;

        file_put_contents(__DIR__ . '/test.txt', $data['callback_query']['data'] . '-', FILE_APPEND);
        
        
        // Yii::$app->telegram->sendMessage([
        //     'chat_id' => $telegram->input->message->chat->id,
        //     'text' => $data['data']
        // ]);

        switch ($data['callback_query']['data']) {
            case 'start':
                //зафиксировать время начала дня в кеше
                $workDayTimeStart = time();
                
                $cache = Yii::$app->cache;
                $cache->set('workDayTimeStart'.$data['callback_query']['message']['chat']['id'], $workDayTimeStart, date_create('tomorrow')->getTimestamp() - time());
            
                Yii::$app->telegram->sendMessage([
                    'chat_id' => $telegramUserId,
                    'text' => 'Московское время - '.date('H:i', time()).'. Начался рабочий день, успехов!',
                    'reply_markup' => json_encode([
                        'inline_keyboard'=>[
                            [
                                ['text'=>'Составить отчет о рабочем дне','callback_data'=> 'finish'],
                                ['text'=>'Ушел на перерыв','callback_data'=> 'breakStart']
                            ],
                            [
                                ['text'=>'Сколько прошло рабочего времени?','callback_data'=> 'check_time']
                            ],
                            [
                                ['text'=>'Зафиксировать выполненную задачу','callback_data'=> 'fix_task']
                            ]
                        ]
                    ]),
                ]);
                break;
            case 'finish':
                $workDayTimeFinish = time();
                $cache = Yii::$app->cache;
                // $cache->set('workDayTimeFinish'.$data['callback_query']['message']['chat']['id'], $workDayTimeFinish, date_create('tomorrow')->getTimestamp() - time());
                $workDayTimeStart = $cache->get('workDayTimeStart'.$data['callback_query']['message']['chat']['id']);

                $time = ($workDayTimeFinish - $workDayTimeStart)/3600;

                $tasks = $cache->get('tasks'.$data['callback_query']['message']['chat']['id']);
                Yii::$app->telegram->sendMessage([
                    'chat_id' => $telegramUserId,
                    'text' => 'Московское время - '.date('H:i', time()).'.'. PHP_EOL .'Рабочий день закончился! @'. $data['callback_query']['message']['chat']['username'] .PHP_EOL .'Ты проработал - '. round($time, 2) .'часа и зафиксировал задачи: '. PHP_EOL. print_r($tasks, true),
                ]);

                $chatId = '-832164570';
                Yii::$app->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'Московское время - '.date('H:i', time()).'.'. PHP_EOL .'Рабочий день закончился! @'. $data['callback_query']['message']['chat']['username'] .PHP_EOL .'Ты проработал - '. round($time, 2) .'часа и зафиксировал задачи: '. PHP_EOL. print_r($tasks, true),
                ]);
                //зафиксировать конец рабочего дня, посчитать время, составить отчет
                break;
            case 'check_time':
                $cache = Yii::$app->cache;
                $workDayTimeStart = $cache->get('workDayTimeStart'.$data['callback_query']['message']['chat']['id']);
            
                $now = time();
                $time = $now-$workDayTimeStart;//нужно еще отнять время проведенное в перерыве
                $time = $time/3600;
                
                 Yii::$app->telegram->sendMessage([
                    'chat_id' => $telegramUserId,
                    'text' => 'Ты проработал '. round($time, 2) .' часа'
                ]);
                break;

            case 'fix_task':
                $cache = Yii::$app->cache;
                $cache->set('isTask'.$data['callback_query']['message']['chat']['id'], 'yes', date_create('tomorrow')->getTimestamp() - time());
            
                    Yii::$app->telegram->sendMessage([
                        'chat_id' => $telegramUserId,
                        'text' => 'Введи описание сделанной задачи!'
                    ]);
                break;
            case 'breakStart':
                $cache = Yii::$app->cache;
                $breakStart = time();
                $cache->set('breakStart'.$data['callback_query']['message']['chat']['id'], $breakStart, date_create('tomorrow')->getTimestamp() - time());

                Yii::$app->telegram->sendMessage([
                    'chat_id' => $telegramUserId,
                    'text' => 'Московское время - '.date('H:i',time()).'. Начался перерыв, набирайся сил и возвращайся!',
                    'reply_markup' => json_encode([
                        'inline_keyboard'=>[
                            [
                                ['text'=>'Вернулсяся с перерыва','callback_data'=> 'breakFinish']
                            ],
                            [
                                ['text'=>'Сколько прошло рабочего времени?','callback_data'=> 'check_time']
                            ],
                            [
                                ['text'=>'Зафиксировать выполненную задачу','callback_data'=> 'fix_task']
                            ]
                        ]
                    ]),
                ]);
                //зафиксировать начало перерыва
                break;
            case 'breakFinish':
                $cache = Yii::$app->cache;
                $breakFinish = time();

                $breakStart = $cache->get('breakStart'.$data['callback_query']['message']['chat']['id']);
                $breakTimeSum = ($breakFinish - $breakStart)/3600;
                $breakTimeSum = round($breakTimeSum, 2);

                $oldData = $cache->get('breakTimeSum'.$data['callback_query']['message']['chat']['id']);
                $cache->set('breakTimeSum'.$data['callback_query']['message']['chat']['id'], $breakTimeSum+$oldData, date_create('tomorrow')->getTimestamp() - time());
            

                Yii::$app->telegram->sendMessage([
                    'chat_id' => $telegramUserId,
                    'text' => 'Московское время - '.date('H:i',time()).'. Ты отдохнул, это хорошо! Давай приступим к работе дальше ;)',
                    'reply_markup' => json_encode([
                        'inline_keyboard'=>[
                            [
                                ['text'=>'Составить отчет о рабочем дне','callback_data'=> 'finish'],
                                ['text'=>'Ушел на перерыв','callback_data'=> 'breakStart']
                            ],
                            [
                                ['text'=>'Сколько прошло рабочего времени?','callback_data'=> 'check_time']
                            ],
                            [
                                ['text'=>'Зафиксировать выполненную задачу','callback_data'=> 'fix_task']
                            ]
                        ]
                    ]),
                ]);
                //зафиксировать конец перерыва, посчитать время в перерыве за этот перерыв (перерывов может быть больше одного)
                break;
            default:
                if ($data['message']['chat']['type'] === 'group') {
                    break;
                }

                $cache = Yii::$app->cache;
                $workDayTimeStart = $cache->get('workDayTimeStart'.$data['message']['chat']['id']);
                $isTask = $cache->get('isTask'.$data['message']['chat']['id']);

                if ($isTask === 'yes') {
                    //записать задачу
                    // $tasks = array();
                    $task = $data['message']['text'];

                    $tasks = $cache->get('tasks'.$data['message']['chat']['id']);

                    $tasks [] = $task;
                    // $tasks .= $data['message']['text'];
                    // array_push($tasks, $task);

                    $cache->set('tasks'.$data['message']['chat']['id'], $tasks, date_create('tomorrow')->getTimestamp() - time());
                    
                    file_put_contents(__DIR__ . '/tasks.txt', print_r($tasks, true) . '-', FILE_APPEND);
        

                    Yii::$app->telegram->sendMessage([
                        'chat_id' => $telegram->input->message->chat->id,
                        'text' => 'Задача зафиксирована: '. $task. ' Полный список задач: ' . print_r($tasks, true)
                    ]);

                    $cache->set('isTask'.$data['message']['chat']['id'], 'no', date_create('tomorrow')->getTimestamp() - time());
                }else{
                    if ($workDayTimeStart == 0) {
                        Yii::$app->telegram->sendMessage([
                            'chat_id' => $telegram->input->message->chat->id,
                            'text' => 'Выберите:',
                            'reply_markup' => json_encode([
                                'inline_keyboard'=>[
                                    [
                                        ['text'=>'Начать рабочий день','callback_data'=> 'start']
                                    ]
                                ]
                            ]),
                        ]);
                        break;
                    }else{
                        Yii::$app->telegram->sendMessage([
                            'chat_id' => $telegram->input->message->chat->id,
                            'text' => 'Чем могу помочь?',
                            'reply_markup' => json_encode([
                                'inline_keyboard'=>[
                                    [
                                        ['text'=>'Составить отчет о рабочем дне','callback_data'=> 'finish'],
                                        ['text'=>'Ушел на перерыв','callback_data'=> 'breakStart']
                                    ],
                                    [
                                        ['text'=>'Сколько прошло рабочего времени?','callback_data'=> 'check_time']
                                    ],
                                    [
                                        ['text'=>'Зафиксировать выполненную задачу','callback_data'=> 'fix_task']
                                    ]
                                ]
                            ]),
                        ]);
                    }
                }
        }

    } catch (ErrorException $e) {
        file_put_contents(__DIR__ . '/error.txt', $e . '-', FILE_APPEND);     
    }

    }

}
