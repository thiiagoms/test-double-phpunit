<?php

declare(strict_types=1);

namespace Tests\Integration\DAO;

use PDO;
use PHPUnit\Framework\TestCase;
use Thiiagoms\DAO\LeilaoDAO;
use Thiiagoms\Model\Leilao;

class LeilaoDAOTest extends TestCase
{
    private static PDO $connection;

    public static function setUpBeforeClass(): void
    {
        self::$connection = new PDO('sqlite::memory:');

        $sql = 'CREATE TABLE leiloes (
            id INTEGER PRIMARY KEY,
            descricao TEXT,
            dataInicio TEXT,
            finalizado BOOLEAN
        )';

        self::$connection->exec($sql);
    }

    protected function setUp(): void
    {
        self::$connection->beginTransaction();
    }

    public function leiloesProvider(): array
    {
        $leilaoNaoFinalizado = new Leilao('Variante 0km');
        $leilaoFinalizado = new Leilao('Fiat 147 0KM');
        $leilaoFinalizado->finaliza();

        return [
            [
                [$leilaoNaoFinalizado, $leilaoFinalizado]
            ]
        ];
    }

    /**
     * @dataProvider leiloesProvider
     *
     * @return void
     */
    public function testBuscaLeiloesNaoFinalizados(array $leiloes): void
    {
        $leilaoDAO = new LeilaoDAO(self::$connection);

        foreach ($leiloes as $leilao) {
            $leilaoDAO->salva($leilao);
        }

        $leiloesNaoFinalizados = $leilaoDAO->recuperarNaoFinalizados();

        $this->assertCount(1, $leiloesNaoFinalizados);
        $this->assertContainsOnlyInstancesOf(Leilao::class, $leiloesNaoFinalizados);
        $this->assertSame('Variante 0km', $leiloesNaoFinalizados[0]->recuperarDescricao());
    }

    /**
     * @dataProvider leiloesProvider
     *
     * @return void
     */
    public function testBuscaLeiloesFinalizados(array $leiloes): void
    {
        $leilaoDAO = new LeilaoDAO(self::$connection);

        foreach ($leiloes as $leilao) {
            $leilaoDAO->salva($leilao);
        }

        $leiloesFinalizados = $leilaoDAO->recuperarFinalizados();

        $this->assertCount(1, $leiloesFinalizados);
        $this->assertContainsOnlyInstancesOf(Leilao::class, $leiloesFinalizados);
        $this->assertSame('Fiat 147 0KM', $leiloesFinalizados[0]->recuperarDescricao());
    }

    public function testAtualizarLeilaoDeveAlterarStatus(): void
    {
        // Arrange
        $leilao = new Leilao('Brasilia Amarela');

        $leilaoDAO = new LeilaoDAO(self::$connection);
        $leilao = $leilaoDAO->salva($leilao);

        // Teste intermediário
        $leilaoNaoFinalizado = $leilaoDAO->recuperarNaoFinalizados();

        $this->assertCount(1, $leilaoNaoFinalizado);
        $this->assertContainsOnlyInstancesOf(Leilao::class, $leilaoNaoFinalizado);
        $this->assertSame($leilao->recuperarId(), $leilaoNaoFinalizado[0]->recuperarId());
        $this->assertSame('Brasilia Amarela', $leilaoNaoFinalizado[0]->recuperarDescricao());
        $this->assertFalse($leilaoNaoFinalizado[0]->estaFinalizado());

        // Act
        $leilao->finaliza();
        $leilaoDAO->atualiza($leilao);

        // Assert
        $leilaoFinalizado = $leilaoDAO->recuperarFinalizados();

        $this->assertCount(1, $leilaoFinalizado);
        $this->assertContainsOnlyInstancesOf(Leilao::class, $leilaoFinalizado);
        $this->assertSame($leilao->recuperarId(), $leilaoFinalizado[0]->recuperarId());
        $this->assertSame('Brasilia Amarela', $leilaoFinalizado[0]->recuperarDescricao());
        $this->assertTrue($leilaoFinalizado[0]->estaFinalizado());
    }

    protected function tearDown(): void
    {
        self::$connection->rollBack();
    }
}
