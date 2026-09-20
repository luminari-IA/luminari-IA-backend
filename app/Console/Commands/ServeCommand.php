<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\ServeCommand as BaseServeCommand;

class ServeCommand extends BaseServeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Serve the application on the PHP development server (Luminary Enhanced)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $host = $this->host();
        $port = $this->port();

        $this->line("");
        $this->line(" <fg=yellow;options=bold>✨ LUMINARY BACKEND ✨</>");
        $this->line(" <fg=gray>========================================================</>");
        $this->line(" 🚀 <fg=white>Servidor Local:</>    <fg=cyan;options=bold>http://{$host}:{$port}</>");
        $this->line(" 📚 <fg=white>Documentación API:</> <fg=green;options=bold>http://{$host}:{$port}/api/documentation</>");
        $this->line(" <fg=gray>========================================================</>");
        $this->line("");

        return parent::handle();
    }
}
