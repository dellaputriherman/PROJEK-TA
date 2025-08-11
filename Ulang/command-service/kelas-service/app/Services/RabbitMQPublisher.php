<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Support\Facades\Log;

class RabbitMQPublisher
{
    protected $connection;
    protected $channel;
    protected $queue;

    public function __construct()
    {
        $host = env('RABBITMQ_HOST', 'rabbitmq');
        $port = env('RABBITMQ_PORT', 5672);
        $user = env('RABBITMQ_USER', 'guest');
        $password = env('RABBITMQ_PASSWORD', 'guest');
        $this->queue = env('RABBITMQ_QUEUE', 'data_sync_queue');

        try {
            $this->connection = new AMQPStreamConnection($host, $port, $user, $password);
            $this->channel = $this->connection->channel();

            $this->channel->queue_declare($this->queue, false, true, false, false);

            Log::info("RabbitMQ connection established to {$host}:{$port}, queue: {$this->queue}");
        } catch (\Exception $e) {
            Log::error("Gagal terhubung ke RabbitMQ: " . $e->getMessage());
        }
    }

    public function publish($data, $table, $action)
    {
        if (!$this->channel) {
            Log::error("Koneksi RabbitMQ tidak tersedia, tidak bisa mengirim pesan.");
            return;
        }

        $messageData = [
            'table' => $table,
            'action' => $action, // create | update | delete
            'payload' => $data
        ];

        $msg = new AMQPMessage(json_encode($messageData), [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ]);

        $this->channel->basic_publish($msg, '', $this->queue);

        Log::info("Pesan berhasil dikirim ke RabbitMQ untuk table: {$table}, action: {$action}");
    }

    public function __destruct()
    {
        try {
            if ($this->channel) {
                $this->channel->close();
            }
            if ($this->connection) {
                $this->connection->close();
            }
        } catch (\Exception $e) {
            Log::error("Gagal menutup koneksi RabbitMQ: " . $e->getMessage());
        }
    }
}
