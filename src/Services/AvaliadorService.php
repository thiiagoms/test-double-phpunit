<?php

declare(strict_types=1);

namespace Thiiagoms\Services;

use Thiiagoms\Model\Lance;
use Thiiagoms\Model\Leilao;

class AvaliadorService
{
    private float $menorValor = INF;
    
    private float $maiorValor = 0;

    private array $maiores;

    public function avalia(Leilao $leilao): void
    {
        $leilao->finaliza();

        foreach ($leilao->getLances() as $lance) {
            if ($lance->getValor() > $this->maiorValor) {
                $this->maiorValor = $lance->getValor();
            }

            if ($lance->getValor() < $this->menorValor) {
                $this->menorValor = $lance->getValor();
            }

            $this->maiores = $this->avaliaTresMaioresLances($leilao);
        }
    }

    public function getMenorValor(): float
    {
        return $this->menorValor;
    }

    public function getMaiorValor(): float
    {
        return $this->maiorValor;
    }

    /**
     * @return Lance[]
     */
    public function getTresMaioresLances(): array
    {
        return $this->maiores;
    }

    /**
     * @param Leilao $leilao
     * @return Lance[]|array
     */
    private function avaliaTresMaioresLances(Leilao $leilao):  array
    {
        $lances = $leilao->getLances();
        usort($lances, function (Lance $lance1, Lance $lance2) {
            return $lance2->getValor() - $lance1->getValor();
        });

        return array_slice($lances, 0, 3);
    }
}
