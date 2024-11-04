<?php

declare(strict_types=1);

namespace Tests\Services;

use DomainException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Thiiagoms\DAO\LeilaoDAO;
use Thiiagoms\Model\Leilao;
use Thiiagoms\Services\EncerradorService;
use Thiiagoms\Services\EnviadorEmailService;

class EncerradorServiceTest extends TestCase
{
    private EncerradorService $encerradorService;

    private MockObject $enviadorEmailService;

    private Leilao $leilaoFiat147;

    private Leilao $leilaoVariante;

    protected function setUp(): void
    {
        $this->leilaoFiat147 = new Leilao('Fiat 147 0Km', new \DateTimeImmutable('8 days ago'));
        $this->leilaoVariante = new Leilao('Variant 1972 0Km', new \DateTimeImmutable('10 days ago'));

        /** @var LeilaoDAO|MockObject $leilaoDAO */
        $leilaoDAO = $this->createMock(LeilaoDAO::class);

        $leilaoDAO->method('recuperarNaoFinalizados')
            ->willReturn([$this->leilaoFiat147, $this->leilaoVariante]);

        $leilaoDAO->expects($this->exactly(2))
            ->method('atualiza')
            ->withConsecutive([$this->leilaoFiat147], [$this->leilaoVariante]);

        /** @var EnviadorEmailService|MockObject $enviadorEmailService */
        $this->enviadorEmailService = $this->createMock(EnviadorEmailService::class);

        $this->encerradorService = new EncerradorService($leilaoDAO, $this->enviadorEmailService);
    }

    public function testLeiloesComMaisDeUmaSemanaDevemSerEncerrados(): void
    {
        $this->encerradorService->encerra();

        $leiloes = [$this->leilaoFiat147, $this->leilaoVariante];

        $this->assertCount(2, $leiloes);
        $this->assertTrue($leiloes[0]->estaFinalizado());
        $this->assertTrue($leiloes[1]->estaFinalizado());
    }

    public function testDeveContinuarOProcessamentoAoEncontrarErroAoEnviarEmail(): void
    {
        $this->enviadorEmailService->expects($this->exactly(2))
            ->method('notificarTerminoLeilao')
            ->willThrowException(new DomainException('Erro ao enviar e-mail'));

        $this->encerradorService->encerra();
    }

    public function testDeveEnviarLeilaoPorEmailApenasQuandoLeilaoForFinalizado(): void
    {
        $this->enviadorEmailService->expects($this->exactly(2))
            ->method('notificarTerminoLeilao')
            ->willReturnCallback(fn (Leilao $leilao) => $this->assertTrue($leilao->estaFinalizado()));

        $this->encerradorService->encerra();
    }
}
