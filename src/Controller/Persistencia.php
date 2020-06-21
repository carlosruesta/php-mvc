<?php

namespace Alura\Cursos\Controller;

use Alura\Cursos\Entity\Curso;
use Alura\Cursos\Helper\FlashMessageTrait;
use Doctrine\ORM\EntityManagerInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Persistencia implements RequestHandlerInterface
{
    use FlashMessageTrait;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $parametrosBody = $request->getParsedBody();

        $descricao = filter_var($parametrosBody['descricao'], FILTER_SANITIZE_STRING);

        $curso = new Curso();
        $curso->setDescricao($descricao);

        $parametrosQuery = $request->getQueryParams();

        if (isset($parametrosQuery['id'])) {
            $id = filter_var($parametrosQuery['id'], FILTER_VALIDATE_INT);
            if (is_null($id) || $id === false) {
                $this->defineMensagem('danger', 'Id inválido do curso');
                return new Response(403, ['Location' => '/listar-cursos']);
            }
            $curso->setId($id);
            $this->entityManager->merge($curso);
            $mensagem = 'Curso atualizado com sucesso';
        } else {
            $this->entityManager->persist($curso);
            $mensagem = 'Curso inserido com sucesso';
        }

        $this->entityManager->flush();

        $tipo = 'success';
        $this->defineMensagem($tipo, $mensagem);

        return new Response(302, ['Location' => '/listar-cursos']);
    }
}