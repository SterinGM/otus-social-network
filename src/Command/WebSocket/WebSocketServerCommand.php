<?php

namespace App\Command\WebSocket;

use App\Message\Post\PostCreatedMessage;
use App\WebSocket\WebSocketServer;
use App\WebSocket\WebSocketService;
use Exception;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpReceiver;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\Connection;

#[AsCommand(
    name: 'app:websocket:serve',
    description: 'Starts the WebSocket server for real-time post notifications',
    help: 'This command starts a WebSocket server that handles real-time notifications for new posts'
)]
class WebSocketServerCommand extends Command
{
    private WebSocketService $webSocketService;
    private WebSocketServer $webSocketServer;
    private int $port;
    private string $dsn;

    public function __construct(WebSocketService $webSocketService, WebSocketServer $webSocketServer, int $port = 8090, string $dsn = '')
    {
        $this->webSocketService = $webSocketService;
        $this->webSocketServer = $webSocketServer;
        $this->port = $port;
        $this->dsn = $dsn;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('🚀 Starting Symfony WebSocket Server');
        $io->text([
            "Port: {$this->port}",
            "PID: " . getmypid(),
            "Time: " . date('Y-m-d H:i:s')
        ]);

        try {
            $server = IoServer::factory(
                new HttpServer(
                    new WsServer($this->webSocketServer)
                ),
                $this->port
            );

            $io->success("WebSocket server running on port {$this->port}");
            $io->note('Press Ctrl+C to stop the server');
            $io->text('Use http://localhost:8089/websocket-client.html to test');

            $receiver = new AmqpReceiver(
                Connection::fromDsn($this->dsn)
            );

            $server->loop->addPeriodicTimer(1, function () use ($io, $receiver) {
                try {
                    $data = $receiver->getFromQueues(['user_posts_queue']);

                    foreach ($data as $envelop) {
                        $message = $envelop->getMessage();

                        if ($message instanceof PostCreatedMessage) {
                            $this->webSocketService->notifyUsersAboutPost($message);
                            $receiver->ack($envelop);
                        }
                    }
                } catch (Exception) {}
            });

            $server->run();

        } catch (Exception $e) {
            $io->error("Failed to start WebSocket server: " . $e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}