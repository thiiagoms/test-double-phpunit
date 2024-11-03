<?php

declare(strict_types=1);

namespace Thiiagoms\Services;

use Thiiagoms\Model\Leilao;

class EnviadorEmailService
{
    public function notificarTerminoLeilao(Leilao $leilao): void
    {
        $sucesso = mail(
            'usuario@email.com',
            'Leilão finalizado',
            "O leilão para {$leilao->recuperarDescricao()} foi finalizado"
        );

        if (!$sucesso) {
            throw new \DomainException('Erro ao enviar e-mail');
        }
    }
}