<?php

declare(strict_types=1);

namespace Thiiagoms\Services;

use Thiiagoms\DAO\LeilaoDAO;

class EncerradorService
{
    private LeilaoDAO $leilaoDAO;
    private EnviadorEmailService $enviadorEmailService;

    public function __construct(LeilaoDao $leilaoDAO, EnviadorEmailService $enviadorEmailService)
    {
        $this->leilaoDAO = $leilaoDAO;
        $this->enviadorEmailService = $enviadorEmailService;
    }

    public function encerra(): void
    {
        $leiloes = $this->leilaoDAO->recuperarNaoFinalizados();

        foreach ($leiloes as $leilao) {

            if ($leilao->temMaisDeUmaSemana()) {

                try {

                    $leilao->finaliza();
                    $this->leilaoDAO->atualiza($leilao);
                    $this->enviadorEmailService->notificarTerminoLeilao($leilao);
                } catch (\DomainException $e) {
                    error_log($e->getMessage());
                }
            }
        }
    }
}
