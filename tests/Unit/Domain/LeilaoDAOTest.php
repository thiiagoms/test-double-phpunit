<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use DomainException;
use PHPUnit\Framework\TestCase;
use Thiiagoms\Model\Lance;
use Thiiagoms\Model\Leilao;
use Thiiagoms\Model\Usuario;

class LeilaoDAOTest extends TestCase
{
    public function testProporLanceEmLeilaoFinalizadoDeveLancarExcecao(): void
    {
        $leilao = new Leilao('Fiat 147 0KM');
        $leilao->finaliza();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Este leilão já está finalizado');


        $leilao->recebeLance(new Lance(new Usuario(''), 1000));
    }

    public function dadosParaProporLances(): array
    {
        $usuario1 = new Usuario('Usuário 1');
        $usuario2 = new Usuario('Usuário 2');

        return [
            [1, [new Lance($usuario1, 1000)]],
            [2, [new Lance($usuario1, 1000), new Lance($usuario2, 2000)]],
        ];
    }

    /**
     * @dataProvider dadosParaProporLances
     */
    public function testProporLancesEmLeilaoDeveFuncionar(int $qtdEsperado, array $lances): void
    {
        $leilao = new Leilao('Fiat 147 0KM');

        foreach ($lances as $lance) {
            $leilao->recebeLance($lance);
        }

        static::assertCount($qtdEsperado, $leilao->getLances());
    }

    public function testMesmoUsuarioNaoPodeProporDoisLancesSeguidos(): void
    {
        $usuario = new Usuario('Ganancioso');
        $leilao = new Leilao('Objeto inútil');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Usuário já deu o último lance');

        $leilao->recebeLance(new Lance($usuario, 1000));
        $leilao->recebeLance(new Lance($usuario, 1100));
    }
}