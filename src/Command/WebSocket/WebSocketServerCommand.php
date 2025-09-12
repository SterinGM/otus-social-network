<?php

namespace App\Command\WebSocket;

use App\WebSocket\Consumer\PostConsumer;
use App\WebSocket\WebSocketServer;
use Exception;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:websocket:serve',
    description: 'Запускает WebSocket сервер для уведомлений о новых постах в реальном времени',
    help: 'Эта команда запускает WebSocket сервер, который обрабатывает уведомления о новых постах в реальном времени'
)]
class WebSocketServerCommand extends Command
{
    private WebSocketServer $webSocketServer;
    private PostConsumer $postNotificationConsumer;
    private int $port;

    public function __construct(
        WebSocketServer $webSocketServer,
        PostConsumer    $postNotificationConsumer,
        int             $port = 8090
    ) {
        $this->webSocketServer = $webSocketServer;
        $this->postNotificationConsumer = $postNotificationConsumer;
        $this->port = $port;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->displayServerInfo($io);

        try {
            $server = $this->createWebSocketServer();
            $this->displaySuccessMessage($io);
            $this->setupMessageReceiver($server);
            $server->run();

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error(sprintf('Не удалось запустить WebSocket сервер: %s', $e->getMessage()));

            return Command::FAILURE;
        }
    }

    private function displayServerInfo(SymfonyStyle $io): void
    {
        $io->title('🚀 Запуск WebSocket сервера Symfony');
        $io->text([
            sprintf('Порт: %d', $this->port),
            sprintf('PID: %s', getmypid()),
            sprintf('Время: %s', date('Y-m-d H:i:s'))
        ]);
    }

    private function createWebSocketServer(): IoServer
    {
        return IoServer::factory(
            new HttpServer(
                new WsServer($this->webSocketServer)
            ),
            $this->port
        );
    }

    private function displaySuccessMessage(SymfonyStyle $io): void
    {
        $io->success(sprintf('WebSocket сервер запущен на порту %d', $this->port));
        $io->note('Нажмите Ctrl+C для остановки сервера');
        $io->text('Используйте http://localhost:8089/websocket-client.html для тестирования');
    }

    private function setupMessageReceiver(IoServer $server): void
    {
        $server->loop->addPeriodicTimer(1, function () {
            $this->postNotificationConsumer->processMessages();
        });
    }
}