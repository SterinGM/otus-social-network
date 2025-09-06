<?php

namespace App\Command;

use App\WebSocket\WebSocketService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:websocket:status',
    description: 'Shows WebSocket server status and connections',
    help: 'This command shows current WebSocket server status and connected users'
)]
class WebSocketStatusCommand extends Command
{
    private WebSocketService $webSocketService;

    public function __construct(WebSocketService $webSocketService)
    {
        $this->webSocketService = $webSocketService;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('📊 WebSocket Server Status');

        $stats = $this->webSocketService->getStats();

        $io->table(
            ['Metric', 'Value'],
            [
                ['Total Connections', $stats['total_connections']],
                ['Authenticated Users', $stats['authenticated_users']],
                ['Total User Connections', $stats['total_user_connections']]
            ]
        );

        if (!empty($stats['users'])) {
            $io->section('Connected Users');
            $io->listing($stats['users']);
        } else {
            $io->note('No authenticated users connected');
        }

        return Command::SUCCESS;
    }
}