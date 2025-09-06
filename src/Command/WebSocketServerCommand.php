<?php

namespace App\Command;

use App\WebSocket\WebSocketServer;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

#[AsCommand(
    name: 'app:websocket:serve',
    description: 'Starts the WebSocket server for real-time post notifications',
    help: 'This command starts a WebSocket server that handles real-time notifications for new posts'
)]
class WebSocketServerCommand extends Command
{
    private WebSocketServer $webSocketServer;
    private int $port;

    public function __construct(WebSocketServer $webSocketServer, int $port = 3001)
    {
        $this->webSocketServer = $webSocketServer;
        $this->port = $port;

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

            $server->run();

        } catch (Exception $e) {
            $io->error("Failed to start WebSocket server: " . $e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}