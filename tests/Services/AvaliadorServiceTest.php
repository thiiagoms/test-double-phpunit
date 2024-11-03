<?php

declare(strict_types=1);

namespace Tests\Services;

use PHPUnit\Framework\TestCase;
use Thiiagoms\Model\Lance;
use Thiiagoms\Model\Leilao;
use Thiiagoms\Model\Usuario;
use Thiiagoms\Services\AvaliadorService;

class AvaliadorServiceTest extends TestCase
{
    private AvaliadorService $avaliadorService;

    protected function setUp(): void
    {
        $this->avaliadorService = new AvaliadorService();
    }

    public function leilaoComLancesEmOrdemCrescente(): array
    {
        $leilao = new Leilao('Fiat 147 0KM');
        $joao = new Usuario('João');
        $maria = new Usuario('Maria');
        $ana = new Usuario('Ana');

        $leilao->recebeLance(new Lance($joao, 1000));
        $leilao->recebeLance(new Lance($maria, 1500));
        $leilao->recebeLance(new Lance($ana, 2000));

        return [
            [$leilao]
        ];
    }

    public function leilaoComLancesEmOrdemDecrescente(): array
    {
        $leilao = new Leilao('Fiat 147 0KM');
        $joao = new Usuario('João');
        $maria = new Usuario('Maria');
        $ana = new Usuario('Ana');

        $leilao->recebeLance(new Lance($ana, 2000));
        $leilao->recebeLance(new Lance($maria, 1500));
        $leilao->recebeLance(new Lance($joao, 1000));

        return [
            [$leilao]
        ];
    }

    public function leilaoComLancesEmOrdemAleatoria(): array
    {
        $leilao = new Leilao('Fiat 147 0KM');
        $joao = new Usuario('João');
        $maria = new Usuario('Maria');
        $ana = new Usuario('Ana');

        $leilao->recebeLance(new Lance($maria, 1500));
        $leilao->recebeLance(new Lance($ana, 2000));
        $leilao->recebeLance(new Lance($joao, 1000));

        return [
            [$leilao]
        ];
    }

    /**
     * @dataProvider leilaoComLancesEmOrdemAleatoria
     * @dataProvider leilaoComLancesEmOrdemDecrescente
     * @dataProvider leilaoComLancesEmOrdemCrescente
     */
    public function testAvaliadorDeveAcharMaiorValor(Leilao $leilao): void
    {
        $this->avaliadorService->avalia($leilao);

        $this->assertEquals(2000, $this->avaliadorService->getMaiorValor());
    }

    /**
     * @dataProvider leilaoComLancesEmOrdemAleatoria
     * @dataProvider leilaoComLancesEmOrdemDecrescente
     * @dataProvider leilaoComLancesEmOrdemCrescente
     */
    public function testAvaliadorDeveAcharMenorValor(Leilao $leilao): void
    {
        $this->avaliadorService->avalia($leilao);

        $this->assertEquals(1000, $this->avaliadorService->getMenorValor());
    }

    /**
     * @dataProvider leilaoComLancesEmOrdemAleatoria
     * @dataProvider leilaoComLancesEmOrdemDecrescente
     * @dataProvider leilaoComLancesEmOrdemCrescente
     */
    public function testAvaliadorDeveOrdenarOs3Lances(Leilao $leilao): void
    {
        $this->avaliadorService->avalia($leilao);

        $lances = $this->avaliadorService->getTresMaioresLances();

        $this->assertCount(3, $lances);
        $this->assertEquals(2000, $lances[0]->getValor());
        $this->assertEquals(1500, $lances[1]->getValor());
        $this->assertEquals(1000, $lances[2]->getValor());
    }

    public function testAvaliadorDeveRetornarOsMaioresLancesDisponiveis(): void
    {
        $leilao = new Leilao('Fiat 147 0KM');

        $leilao->recebeLance(new Lance(new Usuario('João'), 1000));
        $leilao->recebeLance(new Lance(new Usuario('Maria'), 1500));

        $this->avaliadorService->avalia($leilao);

        static::assertCount(2, $this->avaliadorService->getTresMaioresLances());
    }
}