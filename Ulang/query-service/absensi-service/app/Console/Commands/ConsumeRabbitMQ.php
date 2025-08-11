<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ConsumeRabbitMQ extends Command
{
    protected $signature = 'rabbitmq:consume';
    protected $description = 'Consume messages from RabbitMQ';

    public function handle()
    {
        $connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare('data_sync_queue', false, true, false, false);

        $callback = function (AMQPMessage $msg) {
            $this->info('Received: ' . $msg->body);

            $data = json_decode($msg->body, true);

            try {
                DB::connection('mongodb')
                    ->collection($data['table'])
                    ->insert($data['payload']);

                $this->info('Data berhasil disimpan ke MongoDB.');
            } catch (\Exception $e) {
                $this->error('Gagal menyimpan ke MongoDB: ' . $e->getMessage());
            }
        };

        $channel->basic_consume('data_sync_queue', '', false, true, false, false, $callback);

        $this->info(" [*] Waiting for messages. To exit press CTRL+C");

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
